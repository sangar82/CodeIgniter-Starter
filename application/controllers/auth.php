<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('url');
	}

	//redirect if needed, otherwise display the user list
	function index()
	{
		$this->data['title'] = lang('web_list_user');
		
		if (!$this->ion_auth->logged_in())
		{
			//set message 
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_not_logged') ) );
			
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		}

		//set the flash data error message if there is one
		$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

		//list the users
		$this->data['users'] = $this->ion_auth->users()->result();
		foreach ($this->data['users'] as $k => $user)
		{
			$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
		}

		
		$layout['body'] = $this->load->view('auth/index', $this->data, TRUE);
		$this->load->view('layouts/backend', $layout);
		
	}

	//log the user in
	function login()
	{
		$this->data['title'] = "Login";

		//validate form input
		$this->form_validation->set_rules('identity', 'lang:web_email', 'required|valid_email|trim|xss_clean');
		$this->form_validation->set_rules('password', 'lang:web_password', 'required|trim|xss_clean');
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

		if ($this->form_validation->run() == true)
		{ //check to see if the user is logging in
			//check for "remember me"
			$remember = (bool) $this->input->post('remember');

			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
			{ //if the login is successful
				//redirect them back to the home page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect($this->config->item('base_url'), 'refresh');
			}
			else
			{ //if the login was un-successful
				//redirect them back to the login page
				$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_login_incorrect') ));
				redirect('auth/login', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
			}
		}
		else
		{  //the user is not logging in so display the login page
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['identity'] = array('name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'value' => $this->form_validation->set_value('identity'),
			);
			$this->data['password'] = array('name' => 'password',
				'id' => 'password',
				'type' => 'password',
			);

			$layout['body'] = $this->load->view('auth/login', $this->data, TRUE);
			$this->load->view('layouts/login', $layout);
		}
	}

	//log the user out
	function logout()
	{
		$this->data['title'] = "Logout";

		//log the user out
		$logout = $this->ion_auth->logout();

		//redirect them back to the page they came from
		redirect('login', 'refresh');
	}

	//change password
	function change_password()
	{
		$this->form_validation->set_rules('old', 'Old password', 'required');
		$this->form_validation->set_rules('new', 'New Password', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', 'Confirm New Password', 'required');

		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login', 'refresh');
		}
		
		$user = $this->ion_auth->user()->row();

		if ($this->form_validation->run() == false)
		{ //display the form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['old_password'] = array(
				'name' => 'old',
				'id'   => 'old',
				'type' => 'password',
			);
			$this->data['new_password'] = array(
				'name' => 'new',
				'id'   => 'new',
				'type' => 'password',
			);
			$this->data['new_password_confirm'] = array(
				'name' => 'new_confirm',
				'id'   => 'new_confirm',
				'type' => 'password',
			);
			$this->data['user_id'] = array(
				'name'  => 'user_id',
				'id'    => 'user_id',
				'type'  => 'hidden',
				'value' => $user->id,
			);

			//render
			$layout['body'] = $this->load->view('auth/change_password', $this->data, TRUE);
			$this->load->view('layouts/login', $layout);
		}
		else
		{
			$identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));

			$change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

			if ($change)
			{ //if the password was successfully changed
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				$this->logout();
			}
			else
			{
				$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => $this->ion_auth->errors()) );
				redirect('auth/change_password', 'refresh');
			}
		}
	}

	//forgot password
	function forgot_password()
	{
		$this->form_validation->set_rules('email', 'lang:web_email', 'required|trim|clean_xss|valid_email');
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

		if ($this->form_validation->run() == false)
		{
			//setup the input
			$this->data['email'] = array('name' => 'email',
				'id' => 'email',
			);
			//set any errors and display the form
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$layout['body'] = $this->load->view('auth/forgot_password', $this->data, TRUE);
			$this->load->view('layouts/login', $layout);
		}
		else
		{
			//run the forgotten password method to email an activation code to the user
			$forgotten = $this->ion_auth->forgotten_password($this->input->post('email'));

			if ($forgotten)
			{ //if there were no errors
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth/login", 'refresh'); //we should display a confirmation page here instead of the login page
			}
			else
			{
				$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => $this->ion_auth->errors()) );
				redirect("auth/forgot_password", 'refresh');
			}
		}
	}

	//reset password - final step for forgotten password
	public function reset_password($code)
	{
		$reset = $this->ion_auth->forgotten_password_complete($code);

		if ($reset)
		{  //if the reset worked then send them to the login page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("auth/login", 'refresh');
		}
		else
		{ //if the reset didnt work then send them back to the forgot password page
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => $this->ion_auth->errors()) );
			redirect("auth/forgot_password", 'refresh');
		}
	}

	//activate the user
	function activate($id, $code=false)
	{
		if ($code !== false)
			$activation = $this->ion_auth->activate($id, $code);
		else if ($this->ion_auth->is_admin())
			$activation = $this->ion_auth->activate($id);

		if ($activation)
		{
			//redirect them to the auth page
			$this->session->set_flashdata('message', $this->ion_auth->messages());

			if (!$this->ion_auth->logged_in())
				redirect("login", 'refresh');
			else
				redirect("auth", 'refresh');
		}
		else
		{
			//redirect them to the forgot password page
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => $this->ion_auth->errors()) );
			redirect("auth/forgot_password", 'refresh');
		}
	}

	//deactivate the user
	function deactivate($id = NULL)
	{
		// no funny business, force to integer
		$id = (int) $id;

		$this->load->library('form_validation');
		$this->form_validation->set_rules('confirm', 'confirmation', 'required');
		$this->form_validation->set_rules('id', 'user ID', 'required|is_natural');

		if ($this->form_validation->run() == FALSE)
		{
			// insert csrf check
			$this->data['csrf'] = $this->_get_csrf_nonce();
			$this->data['user'] = $this->ion_auth->user($id)->row();

			$layout['body'] = $this->load->view('auth/deactivate_user', $this->data, TRUE);
			$this->load->view('layouts/backend', $layout);
		}
		else
		{
			// do we really want to deactivate?
			if ($this->input->post('confirm') == 'yes')
			{
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
				{
					show_404();
				}

				// do we have the right userlevel?
				if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin())
				{
					$this->ion_auth->deactivate($id);
					$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_user_deactivate') ) );
				}
			}

			//redirect them back to the auth page
			redirect('auth', 'refresh');
		}
	}


	function create() 
	{
		//Rules for validation
		$this->_set_rules();

		//create control variables
		$data['title'] = lang('web_add_user');
		$data['updType'] = 'create';
		$data['user'] = getTableColumns('users', true);

		if (!$this->ion_auth->logged_in())
		{
			//set message 
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_not_logged') ) );
			
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		}

		//validate the fields of form
		if ($this->form_validation->run() == FALSE) 
		{			
			//load the view and the layout
			$layout['body'] = $this->load->view('auth/create_user', $data, TRUE);
			$this->load->view('layouts/backend', $layout);
		}
		else
		{

			$username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
			$email = $this->input->post('email');
			$password = $this->input->post('password');

			$additional_data = array(
							'first_name' 	=> 	$this->input->post('first_name'),
							'last_name' 	=> 	$this->input->post('last_name')
			);

			//control that the email is not taken, if it is taken, redirect
			if ($this->ion_auth->email_check($email))
			{
				$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_email_taken') ));
				redirect('auth');
			}

			// run insert model to write data to db
			if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data))
			{
				//watch if there is a email activation
				if ( $this->config->item('email_activation', 'ion_auth') )
				{
					$lang_success = 'web_create_success_act';
				}
				else
				{
					$lang_success = 'web_create_success';	
				}

				$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang($lang_success) ));
				redirect('auth/');
			}
			else
			{
				$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => lang('web_create_failed') ));
				redirect('auth/');
			}	
	  	} 
	}


	function edit($id = FALSE) 
	{
		if (!$this->ion_auth->logged_in())
		{
			//set message 
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_not_logged') ) );
			
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		}

		//Rules for validation
		$this->_set_rules('edit');

		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			//create control variables
			$data['title'] = lang("web_edit_user");
			$data['updType'] = 'edit';

			//get the $id
			$id = ( $this->uri->segment(3) )  ? $this->uri->segment(3) : $this->input->post('id', TRUE);

			//Filter & Sanitize $id
			$id = ($id != 0) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

			//redirect if it´s no correct
			if (!$id){
				$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exist') ) );
				redirect('auth/');
			}

			//search the item to show in edit form
			//$data['user'] = User::find_by_id($id);
			$query = $this->db->get_where('users', array('id' => $id));
			$data['user'] = $query->row(); 
			
			//load the view and the layout
			$layout['body'] = $this->load->view('auth/create_user', $data, TRUE);
			$this->load->view('layouts/backend', $layout);
		}
		else
		{
			$username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
			$email = $this->input->post('email');
			$password = $this->input->post('password');

			$additional_data = array(
							'first_name' 	=> 	$this->input->post('first_name'),
							'last_name' 	=> 	$this->input->post('last_name'),
							'email' 		=> 	$this->input->post('email')
			);

			if ( $this->input->post('password') != '' )
				$additional_data['password']  = 	$this->input->post('password');
		
			//find the item to update
			//$user = Category::find($this->input->post('id', TRUE));

			// run insert model to write data to db
			if ($this->form_validation->run() == true && $this->ion_auth->update( $this->input->post('id'), $additional_data))
			{
				$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_edit_success') ));
				redirect('auth/');
			}
			else
			{
				$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => lang('web_edit_failed') ) );
				redirect('auth/');
				
			}	
	  	} 
	}

	function delete($id = false)
	{
		if (!$this->ion_auth->logged_in())
		{
			//set message 
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_not_logged') ) );
			
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		}

		//filter & Sanitize $id
		$id = ($id != 0) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

		//redirect if it´s no correct
		if (!$id){
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exit') ) );
			redirect('auth/');
		}
		
		$query = $this->db->get_where('users', array('id' => $id));

		//search the item to delete
		if ( ! $query->num_fields() )
		{
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exit') ) );
			redirect('auth/');
		}

		//delete the item
		if ( $this->ion_auth->remove_from_group(false, $id) == TRUE && $this->ion_auth->delete_user($id) == TRUE) 
		{
			$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_delete_success') ));
			redirect('auth/');
		}
		else
		{
			$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => lang('web_delete_failed') ) );
			redirect('auth/');
			
		}	
	}


	function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key => $value);
	}

	function _valid_csrf_nonce()
	{
		if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
				$this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	 /**
     * Set rules for form create and edit validations.
     *	
     * @return void
     */
	private function _set_rules($type = 'create')
	{
		//validate form input
		$this->form_validation->set_rules('first_name', 'lang:web_name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'lang:web_lastname', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'lang:web_email', 'required|valid_email');

		if ($type = 'edit')
			$this->form_validation->set_rules('password', 'lang:web_password', 'min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		else
			$this->form_validation->set_rules('password', 'lang:web_password', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');

		if ($type == 'edit')	
			$this->form_validation->set_rules('password_confirm', 'lang:web_password_confirm', '');
		else
			$this->form_validation->set_rules('password_confirm', 'lang:web_password_confirm', 'required');

		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	}
	
	
		

}

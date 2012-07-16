<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller
{
	protected $before_filter = array(
		'action' => 'is_logged_in',
		'except' => array('login', 'activate', 'deactivate', 'reset_password', 'forgot_password')
		//'only' => array('index')
	);

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->load->model('group');
		$this->load->model('usersgroup');
	}

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

			if (User::validate_login($this->input->post('identity'), $this->input->post('password')))
			{ //if the login is successful

				$this->sangar_auth->register_session();

				//redirect them back to the home page
				$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_login_correct') ));
				redirect('admin', 'refresh');
			}
			else
			{ //if the login was un-successful
				//redirect them back to the login page
				$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_login_incorrect') ));
				redirect('login', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
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

			$layout['body'] = $this->load->view('users/login', $this->data, TRUE);
			$this->load->view('layouts/login', $layout);
		}
    }


	function index()
	{
		//print_r($this->session->userdata());

		$data['title'] = lang('web_list_user');

		$data['users'] = User::find('all');

		$layout['body'] = $this->load->view('users/index', $data, TRUE);

		$this->load->view('layouts/backend', $layout);		
	}    


	function create() 
	{
		//Rules for validation
		$this->_set_rules();

		//create control variables
		$data['title'] = lang('web_add_user');
		$data['updType'] = 'create';

		//validate the fields of form
		if ($this->form_validation->run() == FALSE) 
		{			
			//load the view and the layout
			$layout['body'] = $this->load->view('users/create_user', $data, TRUE);
			$this->load->view('layouts/backend', $layout);
		}
		else
		{

			$data = array(
							'username'		=>	$this->input->post('email'),
							'email'			=>	$this->input->post('email'),
							'first_name' 	=> 	$this->input->post('first_name'),
							'last_name' 	=> 	$this->input->post('last_name'),
							'password' 		=> 	User::new_password($this->input->post('password'))
			);

			$result = $this->sangar_auth->register($data);

			if ($result)
			{	
				$email_activation = $this->config->item('email_activation');

				//watch if there is a email activation
				$lang_success =  ( $email_activation ) ?  'web_create_success_act' : "";
			
				$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang($lang_success) ));
				redirect('/admin/users/');	
			}
			else
			{
				$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => lang('web_create_failed') ));
				redirect('/admin/users/');					
			}
	  	} 
	}


	function edit($id = FALSE) 
	{
		//get the $id
		$id = ( $this->uri->segment(4) )  ? $this->uri->segment(4) : $this->input->post('id', TRUE);

		//Filter & Sanitize $id
		$id = ($id != 0) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

		//redirect if it´s no correct
		if (!$id){
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exist') ) );
			redirect('/admin/users/');
		}

		//Rules for validation
		$this->_set_rules('edit', $id);

		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			//create control variables
			$data['title'] = lang("web_edit_user");
			$data['updType'] = 'edit';


			//search the item to show in edit form
			$data['user'] = User::find_by_id($id);
			
			//load the view and the layout
			$layout['body'] = $this->load->view('users/create_user', $data, TRUE);
			$this->load->view('layouts/backend', $layout);
		}
		else
		{

			$data = array(
							'username'		=>	$this->input->post('email'),
							'email'			=>	$this->input->post('email'),
							'first_name' 	=> 	$this->input->post('first_name'),
							'last_name' 	=> 	$this->input->post('last_name')
			);

			if ( $this->input->post('password') != '' )
				$data['password']	=	User::new_password($this->input->post('password'));
		
			//find the item to update
			$user = User::find($this->input->post('id', TRUE));
			$user->update_attributes($data);

			// run insert model to write data to db
			if ($user->is_valid())
			{
				$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_edit_success') ));
				redirect('/admin/users/');
			}
			
			if ($user->is_invalid())
			{
				$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => lang('web_edit_failed') ) );
				redirect('/admin/users/');
				
			}	
	  	} 
	}


	function delete($id = false)
	{

		//filter & Sanitize $id
		$id = ($id != 0) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

		//redirect if it´s no correct
		if (!$id){
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exit') ) );
			redirect('/admin/users/');
		}
		
		$user = User::find_by_id($id);

		//search the item to delete
		if ( !$user )
		{
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exit') ) );
			redirect('/admin/users/');
		}


		//todo: delete groups

		//delete the item
		if ( $user->delete() ) 
		{
			$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_delete_success') ));
			redirect('/admin/users/');
		}
		else
		{
			$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => lang('web_delete_failed') ) );
			redirect('/admin/users/');
			
		}	
	}
	

	//activate the user
	function activate($id, $code=false)
	{		
		if ($code !== false)
			$activation = User::activate($id, $code);
		else if ($this->sangar_auth->is_admin())
			$activation = User::activate($id);

		if ($activation)
		{
			//redirect them to the forgot password page
			$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('activate_successful')) );


			if (!$this->sangar_auth->logged_in())
				redirect("login/", 'refresh');
			else
				redirect("/admin/users/", 'refresh');
		}
		else
		{
			//redirect them to the forgot password page
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('activate_unsuccessful')) );
			redirect("login/", 'refresh');
		}
	
	}

	//deactivate the user
	function deactivate($id = NULL)
	{
		// no funny business, force to integer
		$id = (int) $id;

		if ($this->sangar_auth->is_admin())
		{
			$code = User::deactivate($id);

			if ($code)
			{
				$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('deactivate_successful')) );
			}
			else
			{
				$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('deactivate_unsuccessful')) );
			}

			redirect("/admin/users/", 'refresh');
		}
		else
		{
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_not_do_this')) );
			redirect("/admin/users/", 'refresh');
		}
	}	
		

	private function _set_rules($type = 'create', $id = NULL)
	{
		//validate form input
		$this->form_validation->set_rules('first_name', 'lang:web_name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'lang:web_lastname', 'required|xss_clean');

		if ($id)
		{
			$this->form_validation->set_rules('email', 'lang:web_email', 'required|valid_email|is_unique[users.email.id.'.$id.']|xss_clean');	
		}
		else
		{
			$this->form_validation->set_rules('email', 'lang:web_email', 'required|valid_email|is_unique[users.email]|xss_clean');	
		}

		if ($type == 'edit')
			$this->form_validation->set_rules('password', 'lang:web_password', 'min_length[' . $this->config->item('min_password_length') . ']|max_length[' . $this->config->item('max_password_length') . ']|matches[password_confirm]');
		else
			$this->form_validation->set_rules('password', 'lang:web_password', 'required|min_length[' . $this->config->item('min_password_length') . ']|max_length[' . $this->config->item('max_password_length') . ']|matches[password_confirm]');

		if ($type == 'edit')	
			$this->form_validation->set_rules('password_confirm', 'lang:web_password_confirm', '');
		else
			$this->form_validation->set_rules('password_confirm', 'lang:web_password_confirm', 'required');

		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	}	

    function logout()
    {
        User::logout();

        redirect('');
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
			$layout['body'] = $this->load->view('users/forgot_password', $this->data, TRUE);
			$this->load->view('layouts/login', $layout);
		}
		else
		{
			//run the forgotten password method to email an activation code to the user
			$forgotten = $this->sangar_auth->forgotten_password($this->input->post('email'));

			if ($forgotten)
			{ //if there were no errors
				$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('remember_pass_successful') ));
				redirect("users/login", 'refresh'); //we should display a confirmation page here instead of the login page
			}
			else
			{
				$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('remember_pass_unsuccessful') ));
				redirect("users/forgot_password", 'refresh');
			}
		}
	}
	

	public function reset_password($code)
	{
		$reset = $this->sangar_auth->forgotten_password_complete($code);

		if ($reset)
		{  //if the reset worked then send them to the login page
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('password_change_successful')) );
			redirect("users/login", 'refresh');
		}
		else
		{ //if the reset didnt work then send them back to the forgot password page
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('password_change_unsuccessful')) );
			redirect("users/forgot_password", 'refresh');
		}
	}	    
	

}

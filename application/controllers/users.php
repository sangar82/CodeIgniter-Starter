<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

	public $layout = 'frontend';

	function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->model('post');
	}


	public function index()
	{
		

	}

	public function articles($user_id = FALSE)
	{
		
		$data['posts'] = Post::find_all_by_user_id($user_id);
		

		$this->load->view('users/articles', $data);
	}



	public function create()
	{

		$this->form_validation->set_rules('name', 'name', 'required|trim|xss_clean|min_length[2]|max_length[100]');			
		$this->form_validation->set_rules('lastname', 'lastname', 'required|trim|xss_clean|min_length[2]|max_length[100]');
		$this->form_validation->set_rules('email', 'email', 'valid_email|required|trim|xss_clean|min_length[2]|max_length[100]');
		//$this->form_validation->set_rules('image', 'image', 'callback_is_image');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this->load->view('/users/create');
		}
		else 
		{
			// passed validation proceed to post success logic
		 	
			//upload an image options
			$config['upload_path'] = FCPATH.'public/uploads/img/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['encrypt_name']	= TRUE;
			$config['max_width']  = '1024';
			$config['max_height']  = '10768';


			$this->load->library('upload', $config);
			$this->load->library('image_lib', $config); 

			if ( ! $this->upload->do_upload('image'))
			{
				$data = array('upload_error' => $this->upload->display_errors("<span class='error'>", "</span>"));
				$this->load->view('users/create', $data);
			}
			else
			{	
				$data = array();
				$info_upload = $this->upload->data();

				
				//thubmnails options
				$config = array();
				$config['image_library'] = 'gd2';
				$config['source_image'] = FCPATH.'public/uploads/img/'.$info_upload["file_name"];
				$config['new_image'] = FCPATH.'public/uploads/img/thumb_'.$info_upload["file_name"];
				$config['create_thumb'] = TRUE;
				$config['maintain_ratio'] = TRUE;
				$config['master_dim'] = 'width';
				$config['width'] = 75;
				$config['height'] = 50;

				$this->load->library('image_lib');
				$this->image_lib->initialize($config);  
					
				if ( ! $this->image_lib->resize())
				{
					$data = array('upload_error' => $this->image_lib->display_errors("<span class='error'>", "</span>"));
				}
	
		

				// build array for the model
				$form_data = array(
						       	'name' => set_value('name'),
						       	'lastname' => set_value('lastname'),
						       	'email' => set_value('email'),
						       	'image' =>	$info_upload["file_name"]
							);
						
				// run insert model to write data to db
				if ( User::create($form_data) == TRUE) // the information has therefore been successfully saved in the db
				{
					$this->session->set_flashdata('message', 'creado correctamente');
					$this->load->view('users/create', $data);
				}
				else
				{
					$this->session->set_flashdata('message', 'Error creando el usuario');
					$this->load->view('users/create', $data);
					
				}
				
			}

		}
	}



	public function is_image($str)
	{
		if ($str == '')
        {
            $this->form_validation->set_message('is_image', 'No has introducido ninguna imagen');
            return FALSE;
        }
        
        $a = explode(".", $_FILES['image']['name']);
        $a = array_reverse($a);
        $ext = strtolower($a[0]);
        
        $autorise = array('jpg', 'png', 'gif', 'jpeg');
        
        $is_image = in_array($ext, $autorise) ? TRUE : FALSE;

        if ($is_image){
        	return TRUE;
        }else{
        	$this->form_validation->set_message('is_image', 'Extensión incorrecta');
        	return FALSE;
        }
	}

 
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
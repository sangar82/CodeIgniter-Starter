<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller {

	protected $before_filter = array(
		'action' => 'is_logged_in',
		//'except' => array('index')
		//'only' => array('index')
	);
	
	function __construct()
	{
		parent::__construct();
	} 

	public function index()
	{	
		if(!$this->user)
		{
			//set message 
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_not_logged') ) );
			
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		}

		//create control variables
		$layout['title'] = lang('web_home');	

		//Guardamos en la variable $layout['body'] la vista renderizada users/list. Le pasamos tb la lista de todos los usuarios
		$layout['body'] = $this->load->view('admin/index', FALSE, TRUE);

		//Cargamos el layout y le pasamos el contenido que esta en la variable $layout
		$this->load->view('layouts/backend', $layout);
	}

	public function ckeditor()
	{
		$url = FCPATH.'public/uploads/ckeditor/'.time()."_".$_FILES['upload']['name'];
		
		$url_aux = substr($url, strlen(FCPATH) - 1);
			
	    if (($_FILES['upload'] == "none") OR (empty($_FILES['upload']['name'])) )
	    {
	       $message = "No file uploaded.";
	    }
	    else if(file_exists(FCPATH.'public/uploads/ckeditor/'.$_FILES['upload']['name']))
	    {
	    	$message = "File already exists";
	    }
	    else if ($_FILES['upload']["size"] == 0)
	    {
	       $message = "The file is of zero length.";
	    }
	    else if (($_FILES['upload']["type"] != "image/pjpeg") AND ($_FILES['upload']["type"] != "image/jpeg") AND ($_FILES['upload']["type"] != "image/png"))
	    {
	       $message = "The image must be in either JPG or PNG format. Please upload a JPG or PNG instead.";
	    }
	    else if (!is_uploaded_file($_FILES['upload']["tmp_name"]))
	    {
	       $message = "You may be attempting to hack our server. We're on to you; expect a knock on the door sometime soon.";
	    }
	    else 
	    {
	       $message = "Image uploaded correctly";
	       
	       move_uploaded_file($_FILES['upload']['tmp_name'], $url);
	    }

	    
		$funcNum = $_GET['CKEditorFuncNum'] ;
		$url = $url_aux;
		echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";

	}

}
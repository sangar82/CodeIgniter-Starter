<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
	} 


	public function index()
	{	
		if (!$this->ion_auth->logged_in())
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

}
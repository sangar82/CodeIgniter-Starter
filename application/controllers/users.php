<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {


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

 
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
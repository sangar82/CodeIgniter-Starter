<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Posts extends CI_Controller {



	function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library("pagination");
		$this->load->library('form_validation');

	}
/*
	public function _remap($method)
	{
	    if ($method == 'crear')
	    {
	        $this->create();
	    }
	    else
	    {
	        $this->index();
	    }
	 }   


	public function _remap($method, $params = array())
	{
	    $method = 'process/'.$method;
	    if (method_exists($this, $method))
	    {
	        return call_user_func_array(array($this, $method), $params);
	    }
	    show_404();
	}
*/

	public function index()
	{
		//Paginación
		$config = array();
        $config["base_url"] = site_url() . "";
        $config["total_rows"] = Post::count();
        $config["use_page_numbers"] = TRUE;
        $config["per_page"] = 3;
        $config["uri_segment"] = 1;
        $config['first_link'] = true;
        $config['last_link'] = true;
       	$choice = $config["total_rows"] / $config["per_page"];
	    $config["num_links"] = round($choice);

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(1)) ? $this->uri->segment(1) : 1;
		$data["all_posts"] = Post::all_paginate($config["per_page"], $page);
		$data["links"] = $this->pagination->create_links();
	
		
		//$data['all_posts'] = Post::all(); // or User:find('all')
		$this->load->view('posts/index' , $data);

	}

	public function create(){
		
		// http://codeigniter.com/user_guide/libraries/form_validation.html
		$this->form_validation->set_rules('title', 'title', 'required|trim|xss_clean|min_length[2]|max_length[100]');			
		$this->form_validation->set_rules('content', 'content', 'required|trim|xss_clean');
		$this->form_validation->set_rules('user_id', 'User id', 'required|trim|xss_clean|is_numeric');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this->load->view('/posts/create');
		}
		else 
		{
			// passed validation proceed to post success logic
		 	// build array for the model
			
			$form_data = array(
					       	'title' => set_value('title'),
					       	'content' => set_value('content'),
					       	'user_id' => set_value('user_id'),
						);
					
			// run insert model to write data to db
		
			if ( Post::create($form_data) == TRUE) // the information has therefore been successfully saved in the db
			{
				redirect('posts/success');   // or whatever logic needs to occur
			}
			else
			{
				// Or whatever error handling is necessary
				echo 'An error occurred saving your information. Please try again later';
			}
		}

	}


	public function archive($post_name = FALSE, $post_id = FALSE)
	{
		$data['post'] = Post::find($post_id);

		$data['tags'] = Post::find($post_id)->tags;
		
		$this->load->view('posts/archive' , $data);
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
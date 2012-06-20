<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Categories extends MY_Controller {

	protected $before_filter = array(
		'action' => 'is_logged_in',
		//'except' => array('index')
		//'only' => array('index')
	);
	
	function __construct()
	{
		parent::__construct();

		//$this->output->cache(10000);
	}


	public function index($parent_id = NULL)
	{	
		//set the title of the page 
		$layout['title'] = lang('web_category_list');

		//find all the categories with paginate and save it in array to past to the view
		$data["categories"] 	= 	Category::findby($parent_id);
		$data["category_id"] 	= 	$parent_id;
		$data['category']		= 	Category::find($parent_id);

		//Guardamos en la variable $layout['body'] la vista renderizada users/list. Le pasamos tb la lista de todos los usuarios
		$layout['body'] = $this->load->view('categories/list', $data, TRUE);

		//Cargamos el layout y le pasamos el contenido que esta en la variable $layout
		$this->load->view('layouts/backend', $layout);
	}



	function create($parent_id = FALSE) 
	{
		//get the parent id
		$parent_id = ( $this->uri->segment(3) )  ? $this->uri->segment(3) : $this->input->post('parent_id', TRUE);

		//Filter & Sanitize $id
		$parent_id = ($parent_id != 0) ? filter_var($parent_id, FILTER_VALIDATE_INT) : NULL;

		//Rules for validation
		$this->_set_rules();

		//create control variables
		$data['title'] = lang('web_category_create');
		$data['updType'] = 'create';
		$data['user'] = getTableColumns('categories', true);
		$data['parent_id'] = $parent_id;		

		//validate the fields of form
		if ($this->form_validation->run() == FALSE) 
		{	
			//load the view and the layout
			$layout['body'] = $this->load->view('categories/create', $data, TRUE);
			$this->load->view('layouts/backend', $layout);
		}
		else
		{
			// build array for the model
			$form_data = array(
				'name' => set_value('name'),
				'category_id' => $parent_id,
			);

			$category = Category::create($form_data);

			// run insert model to write data to db
			if ( $category->is_valid() ) // the information has therefore been successfully saved in the db
			{
				$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_create_success') ));
				redirect('categories/create/'.$parent_id);
			}
			
			if ( $category->is_invalid() )
			{
				//$this->session->set_flashdata('message', array( 'type' =>  'error', 'text' => lang('web_create_failed') ));
				$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => $category->errors->full_messages()));
				redirect('categories/create/'.$parent_id);
			}	
	  	} 
	}


	function edit($id = FALSE, $parent_id = FALSE) 
	{
		//Rules for validation
		$this->_set_rules('edit');

		//get the parent id and sanitize
		$parent_id = ( $this->uri->segment(4) )  ? $this->uri->segment(4) : $this->input->post('parent_id', TRUE);
		$parent_id = ($parent_id != 0) ? filter_var($parent_id, FILTER_VALIDATE_INT) : NULL;

		//get the $id and sanitize
		$id = ( $this->uri->segment(3) )  ? $this->uri->segment(3) : $this->input->post('id', TRUE);
		$id = ($id != 0) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

		//redirect if it´s no correct
		if (!$id){
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exit') ) );
			redirect('categories/');
		}

		//create control variables
		$data['title'] = lang("web_category_edit");
		$data['updType'] = 'edit';
		$data['parent_id'] = $parent_id;


		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{

			//search the item to show in edit form
			$data['category'] = Category::find_by_id($id);
			
			//load the view and the layout
			$layout['body'] = $this->load->view('categories/create', $data, TRUE);
			$this->load->view('layouts/backend', $layout);
		}
		else
		{
			// build array for the model
			$form_data = array(
					       	'name' => $this->input->post('name', TRUE ), 
					       	'id'	=> $this->input->post('id', TRUE)
						);
		
			//find the item to update
			$category = Category::find($this->input->post('id', TRUE));
			$category->update_attributes($form_data);

			// run insert model to write data to db
			if ( $category->is_valid()) // the information has therefore been successfully saved in the db
			{
				$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_edit_success') ));
				redirect('categories/'.$parent_id);
			}

			if ($category->is_invalid())
			{
				$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => $category->errors->full_messages() ) );
				redirect('categories/'.$parent_id);	
			}	
	  	} 
	}


	function delete($id = NULL)
	{
		//filter & Sanitize $id
		$id = ($id != 0) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

		//redirect if it´s no correct
		if (!$id){
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exit') ) );
			redirect('categories/');
		}
		
		//search the item to delete
		if ( Category::exists($id) )
		{
			$category = Category::find($id);
		}
		else
		{
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exit') ) );
			redirect('categories/');
		}

		//delete the item
		if ( $category->delete() == TRUE) 
		{
			$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_delete_success') ));	
		}
		else
		{
			$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => lang('web_delete_failed') ) );
		}	

		if ($category->category_id)
			redirect('categories/'.$category->category_id);
		else
			redirect('categories');

	}



	public function update_order_categories(){
		
		$categories = $this->input->post('categories');

		$array_cat = explode(',', $categories);

		$order = 1;

		foreach ($array_cat as $category) {
			Category::change_orders_categories($category, $order);
			$order++;
		}

	}



    /**
     * Set rules for form create and edit validations.
     *	
     * @return void
     */
	private function _set_rules($type = 'create')
	{

		$this->form_validation->set_rules('name', 'lang:web_name', 'required|trim|xss_clean|min_length[2]|max_length[100]|callback_name_check');
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

	}	
	

	public function name_check($name){
		
		$category_id = $this->input->post('parent_id');
		$id = $this->input->post('id');

		if ($category_id)
		{
			if ($id)
				$rows = Category::count( array('conditions' => array('category_id = ? AND name = ? AND id <> ?', $category_id, $name, $id)) );
			else
				$rows = Category::count( array('conditions' => array('category_id = ? AND name = ?', $category_id, $name)) );
		}
		else
		{
			if ($id)
				$rows = Category::count( array('conditions' => array('category_id is null AND name = ? AND id <> ?', $name, $id)) );
			else
				$rows = Category::count( array('conditions' => array('category_id is null AND name = ?', $name )) );
		}

		if ($rows)
		{
			$this->form_validation->set_message('name_check', lang('web_category_unique'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}


	private function set_paginate_options($category_id = NULL)
	{
		$config = array();

        
        if ($category_id)
        {
        	$config["base_url"] = site_url() . "categories/$category_id";
        	$config["total_rows"] = Category::count( array('conditions' => 'category_id = '.$category_id.'') );
        }
        	
        else
        {
        	$config["base_url"] = site_url() . "categories";
        	$config["total_rows"] = Category::count(  array('conditions' => 'category_id is null' ) );
        }
        	
        $config["use_page_numbers"] = TRUE;
        $config["per_page"] = 10;
        $config["uri_segment"] = 3;

        $config["first_link"] = "<< ".lang('web_first');
        $config['first_tag_open'] = "<span class='pag'>";
		$config['first_tag_close'] = '</span>';

		$config['last_link'] = lang('web_last') ." >>";
		$config['last_tag_open'] = "<span class='pag'>";
		$config['last_tag_close'] = "</span>";

		$config['next_link'] = FALSE;
		$config['next_tag_open'] = "<span class='pag'>";
		$config['next_tag_close'] = '</span>';

		$config['prev_link'] = FALSE;
		$config['prev_tag_open'] = "<span class='pag'>";
		$config['prev_tag_close'] = '</span>';

        $config['cur_tag_open'] = "<span class='pag pag_active'>";
        $config['cur_tag_close'] = '</span>';

        $config['num_tag_open'] = "<span class='pag'>";
        $config['num_tag_close'] = '</span>';

        $config['full_tag_open'] = "<div class='navigation'>";
        $config['full_tag_close'] = '</div>';

        $choice = $config["total_rows"] / $config["per_page"];
	    //$config["num_links"] = round($choice);

	    return $config;
	}
	


	function is_logged_in()
	{
		if (!$this->ion_auth->logged_in())
		{
			//set message 
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_not_logged') ) );
			
			//redirect them to the login page
			redirect('auth/login', 'refresh');
		}
	}		

}
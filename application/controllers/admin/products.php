<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
	}



	public function index()
	{	
		//set the title of the page 
		$layout['title'] = "Lista de productos";

		//set the pagination configuration array and initialize the pagination
		$config = $this->set_paginate_options();

		//Initialize the pagination class
		$this->pagination->initialize($config);

		//control of number page
		$page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 1;

		//find all the categories with paginate and save it in array to past to the view
		$data["products"] = Product::paginate($config["per_page"], $page);

		//create paginate´s links
		$data["links"] = $this->pagination->create_links();

		//Guardamos en la variable $layout['body'] la vista renderizada users/list. Le pasamos tb la lista de todos los usuarios
		$layout['body'] = $this->load->view('products/list', $data, TRUE);

		//Cargamos el layout y le pasamos el contenido que esta en la variable $layout
		$this->load->view('layouts/backend', $layout);
	}



	function create() 
	{
		//search the categories and send to the view
		$this->load->model('category');
		$data['categories']  = Category::find('all');

		//Rules for validation
		$this->set_rules();

		//validate the fields of form
		if ($this->form_validation->run() == FALSE) 
		{
			//create control variables
			$data['title'] = "Crear producto";
			$data['updType'] = 'create';
			$data['user'] = getTableColumns('products', true);
			
			//load the view and the layout
			$layout['body'] = $this->load->view('products/create', $data, TRUE);
			$this->load->view('layouts/backend', $layout);
		}
		else
		{
			// build array for the model
			$form_data = array(
					       	'name' => set_value('name'),
					       	'description' => set_value('description'),
					       	'active' => set_value('active'),
					       	'option' => set_value('option'),
					       	'image' => set_value('image'),
					       	'category_id' => set_value('category_id')
						);

			// run insert model to write data to db
			if ( Product::create($form_data) == TRUE) // the information has therefore been successfully saved in the db
			{
				$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_create_success') ));
				redirect('products/');
			}
			else
			{
				$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => lang('web_create_failed') ));
				redirect('products/');
			}	
	  	} 
	}


	function edit($id = FALSE) 
	{
		//search the categories and send to the view
		$this->load->model('category');
		$data['categories']  = Category::find('all');

		//Rules for validation
		$this->set_rules();

		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			//create control variables
			$data['title'] = "Editar producto";
			$data['updType'] = 'edit';

			//get the $id
			$id = ( $this->uri->segment(3) )  ? $this->uri->segment(3) : $this->input->post('id', TRUE);

			//Filter & Sanitize $id
			$id = ($id != 0) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

			//redirect if it´s no correct
			if (!$id){
				$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exit') ) );
				redirect('products/');
			}

			//search the item to show in edit form
			$data['product'] = Product::find_by_id($id);
			
			//load the view and the layout
			$layout['body'] = $this->load->view('products/create', $data, TRUE);
			$this->load->view('layouts/backend', $layout);
		}
		else
		{
			// build array for the model
			$form_data = array(
					       	'name' => $this->input->post('name', TRUE ), 
					       	'description' => $this->input->post('description', TRUE ), 
					       	'active' => $this->input->post('active', TRUE ), 
					       	'option' => $this->input->post('option', TRUE ), 
					       	'image' => $this->input->post('image', TRUE ), 
					       	'category_id' => $this->input->post('category_id', TRUE ), 
					       	'id'	=> $this->input->post('id', TRUE)
						);
		
			//find the item to update
			$product = Product::find($this->input->post('id', TRUE));

			// run insert model to write data to db
			if ( $product->update_attributes($form_data) == TRUE) // the information has therefore been successfully saved in the db
			{
				$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_edit_success') ));
				redirect('products/');
			}
			else
			{
				$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => lang('web_edit_failed') ) );
				redirect('products/');
				
			}	
	  	} 
	}


	function delete($id = NULL){

		//filter & Sanitize $id
		$id = ($id != 0) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

		//redirect if it´s no correct
		if (!$id){
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exit') ) );
			redirect('products/');
		}
		
		//search the item to delete
		if ( Product::exists($id) )
		{
			$product = Product::find($id);
		}
		else
		{
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exit') ) );
			redirect('products/');
		}

		//delete the item
		if ( $product->delete() == TRUE) 
		{
			$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_delete_success') ));
			redirect('products/');
		}
		else
		{
			$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => lang('web_delete_failed') ) );
			redirect('products/');
			
		}	

	}


    /**
     * Set rules for form create and edit validations.
     *	
     * @return void
     */
	private function set_rules()
	{
		$this->form_validation->set_rules('name', 'lang:web_name', 'required|trim|xss_clean|min_length[2]|max_length[100]');			
		$this->form_validation->set_rules('description', 'lang:web_description', 'required|trim|xss_clean|min_length[2]|max_length[500]');			
		$this->form_validation->set_rules('category_id', 'lang:web_category', 'is_numeric|required|trim|xss_clean');			
		$this->form_validation->set_rules('active', 'lang:web_category', 'is_numeric');			
		$this->form_validation->set_rules('option', 'lang:web_options', 'is_numeric|required|trim|xss_clean');				
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	}	


	private function set_paginate_options()
	{
		$config = array();

        $config["base_url"] = site_url() . "categories";
        $config["total_rows"] = Category::count();

        $config["use_page_numbers"] = TRUE;
        $config["per_page"] = 10;
        $config["uri_segment"] = 2;

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
	
		

}
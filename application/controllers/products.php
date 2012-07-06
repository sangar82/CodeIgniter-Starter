<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products extends MY_Controller {
	
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
		//set the title of the page 
		$layout['title'] = "Lista de productos";

		//set the pagination configuration array and initialize the pagination
		$config = $this->set_paginate_options('index');

		//Initialize the pagination class
		$this->pagination->initialize($config);

		//control of number page
		$page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 1;
		$data['parent_category'] = "";

		//find all the categories with paginate and save it in array to past to the view
		$data["products"] = Product::paginate_all($config["per_page"], $page);

		//create paginate´s links
		$data["links"] = $this->pagination->create_links();

		//control variables
		$data['page'] = $page;
		$data['category_id'] = NULL;

		//Guardamos en la variable $layout['body'] la vista renderizada users/list. Le pasamos tb la lista de todos los usuarios
		$layout['body'] = $this->load->view('products/list', $data, TRUE);

		//Cargamos el layout y le pasamos el contenido que esta en la variable $layout
		$this->load->view('layouts/backend', $layout);
	}


	public function product_list( $category_id = NULL, $page = 1)
	{	
		//set the title of the page 
		$layout['title'] = "Lista de productos";

		//set the pagination configuration array and initialize the pagination
		$config = $this->set_paginate_options('product_list', $category_id);

		//Initialize the pagination class
		$this->pagination->initialize($config);

		//control of number page
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 1;

		//find all the categories with paginate and save it in array to past to the view
		$data["products"] 			= 	Product::paginate($category_id, $config["per_page"], $page);
		$data['category_id']		=	$category_id;
		$data['page']				=	$page;
		$data['control']			= 	TRUE;
		
		if ( ! is_null( Category::find($category_id)->category) )
			$data['parent_category']	=	Category::find($category_id)->category->id;
		else
			$data['parent_category']	=	"";
		

		//create paginate´s links
		$data["links"] = $this->pagination->create_links();

		//Guardamos en la variable $layout['body'] la vista renderizada users/list. Le pasamos tb la lista de todos los usuarios
		$layout['body'] = $this->load->view('products/list', $data, TRUE);

		//Cargamos el layout y le pasamos el contenido que esta en la variable $layout
		$this->load->view('layouts/backend', $layout);
	}


	function create($category_id = NULL, $page = NULL) 
	{
		//search the categories and send to the view
		$this->load->model('category');
		$data['categories']  = Category::get_formatted_combo();

		//create control variables
		$data['title'] 					= 	"Crear producto";
		$data['updType'] 				= 	'create';
		$data['product'] 				= 	getTableColumns('products', true);
		$data['product']->category_id 	= 	$category_id;
		$data['page']					=	( $this->uri->segment(4) )  ? $this->uri->segment(4) : $this->input->post('page', TRUE);
		$data['parent_id']				=	( $this->uri->segment(3) )  ? $this->uri->segment(3) : $this->input->post('parent_id', TRUE);

		//variables for check the upload
		$upload_products_ok 		= FALSE;
		$thumbnail_products_ok 		= FALSE;

		//Rules for validation
		$this->set_rules();

		//validate the fields of form
		if ($this->form_validation->run() == FALSE) 
		{
			//load the view and the layout
			$layout['body'] = $this->load->view('products/create', $data, TRUE);
			$this->load->view('layouts/backend', $layout);	
		}
		else
		{
			$this->load->library('upload', $this->set_upload_options('products'));

			//upload the image
			if ( ! $this->upload->do_upload('image'))
			{
				$data['upload_error'] = $this->upload->display_errors("<span class='error'>", "</span>");
				
				//load the view and the layout
				$layout['body'] = $this->load->view('products/create', $data, TRUE);
				$this->load->view('layouts/backend', $layout);
			}
			else
			{
				$upload_products_ok = TRUE;
			}

			//create the thumbnail
			if ($upload_products_ok)
			{	
				//create an array to send to image_lib library to create the thumbnail
				$info_upload = $this->upload->data();

				//Load and initializing the imagelib library to create the thumbnail
				$this->load->library('image_lib');
				$this->image_lib->initialize($this->set_thumbnail_options($info_upload, 'products'));
				
				//create the thumbnail
				if ( ! $this->image_lib->resize())
				{
					$data = array('upload_error' => $this->image_lib->display_errors("<span class='error'>", "</span>"));

					//load the view and the layout
					$layout['body'] = $this->load->view('products/create', $data, TRUE);
					$this->load->view('layouts/backend', $layout);
				}
				else
				{
					$thumbnail_products_ok = TRUE;
				}
			}

			//save at BD
			if ($upload_products_ok and $thumbnail_products_ok)
			{
				// build array for the model
				$form_data = array(
						       	'name' => set_value('name'),
						       	'description' => set_value('description'),
						       	'active' => set_value('active'),
						       	'option' => set_value('option'),
						       	'image' => set_value('image'),
						       	'category_id' => set_value('category_id'),
						       	'image' =>	$info_upload["file_name"]
							);

				// run insert model to write data to db
				$product = Product::create($form_data);

				if ( $product->is_valid() ) // the information has therefore been successfully saved in the db
				{
					$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_create_success') ));
				}
				
				if ( $product->is_invalid() )
				{
					$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => $product->errors->full_messages() ));
				}

				if ($this->input->post('parent_id'))
					redirect('products/product_list/'.$this->input->post('category_id', TRUE).'/'.$this->input->post('page', TRUE));
				else
					redirect('products/'.$this->input->post('page', TRUE));
			}	
	  	} 
	}


	function edit($id = FALSE) 
	{
		//get the $id and sanitize
		$id = ( $this->uri->segment(3) )  ? $this->uri->segment(3) : $this->input->post('id', TRUE);
		$id = ($id != 0) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

		//search the categories and send to the view
		$this->load->model('category');
		$data['categories']  = Category::get_formatted_combo();

		//create control variables
		$data['title'] 		= 	"Editar producto";
		$data['updType'] 	= 	'edit';
		$data['page']		=	( $this->uri->segment(5) )  ? $this->uri->segment(5) : $this->input->post('page', TRUE);
		$data['parent_id']	=	( $this->uri->segment(4) )  ? $this->uri->segment(4) : $this->input->post('parent_id', TRUE);

		//variables for check the upload
		$upload_products_ok 		= FALSE;
		$thumbnail_products_ok 		= FALSE;

		//redirect if it´s no correct
		if (!$id){
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exist') ) );
			redirect('products/');
		}		

		//Rules for validation
		$this->set_rules();

		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			//search the item to show in edit form
			$data['product'] = Product::find_by_id($id);
			
			//load the view and the layout
			$layout['body'] = $this->load->view('products/create', $data, TRUE);
			$this->load->view('layouts/backend', $layout);
		}
		else
		{

			if ($_FILES['image']['name'] != '')
			{
				//initializing the upload library
				$this->load->library('upload', $this->set_upload_options('products'));

				//upload the image
				if ( ! $this->upload->do_upload('image'))
				{
					$data['upload_error'] = $this->upload->display_errors("<span class='error'>", "</span>");
					$data['product'] = Product::find($this->input->post('id', TRUE));
					
					//load the view and the layout
					$layout['body'] = $this->load->view('products/create', $data, TRUE);
					$this->load->view('layouts/backend', $layout);
				}
				else
				{
					$upload_products_ok = TRUE;
				}


				if ($upload_products_ok)
				{	
					//create an array to send to image_lib library to create the thumbnail
					$info_upload = $this->upload->data();

					//Load and initializing the imagelib library to create the thumbnail
					$this->load->library('image_lib');
					$this->image_lib->initialize($this->set_thumbnail_options($info_upload, 'products'));
					
					//create the thumbnail
					if ( ! $this->image_lib->resize())
					{
						$data = array('upload_error' => $this->image_lib->display_errors("<span class='error'>", "</span>"));
						$data['product'] = Product::find($this->input->post('id', TRUE));

						//load the view and the layout
						$layout['body'] = $this->load->view('products/create', $data, TRUE);
						$this->load->view('layouts/backend', $layout);
					}
					else
					{
						$thumbnail_products_ok = TRUE;
					}
				}
			}


			if ( !( ( $_FILES['image']['name'] != '' and $upload_products_ok and $thumbnail_products_ok) or $_FILES['image']['name'] == '' ) )
			{
				return FALSE;
			}


			// build array for the model
			$form_data = array(
					       	'name' 			=> $this->input->post('name', TRUE ), 
					       	'description' 	=> $this->input->post('description', TRUE ), 
					       	'active' 		=> $this->input->post('active', TRUE ), 
					       	'option' 		=> $this->input->post('option', TRUE ), 
					       	'category_id' 	=> $this->input->post('category_id', TRUE ), 
					       	'id'			=> $this->input->post('id', TRUE)
						);

			
			if ( isset( $info_upload["file_name"] ) )
				$form_data['image']		=	$info_upload["file_name"];

			//find the item to update
			$product = Product::find($this->input->post('id', TRUE));

			//save the old image to delete
			$old_image = $product->image;

			// run insert model to write data to db
			$product->update_attributes($form_data);


			if ( $product->is_valid() ) // the information has therefore been successfully saved in the db
			{
				$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_edit_success') ));

				//delete the old images
				if ($upload_products_ok)
				{
					if ( is_file(FCPATH.'public/uploads/products/img/'.$old_image) )
						unlink(FCPATH.'public/uploads/products/img/'.$old_image);
					
					if ( is_file(FCPATH.'public/uploads/products/img/thumbs/'.$old_image) )	
						unlink(FCPATH.'public/uploads/products/img/thumbs/'.$old_image);
				}
			}

			if ( $product->is_invalid() )
			{
				$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => $product->errors->full_messages() ) );
			}	

			if ($this->input->post('parent_id'))
				redirect('products/product_list/'.$this->input->post('category_id', TRUE).'/'.$this->input->post('page', TRUE));
			else
				redirect('products/'.$this->input->post('page', TRUE));
	  	} 
	}


	function delete($id = NULL, $category_id = NULL, $page = NULL)
	{	
		$this->load->helper('file');

		//filter & Sanitize $id
		$id = ($id != 0) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

		//redirect if it´s no correct
		if (!$id){
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exist') ) );
			redirect('products/');
		}
		
		//search the item to delete
		if ( Product::exists($id) )
		{
			$product = Product::find($id);
			$image = $product->image;
		}
		else
		{
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exist') ) );
			redirect('products/');
		}

		//delete the item
		if ( $product->delete() == TRUE) 
		{
			//delete all the images
			if ( is_file(FCPATH.'public/uploads/products/img/'.$image) )
				unlink(FCPATH.'public/uploads/products/img/'.$image);
			
			if ( is_file(FCPATH.'public/uploads/products/img/thumbs/'.$image) )	
				unlink(FCPATH.'public/uploads/products/img/thumbs/'.$image);

			$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_delete_success') ));

			if ($category_id != 0)
				redirect('products/product_list/'.$category_id.'/'.$page);
			else				
				redirect('products/'. $page);
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

	private function set_upload_options($controller)
	{	
		//upload an image options
		$config = array();
		$config['upload_path'] = FCPATH.'public/uploads/'.$controller.'/img/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['encrypt_name']	= TRUE;
		$config['max_width']  = '1024';
		$config['max_height']  = '768';

		//create controller upload folder if not exists
		if (!is_dir($config['upload_path']))
		{
			mkdir(FCPATH."public/uploads/$controller/");
			mkdir($config['upload_path']);
			mkdir($config['upload_path']."thumbs/");
		}
			
		return $config;
	}


	private function set_thumbnail_options($info_upload, $controller)
	{	
		$config = array();
		$config['image_library'] = 'gd2';
		$config['source_image'] = FCPATH.'public/uploads/'.$controller.'/img/'.$info_upload["file_name"];
		$config['new_image'] = FCPATH.'public/uploads/'.$controller.'/img/thumbs/'.$info_upload["file_name"];
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = FALSE;
		$config['master_dim'] = 'width';
		$config['width'] = 100;
		$config['height'] = 100;
		$config['thumb_marker'] = '';

		return $config;
	}


	private function set_paginate_options($method = NULL, $category_id = NULL)
	{
		$config = array();

		if ($method == 'index')
        	$config["base_url"] = site_url() . "products";
        else
        	$config["base_url"] = site_url() . "products/product_list/".$category_id;

        if ($method == 'index')
        	$config["total_rows"] = Product::count();
        else
        	$config["total_rows"] = Product::count( array('conditions' => 'category_id = '.$category_id.'') );


        $config["use_page_numbers"] = TRUE;

        $config["per_page"] = 5;

        if ($method == 'index')
        	$config["uri_segment"] = 2;
        else
        	$config["uri_segment"] = 4;



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
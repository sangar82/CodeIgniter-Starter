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

		$this->template->set_layout('backend');
	}

	public function index()
	{	
		//set the title of the page 
		$this->template->title(lang('web_list_product'));

		//set the pagination configuration array and initialize the pagination
		$config = $this->set_paginate_options('index');

		//Initialize the pagination class
		$this->pagination->initialize($config);

		//control of number page
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;

		$this->template->set('parent_category', "");

		//find all the categories with paginate and save it in array to past to the view
		$this->template->set("products", Product::paginate_all($config["per_page"], $page));

		//create paginate´s links
		$this->template->set("links", $this->pagination->create_links());

		//control variables
		$this->template->set('page', $page);
		$this->template->set('category_id', NULL);

		//load the view
		$this->template->build('products/list');
	}


	public function product_list( $category_id = NULL, $page = 1)
	{	
		//set the title of the page 
		$this->template->title(lang('web_list_product'));

		//set the pagination configuration array and initialize the pagination
		$config = $this->set_paginate_options('product_list', $category_id);

		//Initialize the pagination class
		$this->pagination->initialize($config);

		//control of number page
		$page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 1;

		//find all the categories with paginate and save it in array to past to the view
		$this->template->set("products", Product::paginate($category_id, $config["per_page"], $page));
		$this->template->set('category_id',$category_id);
		$this->template->set('page', $page);
		$this->template->set('control', TRUE);
		
		if ( ! is_null( Category::find($category_id)->category) )
			$this->template->set('parent_category',Category::find($category_id)->category->id);
		else
			$this->template->set('parent_category',"");

		//create paginate´s links
		$this->template->set("links", $this->pagination->create_links());

		//load the view
		$this->template->build('products/list');
	}


	function create($category_id = NULL, $page = NULL) 
	{
		//load block submit helper and append in the head
		$this->template->append_metadata(block_submit_button());

		//search the categories and send to the view
		$this->load->model('category');

		//create control variables
		$this->template->title(lang('web_add_product'));
		$this->template->set('updType', 'create');
		$this->template->set('category_id', $category_id);
		$this->template->set('categories', Category::get_formatted_combo());
		$this->template->set('page', ( $this->uri->segment(5) )  ? $this->uri->segment(5) : $this->input->post('page', TRUE));
		$this->template->set('parent_id', ( $this->uri->segment(4) )  ? $this->uri->segment(4) : $this->input->post('parent_id', TRUE));

		//auxiliar variables for the upload
		$form_data_aux	= array();

		//Rules for validation
		$this->set_rules();

		//validate the fields of form
		if ($this->form_validation->run() == FALSE) 
		{
			//load the view and the layout
			$this->template->build('products/create');	
		}
		else
		{
			foreach ($_FILES as $index => $value)
			{
				if ($value['name'] != '')
				{
					$this->load->library('upload');
					$this->upload->initialize($this->set_upload_options('products'));

					//upload the image
					if ( ! $this->upload->do_upload($index))
					{
						$this->template->set('upload_error', $this->upload->display_errors("<span class='error'>", "</span>"));
						
						//load the view and the layout
						$this->template->build('products/create');

						return FALSE;
					}
					else
					{
						//create an array to send to image_lib library to create the thumbnail
						$info_upload = $this->upload->data();

						//Save the name an array to save on BD before
						$form_data_aux[$index]		=	$info_upload["file_name"];

						//Load and initializing the imagelib library to create the thumbnail
						$this->load->library('image_lib');
						$this->image_lib->initialize($this->set_thumbnail_options($info_upload, 'products'));
						
						//create the thumbnail
						if ( ! $this->image_lib->resize())
						{
							$this->template->set('upload_error',  $this->image_lib->display_errors("<span class='error'>", "</span>"));

							//load the view and the layout
							$this->template->build('products/create');

							return FALSE;
						}
					}
				}
			}

			// build array for the model
			$form_data = array(
					       	'name' => set_value('name'),
					       	'description' => set_value('description'),
					       	'active' => set_value('active'),
					       	'option' => set_value('option'),
					       	'image' => set_value('image'),
					       	'category_id' => set_value('category_id')
						);

			//add the aux form data to the form data array to save
			$form_data = array_merge($form_data, $form_data_aux);

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
				redirect('admin/products/product_list/'.$this->input->post('category_id', TRUE).'/'.$this->input->post('page', TRUE));
			else
				redirect('admin/products/'.$this->input->post('page', TRUE));
		
	  	} 
	}


	function edit($id = FALSE) 
	{
		//load block submit helper and append in the head
		$this->template->append_metadata(block_submit_button());
				
		//get the $id and sanitize
		$id = ( $this->uri->segment(4) )  ? $this->uri->segment(4) : $this->input->post('id', TRUE);
		$id = ($id != 0) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

		//search the categories and send to the view
		$this->load->model('category');
		

		//create control variables
		$this->template->title('web_edit_product');
		$this->template->set('updType', 'edit');
		$this->template->set('page', ( $this->uri->segment(6) )  ? $this->uri->segment(6) : $this->input->post('page', TRUE));
		$this->template->set('parent_id', ( $this->uri->segment(5) )  ? $this->uri->segment(5) : $this->input->post('parent_id', TRUE));
		$this->template->set('categories', Category::get_formatted_combo());

		//variables for check the upload
		$form_data_aux			= array();
		$files_to_delete 		= array();

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
			$this->template->set('product', Product::find_by_id($id));
			
			//load the view and the layout				
			$this->template->build('products/create');
		}
		else
		{

			$data['product'] = Product::find($this->input->post('id', TRUE));
			$this->template->set('product',$data['product']);

			foreach ($_FILES as $index => $value)
			{
				if ($value['name'] != '')
				{
					//initializing the upload library
					$this->load->library('upload');
					$this->upload->initialize($this->set_upload_options('products'));

					//upload the image
					if ( ! $this->upload->do_upload($index))
					{
						$this->template->set('upload_error', $this->upload->display_errors("<span class='error'>", "</span>"));
						
						//load the view and the layout
						$this->template->build('products/create');

						return FALSE;
					}
					else
					{
						//create an array to send to image_lib library to create the thumbnail
						$info_upload = $this->upload->data();

						//Save the name an array to save on BD before
						$form_data_aux[$index]		=	$info_upload["file_name"];

						//Save the name of old files to delete
						array_push($files_to_delete, $data['product']->$index);

						//Load and initializing the imagelib library to create the thumbnail
						$this->load->library('image_lib');
						$this->image_lib->initialize($this->set_thumbnail_options($info_upload, 'products'));
						
						//create the thumbnail
						if ( ! $this->image_lib->resize())
						{
							$this->template->set('upload_error',  $this->image_lib->display_errors("<span class='error'>", "</span>"));

							//load the view and the layout
							$this->template->build('products/create');

							return FALSE;
						}
					}
				}
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

			
			//add the aux form data to the form data array to save
			$form_data = array_merge($form_data_aux, $form_data);

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
				foreach ($files_to_delete as $index)
				{
					if ( is_file(FCPATH.'public/uploads/products/img/'.$index) )
						unlink(FCPATH.'public/uploads/products/img/'.$index);
					
					if ( is_file(FCPATH.'public/uploads/products/img/thumbs/'.$index) )	
						unlink(FCPATH.'public/uploads/products/img/thumbs/'.$index);
				}
			}

			if ( $product->is_invalid() )
			{
				$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => $product->errors->full_messages() ) );
			}	

			if ($this->input->post('parent_id'))
				redirect('admin/products/product_list/'.$this->input->post('category_id', TRUE).'/'.$this->input->post('page', TRUE));
			else
				redirect('admin/products/'.$this->input->post('page', TRUE));
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
			redirect('admin/products/');
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
			redirect('admin/products/');
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
				redirect('admin/products/product_list/'.$category_id.'/'.$page);
			else				
				redirect('admin/products/'. $page);
		}
		else
		{
			$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => lang('web_delete_failed') ) );
			redirect('admin/products/');
			
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
		$this->form_validation->set_rules('category_id', 'lang:web_category', 'required|trim|xss_clean');			
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
        	$config["uri_segment"] = 3;
        else
        	$config["uri_segment"] = 5;



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
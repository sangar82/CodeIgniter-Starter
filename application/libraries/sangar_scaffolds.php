<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:  Sangar-scaffolds
*
* Author: 
*		  sangar1982@gmail.com
*         @sangar1982
*
*
* Location: https://github.com/sangar82/CodeIgniter-Starter
* Location: https://github.com/sangar82/sangar-scaffold-spark 
* Location: http://getsparks.org/packages/sangar-scaffold/versions/HEAD/show
*
* Created:  06.2012
*
* Description:  
*
* Requirements: Sangar Scaffolds creates the files for CRUD operations for you!
*               It creates the tables on the database, the controllers, the models and the views
* 				Each element has validation rules and the possibility to do it multilanguage.
* 				Create also a paginated list view.
* 				
* 				You can create forms with the followings elements:
*				- name
*    			- textarea
*   			- radiobuttons
*  				- checkboxes
*    			- select
*    			- select 1:N (populate the form select with a existent Model)
*    			- upload images (with thumbnail creation and uploads rules)
*    			- upload files (with uploads rules)
*   			- hidden relational (It's a special element. Only one hidden relational by scaffolding is allowed. It will produce a form with relation 1:N linked with his parent form automatically)
*/

require_once FCPATH.'sparks/php-activerecord/0.0.2/vendor/php-activerecord/ActiveRecord.php'; 

class Sangar_scaffolds
{
	public $dbdriver;

	public $controller_name;
	public $model_name;
	public $model_name_for_calls;
	public $scaffold_code;

	public $arrayjson;
	public $errors;

	public $actual_language;
	public $languages;

	public $scaffold_delete_bd;
	public $scaffold_bd;
	public $scaffold_routes;
	public $scaffold_menu;

	public $create_controller;
	public $create_model;	
	public $create_view_create;
	public $create_view_list;

	public $tab;
	public $tabx2;
	public $tabx3;
	public $tabx4;
	public $tabx5;
	public $tabx6;
	public $tabx7;
	public $sl;

	public $there_is_an_image;
	public $there_is_a_file;
	public $array_thumbnails_uploads;
	public $array_required_fields_uploads;

	public $there_is_a_relational_field;
	public $relational_field;
	public $relational_controller;
	public $relational_model;


	public function __construct()
	{
		$this->ci =& get_instance();

		$this->ci->load->database();
		$this->dbdriver = $this->ci->db->dbdriver;

		$this->actual_language 	= $this->ci->config->item('prefix_language');
		$this->languages 		= $this->ci->config->item('languages');

		$this->errors 	= FALSE;

		$this->tab 		= chr(9);
		$this->tabx2 	= chr(9).chr(9);
		$this->tabx3 	= chr(9).chr(9).chr(9);
		$this->tabx4 	= chr(9).chr(9).chr(9).chr(9);
		$this->tabx5 	= chr(9).chr(9).chr(9).chr(9).chr(9);
		$this->tabx6 	= chr(9).chr(9).chr(9).chr(9).chr(9).chr(9);
		$this->tabx7 	= chr(9).chr(9).chr(9).chr(9).chr(9).chr(9).chr(9);
		$this->sl  		= chr(13).chr(10);
	}


	public function create($data)
	{
		//Extraemos las variables
		$this->init($data);

		//Preparamos el JSON a partir de los datos enviados
		$result = $this->prepare_json();

		if ($result === FALSE)
		{
			return $this->errors;
		}


		//borramos la tabla en la base de datos
		if ($this->scaffold_delete_bd)
		{
			$result = $this->delete_table_bd();

			if ($result === FALSE)
			{
				return $this->errors;
			}
		}


		//creamos la tabla en la base de datos
		if ($this->scaffold_bd)
		{
			$result = $this->create_table_db();

			if ($result === FALSE)
			{
				return $this->errors;
			}
		}


		//creamos el controlador
		if ($this->create_controller)
		{
			$result = $this->create_controller();

			if ($result === FALSE)
			{
				return $this->errors;
			}
		}


		//creamos el modelo
		if ($this->create_model)
		{	
			$result = $this->create_model();

			if ($result === FALSE)
			{
				return $this->errors;
			}
		}


		//creamos la vista de create
		if ($this->create_view_create)
		{		
			$result = $this->create_view_create();

			if ($result === FALSE)
			{
				return $this->errors;
			}
		}


		//creamos la vista de lista
		if ($this->create_view_list)
		{
			$result = $this->create_view_list();

			if ($result === FALSE)
			{
				return $this->errors;
			}
		}


		//modify routes.php
		if ($this->scaffold_routes)
		{
			$result = $this->modify_routes();

			if ($result === FALSE)
			{
				return $this->errors;
			}
		}


		if ($this->scaffold_menu)
		{
			$result = $this->modify_menu();

			if ($result === FALSE)
			{
				return $this->errors;
			}
		}


		if ($this->there_is_a_relational_field)
		{
			
			$result = $this->add_relational_link_to_list();

			if ($result === FALSE)
			{
				return $this->errors;
			}

		}

		return TRUE;
	}


	private function init($data)
	{
		$this->controller_name			=	$data['controller_name'];
		$this->model_name				=	$data['model_name'];
		$this->model_name_for_calls		=	ucfirst($data['model_name']);
		$this->scaffold_code			=	$data['scaffold_code'];
		$this->scaffold_delete_bd		=	$data['scaffold_delete_bd'];
		$this->scaffold_bd 				=	$data['scaffold_bd'];
		$this->scaffold_routes 			=	$data['scaffold_routes'];
		$this->scaffold_menu 			=	$data['scaffold_menu'];
		$this->create_controller		=	$data['create_controller'];;
		$this->create_model				=	$data['create_model'];;	
		$this->create_view_create		=	$data['create_view_create'];;
		$this->create_view_list			=	$data['create_view_list'];;
	}


	private function prepare_json()
	{
		$arrayjson = json_decode("{".$this->scaffold_code."}", TRUE);

		$this->there_is_an_image 					= FALSE;
		$this->there_is_a_file						= FALSE;

		$this->there_is_a_relational_field			= FALSE;
		$this->relational_field 					= FALSE;
		$this->relational_controller 				= FALSE;
		$this->relational_model		 				= FALSE;

		$this->array_thumbnails_uploads				= array();
		$this->array_required_fields_uploads		= array();

		if ($arrayjson)
		{
			//evitamos que se puedan crear los nombres de los campos con mayúsculas
			//controlamos si hay imagenes o archivos que subir y miramos que campos tienen thumbnails y 
			//cuales son required y los guardamos en un array 
			//para manipular mas facilmente en el caso de que haya n uploads
			foreach ($arrayjson as $index => $value)
			{
				if (strtolower($index) !== $index)
				{
					$arrayjson[strtolower($index)] = $arrayjson[$index];
					unset($arrayjson[$index]);
				}

				if ($value['type'] == 'image')
				{
					$this->there_is_an_image = TRUE;

					if ($value['multilanguage'] === "TRUE")
					{
						foreach ($this->languages as $prefix=>$language)
						{
							if ( isset ($value['thumbnail']) )
								if ($value['thumbnail'])
									array_push($this->array_thumbnails_uploads, $index."_".$prefix);

					
							if ($value['required'] === 'TRUE')
								array_push($this->array_required_fields_uploads, $index."_".$prefix);							
						}
					}
					else
					{
						if ( isset ($value['thumbnail']) )
							if ($value['thumbnail'])
								array_push($this->array_thumbnails_uploads, $index);

						if ($value['required'] === 'TRUE')
							array_push($this->array_required_fields_uploads, $index);
					}

				}

				if ($value['type'] == 'file')
				{
					$this->there_is_a_file = TRUE;

					if ($value['multilanguage'] === "TRUE")
					{
						foreach ($this->languages as $prefix=>$language)
						{
							if ( isset ($value['thumbnail']) )
								if ($value['thumbnail'])
									array_push($this->array_thumbnails_uploads, $index."_".$prefix);

					
							if ($value['required'] === 'TRUE')
								array_push($this->array_required_fields_uploads, $index."_".$prefix);							
						}
					}
					else
					{
						if ( isset ($value['thumbnail']) )
							if ($value['thumbnail'])
								array_push($this->array_thumbnails_uploads, $index);

						if ($value['required'] === 'TRUE')
							array_push($this->array_required_fields_uploads, $index);
					}
				}

				if ($value['type'] == 'hidden')
				{
					$this->there_is_a_relational_field	=	TRUE;
					$this->relational_field				=	$index;
					$this->relational_controller		=   $value['controller'];
					$this->relational_model				= 	$value['model'];
				}
			}

			$this->arrayjson	=	$arrayjson;
		}
		else
		{
			$this->errors = lang('scaffolds_error_json');
			return FALSE;
		}

	}

	private function delete_table_bd()
	{
		$sql = "DROP TABLE IF EXISTS ".$this->controller_name.";";
		$conn = ActiveRecord\ConnectionManager::get_connection("development");
		$result = (object)$conn->query($sql);
		//$result = TRUE;

		if ($result)
			return TRUE;
		else
		{
			$this->errors = lang('scaffolds_error_del_bd')."<br> <pre>$sql_table</pre>";
			return FALSE;
		}
	}


	private function create_table_db()
	{
		switch($this->dbdriver)
		{
			case 'mysql':

	  			$sql_table = "CREATE TABLE ".$this->controller_name." ("; 
	  			$sql_table .= "id INT(9) NOT NULL AUTO_INCREMENT PRIMARY KEY ,";

	  			
	  			foreach ($this->arrayjson as $index => $value )
		    	{
		    		$sql_table_aux = "";

		      		switch ($value['type'])
		      		{
		        		case 'text':
		        		case 'image':
		        		case 'file':

		        			if ( $value['multilanguage'] == "TRUE")
		        			{
		        				foreach ($this->languages as $prefix=>$language)
		        				{
									$sql_table_aux .= $index."_".$prefix ." varchar(256) DEFAULT '' "; 

						            if ($value['required'])
						             $sql_table_aux .= "NOT NULL, ";
						            else 
						              $sql_table_aux .= ", ";
		        				}

		        				$sql_table .= $sql_table_aux;
		        			}
		        			else
		        			{
					        	$sql_table .= $index."  varchar(256)  DEFAULT '' ";
					        			            
								if ($value['required'])
									$sql_table .= "NOT NULL, ";
								else 
									$sql_table .= ", ";
		        			}

		        		break;

		        		case 'textarea':

		        			if ( $value['multilanguage'] == "TRUE")
		        			{
		        				foreach ($this->languages as $prefix=>$language)
		        				{
									$sql_table_aux .= $index."_".$prefix ." text DEFAULT '' "; 

						            if ($value['required'])
						             $sql_table_aux .= "NOT NULL, ";
						            else 
						              $sql_table_aux .= ", ";
		        				}

		        				$sql_table .= $sql_table_aux;
		        			}
		        			else
		        			{
					        	$sql_table .= $index."  text  DEFAULT '' ";
					        			            
								if ($value['required'])
									$sql_table .= "NOT NULL, ";
								else 
									$sql_table .= ", ";
		        			}

		        		break;

		        		case 'checkbox':

		        			$sql_table .= $index." INT(1) ";

					        if ($value['required'])
					         $sql_table .= "NOT NULL, ";
					        else 
					          $sql_table .= ", ";

		        		break;

		        		case 'select':
		        		case 'radio':

				        	$sql_table .= $index."  varchar(32)  DEFAULT '' ";
				        			            
							if ($value['required'])
								$sql_table .= "NOT NULL, ";
							else 
								$sql_table .= ", ";
		        		break;

		        		case 'selectbd':

				        	$sql_table .= $index."  INT(9) ";
				        			            
							if ($value['required'])
								$sql_table .= "NOT NULL, ";
							else 
								$sql_table .= ", ";
		        		break;

		        		case 'datepicker':

		        			$sql_table .= $index."  date, ";

		        		break;

		        		case 'hidden':

		        			$sql_table .= $index."  INT(9) NOT NULL, ";

		        		break;

		        	}
		        }

				$sql_table .= "created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, ";
				$sql_table .= "updated_at TIMESTAMP NOT NULL";
				$sql_table .= ") ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

				break;
		}

		$conn = ActiveRecord\ConnectionManager::get_connection("development");
		$result = (object)$conn->query($sql_table);
		//$result = TRUE;

		if ($result)
			return TRUE;
		else
		{
			$this->errors = lang('scaffolds_error_bd')."<br>$sql_table";
			return FALSE;
		}
	}


	private function create_controller()
	{
		$data = "";

		$data .= "

<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ".ucfirst($this->controller_name)." extends MY_Controller
{
	protected \$before_filter = array(
		'action' => 'is_logged_in'
		//'except' => array(),
		//'only' => array()
	);

	function __construct()
	{
		parent::__construct();

		\$this->template->set_layout('backend');
	}


	public function index()
	{	
		//set the title of the page 
		\$this->template->title(lang('web_list_of', array(':name' => '".$this->controller_name."')));
";

if ($this->there_is_a_relational_field)
{
$data .= "
		//control of number page
		\$data['".$this->relational_field."'] = (\$this->uri->segment(3)) ? \$this->uri->segment(3) : '';
		\$this->template->set('".$this->relational_field."', \$data['".$this->relational_field."']) ;

		//redirect if it´s no correct
		if (!\$data['".$this->relational_field."']){
			\$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exist') ) );
			redirect('/admin/".$this->relational_controller."/');
		}

		//set the pagination configuration array and initialize the pagination
		\$config = \$this->set_paginate_options(\$data['".$this->relational_field."']);
";
}
else
{
$data .= "
		//set the pagination configuration array and initialize the pagination
		\$config = \$this->set_paginate_options();
";
}

$data.="
		//Initialize the pagination class
		\$this->pagination->initialize(\$config);

		//control of number page
		\$page = (\$this->uri->segment(".(($this->there_is_a_relational_field) ? '4' : '3').")) ? \$this->uri->segment(".(($this->there_is_a_relational_field) ? '4' : '3').") : 1;

		//find all the categories with paginate and save it in array to past to the view
		\$this->template->set('".$this->controller_name."', ".$this->model_name_for_calls."::paginate_all(\$config['per_page'], \$page".( ($this->there_is_a_relational_field)  ? ", \$data['$this->relational_field']" : "" )."));

		//create paginate´s links
		\$this->template->set('links', \$this->pagination->create_links());

		//control variables
		\$this->template->set('page', \$page);

		//Load view
		\$this->template->build('".$this->controller_name."/list');
	}


	function create(\$page = NULL) 
	{
		//load block submit helper and append in the head
		\$this->template->append_metadata(block_submit_button());
";
		foreach ($this->arrayjson as $index => $value )
		{
	  		switch ($value['type'])
	  		{
	  			case 'textarea':

	  				if ($value['ckeditor'] == "TRUE")
	  				{
	  				$data .= "		//ckeditor scripts
	  	\$this->template->append_metadata(\"<script src='js/ckeditor/ckeditor.js' type='text/javascript'></script>\");
	  				";
	  				}
	  		}
	  	}

$data.= "
		//create control variables
		\$form_data_aux 	= 	array();
		\$this->template->title(lang('web_create_t', array(':name' => '".$this->controller_name."')));
		\$this->template->set('updType', 'create');
		";

	  	foreach ($this->arrayjson as $index => $value )
    	{
      		switch ($value['type'])
      		{
        		case 'selectbd':
        			$data .= "\$data['array_".strtolower($value['options']['model'])."']	= 	".$value['options']['model']."::find('all', array('order' => '".$value['options']['order']."' ));";
        		break;
        	}
        }

        if ($this->there_is_a_relational_field)
        {
			$data .= "\$data['".$index."'] = ( \$this->uri->segment(4) )  ? \$this->uri->segment(4) : \$this->input->post('".$index."', TRUE);";
			$data .= $this->sl.$this->tabx2."\$this->template->set('".$index."', \$data['".$index."']);";
        	$data .= $this->sl.$this->tabx2."\$data['page'] = ( \$this->uri->segment(5) )  ? \$this->uri->segment(5) : \$this->input->post('page', TRUE);";
        	$data .= $this->sl.$this->tabx2."\$this->template->set('page', \$data['page']);";
        }
        else
        {
			$data .= "\$this->template->set('page', ( \$this->uri->segment(4) )  ? \$this->uri->segment(4) : \$this->input->post('page', TRUE));";
        }
		
        $data .= "

		//Rules for validation
		\$this->set_rules();

		//validate the fields of form
		if (\$this->form_validation->run() == FALSE) 
		{
			//Load view
			\$this->template->build('".$this->controller_name."/create');	
		}
		else
		{
			//Validation OK!";

			if ($this->there_is_an_image or $this->there_is_a_file)
			{
				if (count($this->array_thumbnails_uploads))
				{
					$data .= $this->sl.$this->tabx3."\$array_thumbnails 	= explode(\",\", \"".implode(",", $this->array_thumbnails_uploads)."\");";
				}
				else
				{
					$data .= $this->sl.$this->tabx3."\$array_thumbnails 	= array();";
				}

				if (count($this->array_required_fields_uploads))
				{
					$data .= $this->sl.$this->tabx3."\$array_required 	= explode(\",\", \"".implode(",", $this->array_required_fields_uploads)."\");";
				}
				else
				{
					$data .= $this->sl.$this->tabx3."\$array_required 	= array();";
				}


				foreach ($this->arrayjson as $index => $value )
		    	{
		      		switch ($value['type'])
		      		{
		      			case 'image':
		      			case 'file':

		      				$aux = "";

		      				if ($value['multilanguage'] == "TRUE")
		      				{
		      					foreach ($this->languages as $prefix=>$language)
		      					{
		      						$aux .= $index."_".$prefix.","; 	
		      					}

		      					$aux = substr( $aux, 0, -1 );

$data .="

			//uploads fields for $index
			\$array_fields_".$index." = explode(\",\", \"$aux\");
";						
	      				}

		      			break;
		      		}
		      	}

$data .= "
			\$this->load->library('upload');
			\$this->load->library('image_lib');

			foreach (\$_FILES as \$index => \$value)
			{
				if (\$value['name'] != '')
				{";
			

			foreach ($this->arrayjson as $index => $value )
	    	{
	      		switch ($value['type'])
	      		{
	      			case 'image':
	      			case 'file':

	      				$aux = "";

	      				if ($value['multilanguage'] == "TRUE")
	      				{
	      					foreach ($this->languages as $prefix=>$language)
	      					{
	      						$aux .= $index."_".$prefix.", "; 	
	      					}

	      					$aux = substr( $aux, 0, -2 );

$data .="
					//uploads rules for \$index					
					if (in_array(\$index, \$array_fields_".$index."))
					{
						\$this->upload->initialize(\$this->set_upload_options('".$this->controller_name."', '".$index."'));
					}
";						
	      				}
	      				else
	      				{
$data .="
					//uploads rules for \$index
					if (\$index == '".$index."')
					{
						\$this->upload->initialize(\$this->set_upload_options('".$this->controller_name."', '".$index."'));
					}
";	
	      				}


	      			break;
	      		}
	      	}


$data .= "
					//upload the image
					if ( ! \$this->upload->do_upload(\$index))
					{
						//Load view
						\$this->template->set('error_'.\$index, \$this->upload->display_errors(\"<span class='error'>\", \"</span>\"));
						\$this->template->build('".$this->controller_name."/create');

						return FALSE;
					}
					else
					{
						//create an array to send to image_lib library to create the thumbnail
						\$info_upload = \$this->upload->data();

						//Save the name an array to save on BD before
						\$form_data_aux[\$index]		=	\$info_upload['file_name'];


						if (in_array(\$index, \$array_thumbnails))
						{
							//Initializing the imagelib library to create the thumbnail
";

			foreach ($this->arrayjson as $index => $value )
	    	{
	      		switch ($value['type'])
	      		{
	      			case 'image':

	      				$aux = "";

	      				if ($value['multilanguage'] == "TRUE")
	      				{
	      					foreach ($this->languages as $prefix=>$language)
	      					{
	      						$aux .= $index."_".$prefix.", "; 	
	      					}

	      					$aux = substr( $aux, 0, -2 );

$data .="
							//thumbnails rules for \$index		
							if (in_array(\$index, \$array_fields_".$index."))
							{
								\$this->image_lib->initialize(\$this->set_thumbnail_options(\$info_upload, '".$this->controller_name."', '".$index."'));
							}
";						
	      				}
	      				else
	      				{
$data .="
							//thumbnails rules for \$index
							if (\$index == '".$index."')
							{
								\$this->image_lib->initialize(\$this->set_thumbnail_options(\$info_upload, '".$this->controller_name."', '".$index."'));
							}
";

	      				}

	      			break;
	      		}
	      	}

$data .= "
							//create the thumbnail
							if ( ! \$this->image_lib->resize())
							{
								//Load view
								\$this->template->set('error_'.\$index, \$this->image_lib->display_errors(\"<span class='error'>\", \"</span>\"));
								\$this->template->build('".$this->controller_name."/create');

								return FALSE;
							}
						}
					}
				}
				else
				{
					if (in_array(\$index, \$array_required))
					{
						//Load view
						\$this->template->set('error_'.\$index, \"<span class='error'>\".lang('upload_no_file_selected').\"</span>\");
						\$this->template->build('".$this->controller_name."/create');

						return FALSE;
					}
				}
			}

";				
	        	
	        }

			// build array for the model
			$data .= "
			\$form_data = array(";

				foreach ($this->arrayjson as $index => $value )
		    	{
		      		switch ($value['type'])
		      		{
		        		case 'text':
		        		case 'textarea':

		        			if ( $value['multilanguage'] == "TRUE")
		        			{
		        				foreach ($this->languages as $prefix=>$language)
		        				{
		        					$data .= $this->sl.$this->tabx4."'".$index."_".$prefix."' => set_value('".$index."_".$prefix."'), ";
		        				}

		        			}
		        			else
		        			{
		        				$data .= $this->sl.$this->tabx4."'".$index."' => set_value('".$index."'), ";
		        			}

		        		break;

		        		case 'checkbox':
		        		case 'select':
		        		case 'selectbd':
		        		case 'radio':
		        		case 'datepicker':
		        		case 'hidden':


		        			$data .= $this->sl.$this->tabx4."'".$index."' => set_value('".$index."'), ";

		        		break;
		        	}
		        }

		        $data = substr( $data, 0, -2 );

			$data .=$this->sl.$this->tabx3.");

";

			if ($this->there_is_an_image or $this->there_is_a_file)
				$data .= $this->sl.$this->tabx3."\$form_data = array_merge(\$form_data, \$form_data_aux);";

			// run insert model to write data to db
			$data .= "

			\$".$this->model_name." = ".$this->model_name_for_calls."::create(\$form_data);

			if ( \$".$this->model_name."->is_valid() ) // the information has therefore been successfully saved in the db
			{
				\$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_create_success') ));
			}
			
			if ( $".$this->model_name."->is_invalid() )
			{
				\$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => \$".$this->model_name."->errors->full_messages() ));
			}

			".(($this->there_is_a_relational_field)  ? "redirect(\"/admin/".$this->controller_name."/\".\$this->input->post('".$this->relational_field."', TRUE));" : "redirect('/admin/".$this->controller_name."/');")."
		
	  	} 
	}


	function edit(\$id = FALSE, \$page = 1) 
	{
		//load block submit helper and append in the head
		\$this->template->append_metadata(block_submit_button());
";
		foreach ($this->arrayjson as $index => $value )
		{
	  		switch ($value['type'])
	  		{
	  			case 'textarea':

	  				if ($value['ckeditor'] == "TRUE")
	  				{
	  				$data .= "		//ckeditor scripts
	  	\$this->template->append_metadata(\"<script src='js/ckeditor/ckeditor.js' type='text/javascript'></script>\");
	  				";
	  				}
	  		}
	  	}

if ($this->there_is_a_relational_field)
{
$data .= "
		//get the \$id and sanitize
		\$id = ( \$this->uri->segment(4) )  ? \$this->uri->segment(4) : \$this->input->post('id', TRUE);
		\$id = ( \$id != 0 ) ? filter_var(\$id, FILTER_VALIDATE_INT) : NULL;

		//get the relation and sanitize
		\$data['".$this->relational_field."'] = (\$this->uri->segment(5)) ? \$this->uri->segment(5) : \$this->input->post('".$this->relational_field."', TRUE);
		\$data['".$this->relational_field."'] = ( \$data['".$this->relational_field."'] != 0 ) ? filter_var(\$data['".$this->relational_field."'], FILTER_VALIDATE_INT) : NULL;
		\$this->template->set('".$this->relational_field."', \$data['".$this->relational_field."']);


		//get the \$page and sanitize
		\$page = ( \$this->uri->segment(6) )  ? \$this->uri->segment(6) : \$this->input->post('page', TRUE);
		\$page = ( \$page != 0 ) ? filter_var(\$page, FILTER_VALIDATE_INT) : NULL;

		//redirect if it´s no correct
		if (!\$id or !\$data['".$this->relational_field."']){
			\$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exist') ) );
			redirect(\"admin/".$this->controller_name."/\".\$data['".$this->relational_field."']);
		}
";
}
else
{
$data .= "
		//get the \$id and sanitize
		\$id = ( \$this->uri->segment(4) )  ? \$this->uri->segment(4) : \$this->input->post('id', TRUE);
		\$id = ( \$id != 0 ) ? filter_var(\$id, FILTER_VALIDATE_INT) : NULL;

		//get the \$page and sanitize
		\$page = ( \$this->uri->segment(5) )  ? \$this->uri->segment(5) : \$this->input->post('page', TRUE);
		\$page = ( \$page != 0 ) ? filter_var(\$page, FILTER_VALIDATE_INT) : NULL;

		//redirect if it´s no correct
		if (!\$id){
			\$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exist') ) );
			redirect('admin/".$this->controller_name."/');
		}
";
}


$data .= "
		//variables for check the upload
		\$form_data_aux			= array();
		\$files_to_delete 		= array();
		";

	  	foreach ($this->arrayjson as $index => $value )
    	{
      		switch ($value['type'])
      		{
        		case 'selectbd':
        			$data .= "\$data['array_".strtolower($value['options']['model'])."']	= 	".$value['options']['model']."::find('all', array('order' => '".$value['options']['order']."' ));";
        		break;
        	}
        }
		
        $data .= "
		//Rules for validation
		\$this->set_rules(\$id);

		//create control variables
		\$this->template->title(lang('web_edit_t', array(':name' => '".$this->controller_name."')));
		\$this->template->set('updType', 'edit');
		\$this->template->set('page', \$page);

		if (\$this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			//search the item to show in edit form
			\$this->template->set('".$this->model_name."', ".$this->model_name_for_calls."::find_by_id(\$id));
			
			//load the view
			\$this->template->build('".$this->controller_name."/create');	
		}
		else
		{";

if ($this->there_is_an_image or $this->there_is_a_file)
{
				if (count($this->array_thumbnails_uploads))
				{
					$data .= $this->sl.$this->tabx3."\$array_thumbnails 	= explode(\",\", \"".implode(",", $this->array_thumbnails_uploads)."\");";
				}
				else
				{
					$data .= $this->sl.$this->tabx3."\$array_thumbnails 	= array();";
				}

				if (count($this->array_required_fields_uploads))
				{
					$data .= $this->sl.$this->tabx3."\$array_required 	= explode(\",\", \"".implode(",", $this->array_required_fields_uploads)."\");";
				}
				else
				{
					$data .= $this->sl.$this->tabx3."\$array_required 	= array();";
				}


				foreach ($this->arrayjson as $index => $value )
		    	{
		      		switch ($value['type'])
		      		{
		      			case 'image':
		      			case 'file':

		      				$aux = "";

		      				if ($value['multilanguage'] == "TRUE")
		      				{
		      					foreach ($this->languages as $prefix=>$language)
		      					{
		      						$aux .= $index."_".$prefix.","; 	
		      					}

		      					$aux = substr( $aux, 0, -1 );

$data .="

			//uploads fields for $index
			\$array_fields_".$index." = explode(\",\", \"$aux\");
";						
	      				}

		      			break;
		      		}
		      	}





$data .= "
			\$data['".$this->model_name."'] = ".$this->model_name_for_calls."::find(\$this->input->post('id', TRUE));
			\$this->template->set('".$this->model_name."', \$data['".$this->model_name."']);

			\$this->load->library('upload');
			\$this->load->library('image_lib');

			foreach (\$_FILES as \$index => \$value)
			{
				if (\$value['name'] != '')
				{
";

			foreach ($this->arrayjson as $index => $value )
	    	{
	      		switch ($value['type'])
	      		{
	      			case 'image':
	      			case 'file':

	      				$aux = "";

	      				if ($value['multilanguage'] == "TRUE")
	      				{
	      					foreach ($this->languages as $prefix=>$language)
	      					{
	      						$aux .= $index."_".$prefix.", "; 	
	      					}

	      					$aux = substr( $aux, 0, -2 );

$data .="
					//uploads rules for \$index					
					if (in_array(\$index, \$array_fields_".$index."))
					{
						\$this->upload->initialize(\$this->set_upload_options('".$this->controller_name."', '".$index."'));
					}
";						
	      				}
	      				else
	      				{
$data .="
					//uploads rules for \$index
					if (\$index == '".$index."')
					{
						\$this->upload->initialize(\$this->set_upload_options('".$this->controller_name."', '".$index."'));
					}
";	
	      				}


	      			break;
	      		}
	      	}


$data .= "
					//upload the image
					if ( ! \$this->upload->do_upload(\$index))
					{
						//Load view
						\$this->template->set('error_'.\$index, \$this->upload->display_errors(\"<span class='error'>\", \"</span>\"));
						\$this->template->build('".$this->controller_name."/create');

						return FALSE;
					}
					else
					{
						//create an array to send to image_lib library to create the thumbnail
						\$info_upload = \$this->upload->data();

						//Save the name an array to save on BD before
						\$form_data_aux[\$index]		=	\$info_upload[\"file_name\"];

						//Save the name of old files to delete
						array_push(\$files_to_delete, \$data['".$this->model_name."']->\$index);

						//Initializing the imagelib library to create the thumbnail

						if (in_array(\$index, \$array_thumbnails))
						{
";
			foreach ($this->arrayjson as $index => $value )
	    	{
	      		switch ($value['type'])
	      		{
	      			case 'image':

	      				$aux = "";

	      				if ($value['multilanguage'] == "TRUE")
	      				{
	      					foreach ($this->languages as $prefix=>$language)
	      					{
	      						$aux .= $index."_".$prefix.", "; 	
	      					}

	      					$aux = substr( $aux, 0, -2 );

$data .="
						//thumbnails rules for \$index		
						if (in_array(\$index, \$array_fields_".$index."))
						{
							\$this->image_lib->initialize(\$this->set_thumbnail_options(\$info_upload, '".$this->controller_name."', '".$index."'));
						}
";						
	      				}
	      				else
	      				{
$data .="
						//thumbnails rules for \$index
						if (\$index == '".$index."')
						{
							\$this->image_lib->initialize(\$this->set_thumbnail_options(\$info_upload, '".$this->controller_name."', '".$index."'));
						}
";

	      				}

	      			break;
	      		}
	      	}

$data .= "						
							//create the thumbnail
							if ( ! \$this->image_lib->resize())
							{
								//Load view
								\$this->template->set('error_'.\$index, \$this->image_lib->display_errors(\"<span class='error'>\", \"</span>\"));
								\$this->template->build('".$this->controller_name."/create');

								return FALSE;
							}
						}
					}
				}

			}		
";

		
}


$data .= "	
			// build array for the model
			\$form_data = array(
					       	'id'	=> \$this->input->post('id', TRUE),";

							foreach ($this->arrayjson as $index => $value )
					    	{
					      		switch ($value['type'])
					      		{
					        		case 'text':
					        		case 'textarea':

					        			if ( $value['multilanguage'] == "TRUE")
					        			{
					        				foreach ($this->languages as $prefix=>$language)
					        				{
					        					$data .= $this->sl.$this->tabx7."'".$index."_".$prefix."' => set_value('".$index."_".$prefix."'), ";
					        				}

					        			}
					        			else
					        			{
					        				$data .= $this->sl.$this->tabx7."'".$index."' => set_value('".$index."'), ";
					        			}

					        		break;


					        		case 'checkbox':
					        		case 'select':
					        		case 'selectbd':
					        		case 'radio':
					        		case 'datepicker':
					        		case 'hidden':

					        			$data .= $this->sl.$this->tabx7."'".$index."' => set_value('".$index."'), ";

					        		break;
					        	}
					        }

					        $data = substr( $data, 0, -2 );

					        $data .= "
						);

			//add the aux form data to the form data array to save
			\$form_data = array_merge(\$form_data_aux, \$form_data);
		
			//find the item to update
			\$".$this->model_name." = ".$this->model_name_for_calls."::find(\$this->input->post('id', TRUE));
			\$".$this->model_name."->update_attributes(\$form_data);

			// run insert model to write data to db
			if ( \$".$this->model_name."->is_valid()) // the information has therefore been successfully saved in the db
			{
";
			if ($this->there_is_an_image or $this->there_is_a_file)
			{
$data .= "				
				//delete the old images
				foreach (\$files_to_delete as \$index)
				{";
					if ($this->there_is_an_image)
					{
$data .= "
					if ( is_file(FCPATH.'public/uploads/".$this->controller_name."/img/'.\$index) )
						unlink(FCPATH.'public/uploads/".$this->controller_name."/img/'.\$index);
					
					if ( is_file(FCPATH.'public/uploads/".$this->controller_name."/img/thumbs/'.\$index) )	
						unlink(FCPATH.'public/uploads/".$this->controller_name."/img/thumbs/'.\$index);";
					}

					if ($this->there_is_a_file)
					{
$data .= "
					if ( is_file(FCPATH.'public/uploads/".$this->controller_name."/files/'.\$index) )	
						unlink(FCPATH.'public/uploads/".$this->controller_name."/files/'.\$index);";
					}
$data .= "
				}
";
					
			}
$data .= "
				\$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_edit_success') ));
				redirect(\"/admin/".$this->controller_name."/".(($this->there_is_a_relational_field) ? "\".\$this->input->post('".$this->relational_field."', TRUE).\"/\"" : "\"").".\$page);
			}

			if (\$".$this->model_name."->is_invalid())
			{
				\$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => \$".$this->model_name."->errors->full_messages() ) );
				redirect(\"/admin/".$this->controller_name."/".(($this->there_is_a_relational_field) ? "\".\$this->input->post('".$this->relational_field."', TRUE).\"/\"" : "\"").".\$page);
			}	
	  	} 
	}


	function delete(\$id = NULL, \$page = 1)
	{
		\$files_to_delete = array();

		//filter & Sanitize \$id
		\$id = (\$id != 0) ? filter_var(\$id, FILTER_VALIDATE_INT) : NULL;

		//redirect if it´s no correct
		if (!\$id){
			\$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exist') ) );
			
			redirect('".$this->controller_name."');
		}
		
		//search the item to delete
		if ( ".$this->model_name_for_calls."::exists(\$id) )
		{
			\$".$this->model_name." = ".$this->model_name_for_calls."::find(\$id);
		}
		else
		{
			\$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exist') ) );
			
			redirect('".$this->controller_name."');		
		}

		";

		if ($this->there_is_an_image or $this->there_is_a_file)
		{
			$data .= "//Save the files into array to delete after";
		}

	  	foreach ($this->arrayjson as $index => $value )
    	{
      		switch ($value['type'])
      		{
        		case 'image':
        		case 'file':

        		    if ( $value['multilanguage'] == "TRUE")
        			{
        				foreach ($this->languages as $prefix=>$language)
        				{
        					$aux = "_".$prefix;
        					$data .= $this->sl.$this->tabx2."array_push(\$files_to_delete, \$".$this->model_name."->$index$aux);";
        				}
        			}
        			else
        			{
						$data .= $this->sl.$this->tabx2."array_push(\$files_to_delete, \$".$this->model_name."->$index);";
        			}

        		break;
        	}
        }


$data .= "

		//delete the item
		if ( \$".$this->model_name."->delete() == TRUE) 
		{
			\$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_delete_success') ));";	


			if ($this->there_is_an_image or $this->there_is_a_file)
			{
$data .= "		

			//delete the old images
			foreach (\$files_to_delete as \$index)
			{";
				if ($this->there_is_an_image)
				{
$data .= "
				if ( is_file(FCPATH.'public/uploads/".$this->controller_name."/img/'.\$index) )
					unlink(FCPATH.'public/uploads/".$this->controller_name."/img/'.\$index);
				
				if ( is_file(FCPATH.'public/uploads/".$this->controller_name."/img/thumbs/'.\$index) )	
					unlink(FCPATH.'public/uploads/".$this->controller_name."/img/thumbs/'.\$index);";
				}

				if ($this->there_is_a_file)
				{
$data .= "
				if ( is_file(FCPATH.'public/uploads/".$this->controller_name."/files/'.\$index) )	
					unlink(FCPATH.'public/uploads/".$this->controller_name."/files/'.\$index);";
				}
$data .= "
			}
";
					
			}


		$data .="
		}
		else
		{
			\$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => lang('web_delete_failed') ) );
		}	

		redirect(\"/admin/".$this->controller_name."/\"".( ($this->there_is_a_relational_field) ? ".\$".$this->model_name."->".$this->relational_field."" : "" ).");
	}


	private function set_rules(\$id = NULL)
	{
		//Creamos los parametros de la funcion del constructor.
		// More validations: http://codeigniter.com/user_guide/libraries/form_validation.html
";
    	foreach ($this->arrayjson as $index => $value )
    	{
      		switch ($value['type'])
      		{
        		case 'text':
   
        			if ( $value['multilanguage'] == "TRUE")
        			{

        				foreach ($this->languages as $prefix=>$language)
        				{

	        				if ($value['is_unique'] == "TRUE")
	        				{
        						$aux 		= "|is_unique[".$this->controller_name.".".$index."_".$prefix."]";
        						$auxwithid 	= "|is_unique[".$this->controller_name.".".$index."_".$prefix.".id.\$id]";

	$data .= "
		if (\$id)
		{
			\$this->form_validation->set_rules('".$index."_".$prefix."', '".ucfirst($index)." ($prefix)', \"".(($value['required'] == 'TRUE') ? 'required|' : '')."trim|xss_clean|min_length[".$value['minlength']."]|max_length[".$value['maxlength']."]$auxwithid\");
		}
		else
		{
			\$this->form_validation->set_rules('".$index."_".$prefix."', '".ucfirst($index)." ($prefix)', \"".(($value['required'] == 'TRUE') ? 'required|' : '')."trim|xss_clean|min_length[".$value['minlength']."]|max_length[".$value['maxlength']."]$aux\");
		}
	";

	        				}
	        				else
	        				{
	        					$data .= $this->tabx2."\$this->form_validation->set_rules('".$index."_".$prefix."', '".ucfirst($index)." ($prefix)', '".(($value['required'] == 'TRUE') ? 'required|' : '')."trim|xss_clean|min_length[".$value['minlength']."]|max_length[".$value['maxlength']."]');".$this->sl;
	        				}
        				}
        			}
        			else
        			{
        				if ($value['is_unique'] == "TRUE")
        				{
        					$aux 		= "|is_unique[".$this->controller_name.".".$index."]";
        					$auxwithid 	= "|is_unique[".$this->controller_name.".".$index.".id.\$id]";

	$data .= "
		if (\$id)
		{
			\$this->form_validation->set_rules('$index', '$index', \"".(($value['required'] == 'TRUE') ? 'required|' : '')."trim|xss_clean|min_length[".$value['minlength']."]|max_length[".$value['maxlength']."]$auxwithid\");
		}
		else
		{
			\$this->form_validation->set_rules('$index', '$index', \"".(($value['required'] == 'TRUE') ? 'required|' : '')."trim|xss_clean|min_length[".$value['minlength']."]|max_length[".$value['maxlength']."]$aux\");
		}

";  
        				}
        				else
        				{
        					$data .= $this->sl.$this->tabx2."\$this->form_validation->set_rules('$index', '$index', '".(($value['required'] == 'TRUE') ? 'required|' : '')."trim|xss_clean|min_length[".$value['minlength']."]|max_length[".$value['maxlength']."]');".$this->sl;
        				}        				
        			}

        		break;

        		case 'textarea':

        			if ( $value['multilanguage'] == "TRUE")
        			{
        				foreach ($this->languages as $prefix=>$language)
        				{
        					if ( $value['ckeditor'] == "TRUE")
								$data .= $this->sl.$this->tabx2."\$this->form_validation->set_rules('".$index."_".$prefix."', '".ucfirst($index)." ($prefix)', '".(($value['required'] == 'TRUE') ? 'required|' : '')."trim|min_length[".$value['minlength']."]|max_length[".$value['maxlength']."]');".$this->sl;
							else
								$data .= $this->sl.$this->tabx2."\$this->form_validation->set_rules('".$index."_".$prefix."', '".ucfirst($index)." ($prefix)', '".(($value['required'] == 'TRUE') ? 'required|' : '')."trim|xss_clean|min_length[".$value['minlength']."]|max_length[".$value['maxlength']."]');".$this->sl;
        				}
        			}
        			else
        			{
        				if ( $value['ckeditor'] == "TRUE")
        					$data .= $this->sl.$this->tabx2."\$this->form_validation->set_rules('$index', '$index', '".(($value['required'] == 'TRUE') ? 'required|' : '')."trim|min_length[".$value['minlength']."]|max_length[".$value['maxlength']."]');".$this->sl;
        				else
        					$data .= $this->sl.$this->tabx2."\$this->form_validation->set_rules('$index', '$index', '".(($value['required'] == 'TRUE') ? 'required|' : '')."trim|xss_clean|min_length[".$value['minlength']."]|max_length[".$value['maxlength']."]');".$this->sl;
        				$data .= $this->tabx5;
        			}

        		break;

        		case 'checkbox':
        		case 'select':
        		case 'selectbd':
        		case 'radio':
        		case 'datepicker':

        	    	$data .= $this->sl.$this->tabx2."\$this->form_validation->set_rules('$index', '$index', '".(($value['required'] == 'TRUE') ? 'required|' : '')."xss_clean');".$this->sl;
        			$data .= $this->tabx5;	

        		break;


        		case 'hidden':

        	    	$data .= $this->sl.$this->tabx2."\$this->form_validation->set_rules('$index', '$index', 'requires|trim|is_numeric|xss_clean');".$this->sl;
        			$data .= $this->tabx5;	

        		break;
        	}
        }

        $data .= $this->tabx2."\$this->form_validation->set_error_delimiters(\"<br /><span class='error'>\", '</span>');";
		$data .= $this->sl.$this->tab."}		

	
	private function set_paginate_options(".(($this->there_is_a_relational_field) ? "\$$this->relational_field" : "").")
	{
		\$config = array();

		\$config['base_url'] = site_url() . 'admin/".$this->controller_name."".(($this->there_is_a_relational_field) ? "/'.\$$this->relational_field" : "'").";

		\$config['use_page_numbers'] = TRUE;

	    \$config['per_page'] = 10;
";

if ($this->there_is_a_relational_field)
$data .= "
		\$config['total_rows'] = ".ucfirst($this->model_name)."::count(array('conditions' => array('".$this->relational_field." = ?', \$".$this->relational_field.")));

		\$config['uri_segment'] = 4;";

else
$data .= "
		\$config['total_rows'] = ".ucfirst($this->model_name)."::count();

		\$config['uri_segment'] = 3;";


$data .= "

	    \$config['first_link'] = \"<< \".lang('web_first');
	    \$config['first_tag_open'] = \"<span class='pag'>\";
		\$config['first_tag_close'] = '</span>';

		\$config['last_link'] = lang('web_last') .\" >>\";
		\$config['last_tag_open'] = \"<span class='pag'>\";
		\$config['last_tag_close'] = '</span>';

		\$config['next_link'] = FALSE;
		\$config['next_tag_open'] = \"<span class='pag'>\";
		\$config['next_tag_close'] = '</span>';

		\$config['prev_link'] = FALSE;
		\$config['prev_tag_open'] = \"<span class='pag'>\";
		\$config['prev_tag_close'] = '</span>';

	    \$config['cur_tag_open'] = \"<span class='pag pag_active'>\";
	    \$config['cur_tag_close'] = '</span>';

	    \$config['num_tag_open'] = \"<span class='pag'>\";
	    \$config['num_tag_close'] = '</span>';

	    \$config['full_tag_open'] = \"<div class='navigation'>\";
	    \$config['full_tag_close'] = '</div>';

	    \$choice = \$config[\"total_rows\"] / \$config[\"per_page\"];
	    //\$config[\"num_links\"] = round(\$choice);

	    return \$config;
	}

		";


if ($this->there_is_an_image or $this->there_is_a_file)
{
$data .= "
	private function set_upload_options(\$controller, \$field)
	{	
		//upload an image options
		\$config = array();
";	
}		
	  	foreach ($this->arrayjson as $index => $value )
    	{
      		switch ($value['type'])
      		{
        		case 'image':

$data .= "
		if (\$field == '$index')
		{
			\$config['upload_path'] 	= FCPATH.'public/uploads/'.\$controller.'/img/';
			\$config['allowed_types'] 	= '".$value['upload']['allowed_types']."';
			\$config['encrypt_name']	= ".$value['upload']['encrypt_name'].";
			\$config['max_width']  		= '".$value['upload']['max_width']."';
			\$config['max_height']  	= '".$value['upload']['max_height']."';
			\$config['max_size'] 		= '".$value['upload']['max_size']."';
		}

";
        		break;

        		case 'file':

$data .= "
		if (\$field == '$index')
		{
			\$config['upload_path'] 		= FCPATH.'public/uploads/'.\$controller.'/files/';
			\$config['allowed_types'] 	= '".$value['upload']['allowed_types']."';
			\$config['max_size'] 		= '".$value['upload']['max_size']."';
			\$config['encrypt_name']		= ".$value['upload']['encrypt_name'].";
		}

";
        		break;

        	}
        }


if ($this->there_is_an_image or $this->there_is_a_file)
{
$data .= "
		//create controller upload folder if not exists
		if (!is_dir(\$config['upload_path']))
		{
			mkdir(FCPATH.\"public/uploads/\$controller/\");
			mkdir(FCPATH.\"public/uploads/\$controller/files/\");
			mkdir(FCPATH.\"public/uploads/\$controller/img/\");
			mkdir(FCPATH.\"public/uploads/\$controller/img/thumbs/\");
		}
			
		return \$config;
	}
";	
}		


if ($this->there_is_an_image)
{
$data .= "
	
	private function set_thumbnail_options(\$info_upload, \$controller, \$field)
	{	
		\$config = array();
		\$config['image_library'] = 'gd2';
		\$config['source_image'] = FCPATH.'public/uploads/'.\$controller.'/img/'.\$info_upload[\"file_name\"];
		\$config['new_image'] = FCPATH.'public/uploads/'.\$controller.'/img/thumbs/'.\$info_upload[\"file_name\"];
		\$config['create_thumb'] = TRUE;
";	
}	

	  	foreach ($this->arrayjson as $index => $value )
    	{
      		switch ($value['type'])
      		{
        		case 'image':

$data .= "
		if (\$field == '$index')
		{
			\$config['maintain_ratio'] = ".$value['thumbnail']['maintain_ratio'].";
			\$config['master_dim'] = '".$value['thumbnail']['master_dim']."';
			\$config['width'] = ".$value['thumbnail']['width'].";
			\$config['height'] = ".$value['thumbnail']['height'].";
			\$config['thumb_marker'] = '';
		}
";
        		break;
        	}
        }

 if ($this->there_is_an_image)
{
$data .= "
		return \$config;
	}
";	
}	
		

        $data .= "	

}";


		if ( $this->save_file($this->controller_name, "controllers/admin/", trim( $data ) ) === TRUE )
			return TRUE;
		else
			return FALSE;
	}


	private function create_model()
	{
		$data = "
<?php
class ".$this->model_name_for_calls." extends ActiveRecord\Model {

	
	static \$validates_presence_of = array(";

		$there_are_requireds = FALSE;

		foreach ($this->arrayjson as $index => $value )
    	{
      		switch ($value['type'])
      		{
        		case 'text':
        		case 'textarea':

        			if ($value['required'] == "TRUE")
        			{
	        			if ( $value['multilanguage'] == "TRUE")
	        			{
	        				foreach ($this->languages as $prefix=>$language)
	        				{
	        					$data .= $this->sl.$this->tabx2."array('".$index."_".$prefix."'), ";
	        				}
	        			}
	        			else
	        			{
	        				$data .= $this->sl.$this->tabx2."array('".$index."'), ";
	        			}

	        			$there_are_requireds = TRUE;
        			}

        		break;

        		case 'checkbox':
        		case 'select':
        		case 'selectbd':
        		case 'radio':
        		case 'datepicker':


        			if ($value['required'] == "TRUE")
        			{
	        			$data .= $this->sl.$this->tabx2."array('".$index."'), ";	        			
	        			$there_are_requireds = TRUE;
        			}        		

        		break;

        		case 'hidden':

	        			$data .= $this->sl.$this->tabx2."array('".$index."'), ";	        			
	        			$there_are_requireds = TRUE;

        		break;
        	}
        }

        if ($there_are_requireds)
        	$data = substr( $data, 0, -2 );

        $data .="	
    );


	static function paginate_all(\$limit, \$page".( ($this->there_is_a_relational_field)  ? ", \$$this->relational_field" : "" ).")
	{
		\$offset = \$limit * ( \$page - 1) ;

		\$result = ".$this->model_name_for_calls."::find('all', array(".( ($this->there_is_a_relational_field)  ? "'conditions' => '$this->relational_field = '.\$$this->relational_field.'', " : "" )."'limit' => \$limit, 'offset' => \$offset, 'order' => 'id DESC' ) );

		if (\$result)
		{
			return \$result;
		}
		else
		{
			return FALSE;
		}
	}


}
		";


		if ( $this->save_file($this->model_name, "models/", trim( $data ) ) === TRUE )
			return TRUE;
		else
			return FALSE;
	}


	private function create_view_create()
	{

		$data = "";
		
		foreach ($this->arrayjson as $index => $value )
    	{
      		switch ($value['type'])
      		{
        		case 'datepicker':

$data .= "
<script src=\"js/datepicker/jquery.ui.datepicker-<?=\$this->config->item('prefix_language')?>.js\" type=\"text/javascript\"></script>
<script>
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional['<?=\$this->config->item('prefix_language')?>']);
		$('.datepicker').datepicker({dateFormat: 'dd-mm-yy'});
	});
</script>
";
        		break;

        		case 'textarea':
				if ($value['ckeditor'] == "TRUE")
				{
					if ($value['multilanguage'] == "TRUE")
					{
$data .= "
<script type='text/javascript'>

	$(document).ready(function(){

";
						foreach ($this->languages as $prefix=>$language)
						{
							$data.="CKEDITOR.replace( '".$index."_".$prefix."', {language: '<?=\$this->config->item('prefix_language')?>', filebrowserUploadUrl : \"/admin/ckeditor/\"});";
						}
$data .= "
		$('#submit').click(function() {
";

						foreach ($this->languages as $prefix=>$language)
						{
							$data .="CKEDITOR.instances.".$index."_".$prefix.".updateElement();";
						}
			
$data.="
			return true;

		});

	});

</script>
";	
					}
					else
					{
$data .= "
<script type='text/javascript'>

	$(document).ready(function(){

		CKEDITOR.replace( '".$index."', {language: '<?=\$this->config->item('prefix_language')?>',filebrowserUploadUrl : \"/admin/admin/ckeditor/\"});

		$('#submit').click(function() {

			CKEDITOR.instances.".$index.".updateElement();

			return true;

		});

	});

</script>
";							
					}
				}
        		break;

        	}
        }

		$data .= "
<div id='content-top'>
    <h2><?=(\$updType == 'create') ? lang('web_create_t', array(':name' => '$this->model_name')) : lang('web_edit_t', array(':name' => '$this->model_name'));?></h2>
    <a href='/admin/".$this->controller_name."/".(($this->there_is_a_relational_field) ? "<?=\$$this->relational_field?>/" : "")."<?=\$page?>' class='bforward'><?=lang('web_back_to_list')?></a>
    <span class='clearFix'>&nbsp;</span>
</div>

<?php 
\$attributes = array('class' => 'tform', 'id' => '');
echo (\$updType == 'create') ? form_open_multipart('/admin/".$this->controller_name."/create', \$attributes) : form_open_multipart('/admin/".$this->controller_name."/edit', \$attributes); 
?>
";

foreach ($this->arrayjson as $index => $value )
{
	switch ($value['type'])
	{
		case 'text':
		
			if ( $value['multilanguage'] == "TRUE")
			{
				foreach ($this->languages as $prefix=>$language)
				{
$data .="
<p>
	<label class='labelform' for='".$index."_".$prefix."'>".ucfirst($index)." ($prefix) ".(($value['required'] == "TRUE") ? "<span class='required'>*</span>" : "") ."</label>
	<input id='".$index."_".$prefix."' type='text' name='".$index."_".$prefix."' maxlength='".$value['maxlength']."' value=\"<?php echo set_value('".$index."_".$prefix."', (isset(\$".$this->model_name."->".$index."_".$prefix.")) ? \$".$this->model_name."->".$index."_".$prefix." : ''); ?>\"  />
	<?php echo form_error('".$index."_".$prefix."'); ?>
</p>
";
				}
			}
			else
			{
$data .="
<p>
	<label class='labelform' for='".$index."'>".ucfirst($index)." ".(($value['required'] == "TRUE") ? "<span class='required'>*</span>" : "") ."</label>
	<input id='".$index."' type='text' name='".$index."' maxlength='".$value['maxlength']."' value=\"<?php echo set_value('".$index."', (isset(\$".$this->model_name."->".$index.")) ? \$".$this->model_name."->".$index." : ''); ?>\"  />
	<?php echo form_error('".$index."'); ?>
</p>
";
			}

		break;

		case 'textarea':
		
			if ( $value['multilanguage'] == "TRUE")
			{
				foreach ($this->languages as $prefix=>$language)
				{
$data .="
<p>
	<label class='labelform' for='".$index."_".$prefix."'>".ucfirst($index)." ($prefix) ".(($value['required'] == "TRUE") ? "<span class='required'>*</span>" : "") ."</label>
	<textarea id=\"".$index."_".$prefix."\"  name=\"".$index."_".$prefix."\"  /><?php echo set_value('".$index."_".$prefix."', (isset(\$".$this->model_name."->".$index."_".$prefix.")) ? htmlspecialchars_decode(\$".$this->model_name."->".$index."_".$prefix.") : ''); ?></textarea>
	<?php echo form_error('".$index."_".$prefix."'); ?>
</p>
";
				}
			}
			else
			{
$data .="
<p>
	<label class='labelform' for='".$index."'>".ucfirst($index)." ".(($value['required'] == "TRUE") ? "<span class='required'>*</span>" : "") ."</label>
	<textarea id=\"".$index."\"  name=\"".$index."\"  /><?php echo set_value('".$index."', (isset(\$".$this->model_name."->".$index.")) ? htmlspecialchars_decode(\$".$this->model_name."->".$index.") : ''); ?></textarea>
	<?php echo form_error('".$index."'); ?>
</p>
";
			}

		break;

		case 'checkbox':
$data .= "
<p>
	<input id='".$index."' ".(($value['checked'] == "TRUE") ?  ' checked '  :  '')."type='checkbox' name='".$index."' value='1' <?=preset_checkbox('".$index."', '1', (isset(\$".$this->model_name."->".$index.")) ? \$".$this->model_name."->".$index." : ''  )?> />&nbsp;<label class='labelforminline' for='".$index."'>".$value['label']." ".(($value['required'] == "TRUE") ? "<span class='required'>*</span>" : "") ."</label>
	<?php echo form_error('".$index."'); ?>
</p>
";

		break;

		case 'select':
$data .="
<p>
	<label class='labelform' for='".$index."'>".ucfirst($index)." ".(($value['required'] == "TRUE") ? "<span class='required'>*</span>" : "") ."</label>

	<select name='".$index."' id='".$index."'>
		<option value=''><?=lang('web_choose_option')?></option>";

		foreach($value['options'] as $index2=>$value2)
		{
				$data .= $this->sl.$this->tab."<option value='".$value2['value']."' <?= preset_select('".$index."', '".$value2['value']."', (isset(\$".$this->model_name."->".$index.")) ? \$".$this->model_name."->".$index." : ''  ) ?>>".$value2['text']."</option>";
		}
		

$data .="		
	</select>
	<?php echo form_error('".$index."'); ?>
</p>
";
		break;


		case 'selectbd':
$data .="
<p>
	<label class='labelform' for='".$index."'>".ucfirst($index)." ".(($value['required'] == "TRUE") ? "<span class='required'>*</span>" : "") ."</label>
	<select name='".$index."' id='".$index."' size='".$value['size']."'>
		<option value=''><?=lang('web_choose_option')?></option>
		<?php foreach (\$array_".strtolower($value['options']['model'])." as \$item): ?>
			<option value=\"<?=\$item->".$value['options']['field_value'].";?>\" <?= preset_select('".$index."', \$item->".$value['options']['field_value'].", (isset(\$".$this->model_name."->".$index.")) ? \$".$this->model_name."->".$index." : ''  ) ?>><?=\$item->".$value['options']['field_text'].";?></option>
		<?php endforeach ?>
		";		

$data .="		
	</select>
	<?php echo form_error('".$index."'); ?>
</p>
";
		break;

		case 'radio':
$data .= "
<p>
	<label class='labelform'>".ucfirst($index)." ".(($value['required'] == "TRUE") ? "<span class='required'>*</span>" : "") ."</label>";

	$c = 0;
	foreach($value['options'] as $index2 => $value2)
	{
		$data .= $this->sl.$this->tab."<input type='radio' name='".$index."' id='".$index."_$c' value='".$value2['value']."' <?=preset_radio('".$index."', '".$value2['value']."', (isset(\$".$this->model_name."->".$index.")) ? \$".$this->model_name."->".$index." : '".$value['checked']."'  );?> > <label class='labelforminline' for='".$index."_$c'> ".$value2['label']." </label>";
		$c++;
	}

$data .= "
	<?php echo form_error('".$index."'); ?>
</p>
";

		break;

		case 'datepicker':
$data .="
<p>
	<label class='labelform' for='".$index."'>".ucfirst($index)." ".(($value['required'] == "TRUE") ? "<span class='required'>*</span>" : "") ."</label>
	<input id='".$index."' type='text' name='".$index."' maxlength='' class='datepicker' value=\"<?php echo set_value('".$index."', (isset(\$".$this->model_name."->".$index.")) ? \$".$this->model_name."->".$index."->format('d-m-Y') : ''); ?>\"  />
	<?php echo form_error('".$index."'); ?>
</p>
";
		break;

		case 'image':

			if ( $value['multilanguage'] == "TRUE")
			{
				foreach ($this->languages as $prefix=>$language)
				{
$data .= "
<p>
	<label class='labelform' for='".$index."_".$prefix."'><?=lang( (\$updType == 'edit')  ? \"web_image_edit\" : \"web_image_create\" )?> [".$index." ($prefix)] ".(($value['required'] == "TRUE") ? "<span class='required'>*</span>" : "") ."</label>

	<?php if (\$updType == 'edit'): ?>
		<p> <img src='/public/uploads/".$this->controller_name."/img/thumbs/<?=\$".$this->model_name."->".$index."_".$prefix."?>' /> </p>
	<?php endif ?>

	<input id='".$index."_".$prefix."' type='file' name='".$index."_".$prefix."' />

	<br/><?php echo form_error('".$index."_".$prefix."'); ?>
	<?php  echo ( isset(\$error_".$index."_".$prefix.")) ?  \$error_".$index."_".$prefix."  : \"\"; ?>
</p>
";
				}

			}
			else
			{
$data .= "
<p>
	<label class='labelform' for='".$index."'><?=lang( (\$updType == 'edit')  ? \"web_image_edit\" : \"web_image_create\" )?> (".$index.") ".(($value['required'] == "TRUE") ? "<span class='required'>*</span>" : "") ."</label>

	<?php if (\$updType == 'edit'): ?>
		<p> <img src='/public/uploads/".$this->controller_name."/img/thumbs/<?=\$".$this->model_name."->".$index."?>' /> </p>
	<?php endif ?>

	<input id='".$index."' type='file' name='".$index."' />

	<br/><?php echo form_error('".$index."'); ?>
	<?php  echo ( isset(\$error_".$index.")) ?  \$error_".$index."  : \"\"; ?>
</p>
";
			}

		break;

		case 'file':

			if ( $value['multilanguage'] == "TRUE")
			{
				foreach ($this->languages as $prefix=>$language)
				{
$data .= "
<p>
	<label class='labelform' for='".$index."_".$prefix."'><?=lang( (\$updType == 'edit')  ? \"web_file_edit\" : \"web_file_create\" )?> [".$index." ($prefix)] ".(($value['required'] == "TRUE") ? "<span class='required'>*</span>" : "") ."</label>

	<input id='".$index."_".$prefix."' type='file' name='".$index."_".$prefix."' />

	<?php if (\$updType == 'edit'): ?>
		<p> <a href='/public/uploads/".$this->controller_name."/files/<?=\$".$this->model_name."->".$index."_".$prefix."?>' />Download actual file ".$index." (".$prefix.")</a> </p>
	<?php endif ?>

	<br/><?php echo form_error('".$index."_".$prefix."'); ?>
	<?php  echo ( isset(\$error_".$index."_".$prefix.")) ?  \$error_".$index."_".$prefix."  : \"\"; ?>
</p>
";
				}

			}
			else
			{
$data .= "
<p>
	<label class='labelform' for='".$index."'><?=lang( (\$updType == 'edit')  ? \"web_file_edit\" : \"web_file_create\" )?> (".$index.") ".(($value['required'] == "TRUE") ? "<span class='required'>*</span>" : "") ."</label>

	<input id='".$index."' type='file' name='".$index."' />

	<?php if (\$updType == 'edit'): ?>
	<a href='/public/uploads/".$this->controller_name."/files/<?=\$".$this->model_name."->".$index."?>' />Download actual file (".$index.")</a>
	<?php endif ?>

	<br/><?php echo form_error('".$index."'); ?>
	<?php  echo ( isset(\$error_".$index.")) ?  \$error_".$index."  : \"\"; ?>
</p>
";
			}

		break;

		case 'hidden':

$data .= "
	<input id='".$index."' type='hidden' name='".$index."' value='<?=\$".$index."?>'/>
";			

		break;
	}
}


$data .= "
<p>
    <?php echo form_submit( 'submit', (\$updType == 'edit') ? lang('web_edit') : lang('web_add'), ((\$updType == 'create') ? \"id='submit' class='bcreateform'\" : \"id='submit' class='beditform'\")); ?>
</p>

<?=form_hidden('page',set_value('page', \$page)) ?>

<?php if (\$updType == 'edit'): ?>
	<?=form_hidden('id',\$".$this->model_name."->id) ?>
<?php endif ?>

<?php echo form_close(); ?>
";

		$this->create_folder_if_no_exists(APPPATH.'views/'.$this->controller_name);

		if ( $this->save_file('create', "views/".$this->controller_name."/", trim( $data ) ) === TRUE )
			return TRUE;
		else
		{
			$this->errors = lang('scaffolds_error_file')." view/".$this->controller_name."/create.php";
			return FALSE;
		}
	}


	private function create_view_list()
	{
		$data = "";

foreach ($this->arrayjson as $index => $value )
{
		switch ($value['type'])
		{
			case 'text':
			case 'textarea':
			case 'image':

				if ( $value['multilanguage'] == "TRUE")
				{
					$data .= "<?php \$".$index."_with_actual_language = '".$index."_'.\$this->config->item('prefix_language'); ?>".$this->sl;
				}

			break;
		
		}
}


$data .= "
<div id='content-top'>
    <h2><?=lang('web_list_of', array(':name' => '".ucfirst($this->controller_name)."'))?></h2>
   
    <a href='/admin/".$this->controller_name."/create/".(($this->there_is_a_relational_field) ? "<?=\$$this->relational_field?>/" : "")."<?=\$page?>' class='bcreate'><?=lang('web_create_t', array(':name' => '".$this->model_name."'))?></a>
  
    <span class='clearFix'>&nbsp;</span>
</div>

<?php if (\$".$this->controller_name."): ?>

<div class='clear'></div>

	<table class='ftable' cellpadding='5' cellspacing='5'>

		<thead>";

			foreach ($this->arrayjson as $index => $value )
			{
				switch ($value['type'])
				{
					case 'text':
					case 'textarea':
					case 'image':
					case 'file':

			$data .="
			<th>".ucfirst($index)."</th>";

					break;
				}
			}


			$data .="
			<th colspan='2'><?=lang('web_options')?></th>
		</thead>

		<tbody>
			<?php foreach (\$".$this->controller_name." as \$".$this->model_name."): ?>
				
				<tr>
";

					foreach ($this->arrayjson as $index => $value )
			    	{
			      		switch ($value['type'])
			      		{
			        		case 'text':
			        		case 'textarea':

		        			if ( $value['multilanguage'] == "TRUE")
		        			{
		        				$data .= $this->tabx5."<td><?=\$".$this->model_name."->\$".$index."_with_actual_language;?></td>".$this->sl;
		        			}
		        			else
		        			{
		        				$data .= $this->tabx5."<td><?=\$".$this->model_name."->".$index.";?></td>".$this->sl;
		        			}

			        		break;


			        		case 'image':

		        			if ( $value['multilanguage'] == "TRUE")
		        			{
		        				$data .= $this->tabx5."<td><img src='/public/uploads/".$this->controller_name."/img/thumbs/<?=\$".$this->model_name."->\$".$index."_with_actual_language?>' border='0' /></td>".$this->sl;	
		        			}
		        			else
		        			{
		        				$data .= $this->tabx5."<td><img src='/public/uploads/".$this->controller_name."/img/thumbs/<?=\$".$this->model_name."->".$index."?>' border='0' /></td>".$this->sl;
		        			}

			        		break;


			        		case 'file':

		        			if ( $value['multilanguage'] == "TRUE")
		        			{
		        				$data .= $this->tabx5."<td>";
		        				foreach ($this->languages as $prefix=>$language)
								{
		        					$data .= $this->tabx6."<a href='/public/uploads/".$this->controller_name."/files/<?=\$".$this->model_name."->".$index."_".$prefix."?>'/>Download ".$index." (".$prefix.")</a></br></br>".$this->sl;	
		        				}
		        				$data .= $this->tabx5."</td>";
		        			}
		        			else
		        			{
		        				$data .= $this->tabx5."<td><a href='/public/uploads/".$this->controller_name."/files/<?=\$".$this->model_name."->".$index."?>' />Download file (".$index.")</a></td>".$this->sl;
		        			}

			        		break;
			        	}
			        }

				$data .= $this->tabx5."<td width='60'><a class='ledit' href='/admin/".$this->controller_name."/edit/<?=\$".$this->model_name."->id?>/".(($this->there_is_a_relational_field)  ? "<?=\$$this->model_name->".$this->relational_field."?>/" : "")."<?=\$page?>'><?=lang('web_edit')?></a></td>
					<td width='60'><a class='ldelete' onClick=\"return confirm('<?=lang('web_confirm_delete')?>')\" href='/admin/".$this->controller_name."/delete/<?=\$".$this->model_name."->id?>/<?=\$page?>'><?=lang('web_delete')?></a></td>
				</tr>
				
			<?php endforeach ?>
		</tbody>

	</table>

	<?php echo \$links; ?>

<?php else: ?>

	<p class='text'><?=lang('web_no_elements');?></p>

<?php endif ?>

		";

		$this->create_folder_if_no_exists(APPPATH.'views/'.$this->controller_name);

		if ( $this->save_file('list', "views/".$this->controller_name."/", trim( $data ) ) === TRUE )
			return TRUE;
		else
		{
			$this->errors = lang('scaffolds_error_file')." view/".$this->controller_name."/list.php";
			return FALSE;		
		}
	}


	private function modify_routes()
	{
		$data = $this->sl.$this->sl;
		$data .="//routes para ".$this->controller_name.$this->sl;
		$data .= "\$route['admin/".$this->controller_name."/(:num)'] = 'admin/".$this->controller_name."/index/$1';";

		if ($this->there_is_a_relational_field)
		{
			$data .= $this->sl."\$route['admin/".$this->controller_name."/(:num)/(:num)'] = 'admin/".$this->controller_name."/index/$1/$2';";
		}

		if ( $this->save_file('routes', "config/", $data, 'a' ) === TRUE )
			return TRUE;
		else
		{
			$this->errors = lang('scaffolds_error_modify')." config/routes.php";
			return FALSE;
		}
	}


	private function modify_menu()
	{
		//No modificamos menu si es relacion
		if ($this->there_is_a_relational_field)
			return TRUE;


		$data = $this->sl.$this->sl;
		$data .= "<?php  \$mactive = (\$this->uri->rsegment(1) == '".$this->controller_name."')  ? \"class='selected'\" : \"\" ?>".$this->sl;
		$data .= "<li <?=\$mactive?>><a href=\"/admin/".$this->controller_name."/\" style=\"background-position: 0px 0px;\">".ucfirst($this->controller_name)."</a></li>";

		if ( $this->save_file('_menu', "views/partials/", $data, 'a' ) === TRUE )
			return TRUE;
		else
		{
			$this->errors = lang('scaffolds_error_modify')." config/routes.php";
			return FALSE;
		}
	}


	private function add_relational_link_to_list()
	{
		$file = file(APPPATH."views/".$this->relational_controller."/list.php" );

		/*
		$tds = array_keys( $file, '<td' );
		$pos_to_insert = $tds[count($tds)-1];
		*/
		$pos = count($file)-1;
		$tds_found = 0;
		while( $pos > 0 and $tds_found < 2 )
		{
			if( strpos($file[$pos],'<td') !== FALSE )
			{
				$tds_found++;
			}
			$pos--;
		}

		for( $i=count($file);$i>$pos;$i--)
		{
			$file[$i] = $file[($i-1)];
		}

		$file[$pos+1] = $this->tabx5."<td><a href=\"/admin/".$this->controller_name."/<?=\$".$this->relational_model."->id?>\">".ucfirst($this->controller_name)." (<?= ".$this->model_name_for_calls."::count(array('conditions' => array('".$this->relational_field." = ?', \$".$this->relational_model."->id)) )?>) </a></td>".$this->sl;

		$result = file_put_contents(APPPATH."views/".$this->relational_controller."/list.php" , $file );


		if ( $result )
			return TRUE;
		else
		{
			$this->errors = "Error modificando lista";
			return FALSE;
		}
	}


	private function create_folder_if_no_exists($path)
	{
		if (@mkdir($path))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}


	private function save_file($filename, $path, $data, $mode = "w")
	{
		$file = fopen(APPPATH.$path.$filename.".php" , $mode);
      
		if ($file)
		{
			$result = fputs ($file, $data);
		}

		fclose ($file);

		if ($result)
			return TRUE;
		else
		{
			$this->errors = lang('scaffolds_error_file')." ".APPPATH.$path.$filename.".php";
			return FALSE;
		}
	}

}
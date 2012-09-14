<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Router extends CI_Router {


	function __construct()
	{	
		parent::__construct();	
	}


	/**
	 * Set the Route
	 *
	 * This function takes an array of URI segments as
	 * input, and sets the current class/method
	 *
	 * @access	private
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	function _set_request($segments = array())
	{
		$segments = $this->_validate_request($segments);

		if (count($segments) == 0)
		{
			return $this->_set_default_controller();
		}

		//modificamos 
		$real_name_class = $this->get_class_name_controller_from_translation($segments[0]);		
		$this->set_class($real_name_class);


		if (isset($segments[1]))
		{
			$real_name_method = $this->get_method_name_from_translation($segments[0], $segments[1]);

			// A standard method request
			$this->set_method($real_name_method);
		}
		else
		{
			// This lets the "routed" segment array identify that the default
			// index method is being used.
			$segments[1] = 'index';
		}

		// Update our "routed" segment array to contain the segments.
		// Note: If there is no custom routing, this array will be
		// identical to $this->uri->segments
		$this->uri->rsegments = $segments;
	}




		// --------------------------------------------------------------------

	/**
	 * Get the real name of the controller to load it. It´s for transltation purposes
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function get_class_name_controller_from_translation($class_name){
		

		global $cn_trans;

		// Check that the controller_translations archive is located in language folder
		$file = APPPATH.'language/controller_translations.php';
		
		if ((!is_file($file)) or(!is_readable($file)))
		   die("Cannot Include Controller Translations names file");

		require_once($file);

		// Obtain the real class name from the array of controller languages

		if ( isset( $cn_trans[$this->config->item('language')][$class_name] ) ){
			return $cn_trans[$this->config->item('language')][$class_name];
		} else {
			return $class_name;
		}

	}


		// --------------------------------------------------------------------

	/**
	 * Get the real name of the method of a controller to load it. It´s for transltation purposes
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function get_method_name_from_translation($class_name, $method_name){
		

		global $mn_trans;

		// Check that the controller_translations archive is located in language folder
		$file = APPPATH.'language/method_translations.php';
		
		if ((!is_file($file)) or(!is_readable($file)))
		   die("Cannot Include Method Translations names file");

		require_once($file);

		// Obtain the real class name from the array of controller languages

		if ( isset( $mn_trans[$this->config->item('language')][$class_name][$method_name] ) ){
			return $mn_trans[$this->config->item('language')][$class_name][$method_name];
		} else {
			return $method_name;
		}

	}



		// --------------------------------------------------------------------

	/**
	 * Validates the supplied segments.  Attempts to determine the path to
	 * the controller.
	 *
	 * @access	private
	 * @param	array
	 * @return	array
	 */
	function _validate_request($segments)
	{
		if (count($segments) == 0)
		{
			return $segments;
		}

		$real_name_class = $this->get_class_name_controller_from_translation($segments[0]);

		// Does the requested controller exist in the root folder?
		if (file_exists(APPPATH.'controllers/'.$real_name_class.'.php'))
		{
			return $segments;
		}


		// Is the controller in a sub-folder?
		if (is_dir(APPPATH.'controllers/'.$segments[0]))
		{
			$real_name_class = $this->get_class_name_controller_from_translation($segments[1]);

			// Set the directory and remove it from the segment array
			$this->set_directory($segments[0]);
			$segments = array_slice($segments, 1);

			if (count($segments) > 0)
			{
				// Does the requested controller exist in the sub-folder?
				if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$real_name_class.'.php'))
				{	
					if ( ! empty($this->routes['404_override']))
					{
						$x = explode('/', $this->routes['404_override']);

						$this->set_directory('');
						$this->set_class($x[0]);
						$this->set_method(isset($x[1]) ? $x[1] : 'index');

						return $x;
					}
					else
					{
						show_404($this->fetch_directory().$segments[0]);
					}
				}
			}
			else
			{
				// Is the method being specified in the route?
				if (strpos($this->default_controller, '/') !== FALSE)
				{
					$x = explode('/', $this->default_controller);

					$this->set_class($x[0]);
					$this->set_method($x[1]);
				}
				else
				{
					$this->set_class($this->default_controller);
					$this->set_method('index');
				}

				// Does the default controller exist in the sub-folder?
				if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$this->default_controller.'.php'))
				{
					$this->directory = '';
					return array();
				}

			}

			return $segments;
		}


		// If we've gotten this far it means that the URI does not correlate to a valid
		// controller class.  We will now see if there is an override
		if ( ! empty($this->routes['404_override']))
		{
			$x = explode('/', $this->routes['404_override']);

			$this->set_class($x[0]);
			$this->set_method(isset($x[1]) ? $x[1] : 'index');

			return $x;
		}


		// Nothing else to do at this point but show a 404
		show_404($segments[0]);
	}

}
<?php
/**
 * Anchor Link with Translations
 *
 * Creates an anchor based on the local URL with translations of method name and controller name
 *
 * @access	public
 * @param	string	the URL
 * @param	string	the link title
 * @param	mixed	any attributes
 * @return	string
 */
if ( ! function_exists('lang_anchor'))
{
	function lang_anchor($controller = '', $method = FALSE, $params = FALSE, $title = FALSE, $attributes = FALSE)
	{
		$title = (string) $title;

		$CI =& get_instance();

		$lng = $CI->config->item('language');
		$base_url = $CI->config->item('base_url')."/";
		$site_url = $base_url;

		if ($controller){
			$trans_controller = get_class_name_controller_from_translation($controller, $lng);
			$site_url .= $trans_controller."/";

		}

		if ($method){
			$trans_method = get_method_name_from_translation($trans_controller, $method, $lng);
			$site_url .= $trans_method."/";
		}


		if ($params){
			$site_url .= $params."/";
		}
	

		if ($title == '')
		{
			$title = $site_url;
		}

		if ($attributes != '')
		{
			$attributes = _parse_attributes($attributes);
		}

		return '<a href="'.$site_url.'"'.$attributes.'>'.$title.'</a>';
	}
}


/**
 * Parse out the attributes
 *
 * Some of the functions use this
 *
 * @access	private
 * @param	array
 * @param	bool
 * @return	string
 */
if ( ! function_exists('_parse_attributes'))
{
	function _parse_attributes($attributes, $javascript = FALSE)
	{
		if (is_string($attributes))
		{
			return ($attributes != '') ? ' '.$attributes : '';
		}

		$att = '';
		foreach ($attributes as $key => $val)
		{
			if ($javascript == TRUE)
			{
				$att .= $key . '=' . $val . ',';
			}
			else
			{
				$att .= ' ' . $key . '="' . $val . '"';
			}
		}

		if ($javascript == TRUE AND $att != '')
		{
			$att = substr($att, 0, -1);
		}

		return $att;
	}
}


		// --------------------------------------------------------------------

	/**
	 * Get the real name of the controller to load it. It´s for transltation purposes
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function get_class_name_controller_from_translation($class_name, $lng){
		

		global $cn_trans;

		// Check that the controller_translations archive is located in language folder
		$file = APPPATH.'language/controller_translations.php';
		
		if ((!is_file($file)) or(!is_readable($file)))
		   die("Cannot Include Controller Translations names file");

		require_once($file);

		// Obtain the real class name from the array of controller languages

		if ( array_search($class_name, $cn_trans[$lng]) )
		{
			return array_search($class_name, $cn_trans[$lng]);
		}
		else
		{
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
	function get_method_name_from_translation($class_name, $method_name, $lng){
		

		global $mn_trans;

		// Check that the controller_translations archive is located in language folder
		$file = APPPATH.'language/method_translations.php';
		
		if ((!is_file($file)) or(!is_readable($file)))
		   die("Cannot Include Method Translations names file");

		require_once($file);

		// Obtain the real class name from the array of controller languages

		if ( array_search($method_name, $mn_trans[$lng][$class_name]) )
		{
			return array_search($method_name, $mn_trans[$lng][$class_name]);
		}
		else
		{
			return $method_name;
		}

	}
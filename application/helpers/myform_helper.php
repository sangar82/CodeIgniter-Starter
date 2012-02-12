<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package        CodeIgniter
 * @author        Rick Ellis
 * @copyright    Copyright (c) 2006, pMachine, Inc.
 * @license        http://www.codeignitor.com/user_guide/license.html
 * @link        http://www.codeigniter.com
 * @since        Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Code Igniter My Form Helpers
 *
 * @package        CodeIgniter
 * @subpackage    Helpers
 * @category    Helpers
 * @author        Travis Cable aka Nexus Rex
 * @link        
 */

// ------------------------------------------------------------------------

// ------------------------------------------------------------------------

// ------------------------------------------------------------------------

/**
 * Preset Select
 *
 * Let's you preset the selected value of a checkbox field via info from a database
 * and allows info the in the POST array to override.
 *
 * @access    public
 * @param    string
 * @param    string
 * @param    string
 * @return    string
 */
if ( ! function_exists('preset_select'))
{
    function preset_select($field = '', $value = '', $preset_value = '')
    {
        if ($value == $preset_value)
        {
            return set_select($field, $preset_value, TRUE);
        }
        else
        {
            return set_select($field, $value);
        }
    }
}

// ------------------------------------------------------------------------

/**
 * Preset Checkbox
 *
 * Let's you preset the selected value of a checkbox field via info from a database
 * and allows info the in the POST array to override.
 *
 * @access    public
 * @param    string
 * @param    string
 * @param    string
 * @return    string
 */
if ( ! function_exists('preset_checkbox'))
{
    function preset_checkbox($field = '', $value = '', $preset_value = '')
    {
        if ($value == $preset_value)
        {
            return set_checkbox($field, $preset_value, TRUE);
        }
        else
        {
            return set_checkbox($field, $value);
        }
    }
}

// ------------------------------------------------------------------------

/**
 * Preset Radio
 *
 * Let's you preset the selected value of a radio field via info from a database
 * and allows info the in the POST array to override.
 * If Form Validation is active it retrieves the info from the validation class
 *
 * @access    public
 * @param    string
 * @param    string
 * @param    string
 * @return    string
 */
if ( ! function_exists('preset_radio'))
{
    function preset_radio($field = '', $value = '', $preset_value = '')
    {
        if ($value == $preset_value)
        {
            return set_radio($field, $preset_value, TRUE);
        }
        else
        {
            return set_radio($field, $value);
        }
    }
}

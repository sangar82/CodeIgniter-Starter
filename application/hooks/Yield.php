<?php
// Based on http://codeigniter.com/forums/viewthread/57902/#284919

/**
 * Yield
 *
 * Adds layout support :: Similar to RoR <%= yield =>
 *
 * Just output the variable $yield in your layout file which should
 * be located in application/views/layouts/
 *
 * By default if no layout is specified 'default.php' will be used.
 * You can specify a layout by setting $this->layout = 'file.php' in
 * your controller. If you don't want to use the layout then set it to
 * FALSE.
 *
 * If you need to populate the layout with other variables then set
 * $this->layout_data to an array.
 *
 * To enable put this in a "hooks" directory in under an "application"
 * directory. Then add the following to your hooks config file:
 *
 * $hook['display_override'][] = array(
 *  'class'    => 'Yield',
 *  'function' => 'run',
 *  'filename' => 'Yield.php',
 *  'filepath' => 'hooks'
 * );
 *
 * Finally make sure to enable hooks in your config.
 */
class Yield {
  function run() {
    global $OUT;
  
    $CI =& get_instance();
    $output = $CI->output->get_output();

    if( !isset($CI->layout) ) $CI->layout = 'default';
    if( $CI->layout !== FALSE ) {
      if( !preg_match('/(.+).php$/', $CI->layout) ) $CI->layout .= '.php';

      $layout = APPPATH . 'layouts/' . $CI->layout;
      if( file_exists($layout) ) {
        $data = isset($CI->layout_data) ? $CI->layout_data : array();
        $data['yield'] = $output;
        $CI->load->vars($data);
        $output = $CI->load->file($layout, true);
      }
    }
    
    $OUT->_display($output);
  }
}  

# EOF

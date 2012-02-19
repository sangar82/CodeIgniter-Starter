<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function initialize_php_activerecord() {
    if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300)
        die('PHP ActiveRecord requires PHP 5.3 or higher');

    define('PHP_ACTIVERECORD_VERSION_ID','1.0');

    // This constant allows you to prepend your file to the autoload stack rather than append it.
    if (!defined('PHP_ACTIVERECORD_AUTOLOAD_PREPEND')) {
        define('PHP_ACTIVERECORD_AUTOLOAD_PREPEND',true);
    }

    // This line simply states that if we haven't opted to disable the autoloader, add it to the autoload stack
    if (!defined('PHP_ACTIVERECORD_AUTOLOAD_DISABLE')) {
        // Because we're prepending - we need to load the library after the models
        spl_autoload_register('activerecord_autoload', false, PHP_ACTIVERECORD_AUTOLOAD_PREPEND);
        spl_autoload_register('activerecord_lib_autoload', false, PHP_ACTIVERECORD_AUTOLOAD_PREPEND);
    }

    // The Utils.php file has some namespaced procedural functions, so we must require it manually.
    require 'lib/Utils'.EXT;

    // Include the CodeIgniter database config so we can access the variables declared within
    include(APPPATH.'config/database'.EXT);

    $dsn = array();
    if ($db) {
        foreach ($db as $name => $db_values) {
            // Convert to dsn format
            $dsn[$name] = $db[$name]['dbdriver'] .
                '://'   . $db[$name]['username'] .
                ':'     . $db[$name]['password'] .
                '@'     . $db[$name]['hostname'] .
                '/'     . $db[$name]['database'];
        }
    } 

    // Initialize ActiveRecord
    ActiveRecord\Config::initialize(function($cfg) use ($dsn, $active_group){
        $cfg->set_model_directory(APPPATH.'models');
        $cfg->set_connections($dsn);
        $cfg->set_default_connection($active_group);
    });
}


function activerecord_lib_autoload($class_name)
{
    $lib_path = APPPATH.'third_party/php-activerecord/lib/';

    if (strpos($class_name, 'ActiveRecord') !== FALSE) 
    {
        $class = substr($class_name, strpos($class_name, '\\')+1);

        if (file_exists($lib_path.$class.EXT))
            require $lib_path.$class.EXT;
    }
}

function activerecord_autoload($class_name)
{
    $path = ActiveRecord\Config::instance()->get_model_directory();
    $root = realpath(isset($path) ? $path : '.');

    if (($namespaces = ActiveRecord\get_namespaces($class_name)))
    {
        $class_name = array_pop($namespaces);
        $directories = array();

        foreach ($namespaces as $directory)
            $directories[] = $directory;

        $root .= DIRECTORY_SEPARATOR . implode($directories, DIRECTORY_SEPARATOR);
    }

    $file = "$root/$class_name".EXT;

    if (file_exists($file))
        require $file;
}
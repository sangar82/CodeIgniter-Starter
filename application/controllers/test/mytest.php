<?php

require_once(APPPATH . '/controllers/test/Toast.php');

class Mytest extends Toast
{
	function __construct() // change constructor method name
	{
	   parent::__construct(__FILE__); // Remember this
	}


	function test_some_action()
	{
		// Test code goes here
		$my_var = 2 + 2;
		$this->_assert_equals($my_var, 4);
	}

	function test_some_other_action()
	{
		// Test code goes here
		$my_var = true;
		$this->_assert_false($my_var);
	}
}
	
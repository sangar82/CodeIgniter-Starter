<?php
class Tag extends ActiveRecord\Model {
    	
    static $has_many = array(
    	array('posttags'),
      	array('posts',  'through' => 'posttags')
    );
    
}
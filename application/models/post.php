<?php
class Post extends ActiveRecord\Model {


    //static $attr_accessible = array('title', 'body');

	static $belongs_to = array(
	    array('user')
	);

	static $has_many = array(
		array('post_tags'),
      	array('tags', 'through' => 'posttags')
    );

    static $validates_uniqueness_of = array(
        array("title", 'message' => 'Title most be unique')
    );


    static function all_paginate($limit, $page){

        $offset = $limit * ( $page - 1) ;
    
    	$result = Post::find('all', array('limit' => $limit, 'offset' => $offset ) );

    	if ($result) {
    		return $result;
    	}else{
    		return FALSE;
    	}

    }
    
}
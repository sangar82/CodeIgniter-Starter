<?php
class PostTag extends ActiveRecord\Model {

	static $table_name = "posts_tags";
    	
    static $belongs_to = array(
      array('post'),
      array('tag')
    );
    
}
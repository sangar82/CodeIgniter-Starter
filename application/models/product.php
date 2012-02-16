<?php
class Product extends ActiveRecord\Model {

	static $belongs_to = array(
	    array('category')
	);


    /**
    * Return values of database with pagination
    *	
    * @return mixed
    */
	static function paginate($category_id, $limit, $page){

		$offset = $limit * ( $page - 1) ;

		$result = Product::find('all', array('conditions' => 'category_id = '.$category_id.'', 'limit' => $limit, 'offset' => $offset ) );

		if ($result) {
			return $result;
		}else{
			return FALSE;
		}
	}
	

	/**
    * Return values of database with pagination
    *	
    * @return mixed
    */
	static function paginate_all($limit, $page){

		$offset = $limit * ( $page - 1) ;

		$result = Product::find('all', array('limit' => $limit, 'offset' => $offset ) );

		if ($result) {
			return $result;
		}else{
			return FALSE;
		}
	}
	
}
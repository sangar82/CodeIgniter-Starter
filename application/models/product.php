<?php
class Product extends ActiveRecord\Model {

    /**
    * Return values of database with pagination
    *	
    * @return mixed
    */
	static function paginate($limit, $page){

		$offset = $limit * ( $page - 1) ;

		$result = Product::find('all', array('limit' => $limit, 'offset' => $offset ) );

		if ($result) {
			return $result;
		}else{
			return FALSE;
		}
	}


}
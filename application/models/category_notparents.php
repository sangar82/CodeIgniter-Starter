<?php
class Category extends ActiveRecord\Model {

	static $has_many = array(
		array('products')
    );

    /**
    * Return values of database with pagination
    *	
    * @return mixed
    */
	static function paginate($parent_id, $limit, $page){

		if ($parent_id){
			$parent = $parent_id;
		}
		else
		{
			$parent = "all";
		}

		$offset = $limit * ( $page - 1) ;

		$result = Category::find( $parent, array('limit' => $limit, 'offset' => $offset ) );

		if ($result) {
			return $result;
		}else{
			return FALSE;
		}
	}


}
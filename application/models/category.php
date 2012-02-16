<?php
class Category extends ActiveRecord\Model {

	static $has_many = array(
		array('products'),
		array('categories')
    );

    static $belongs_to = array(
	    array('category')
	);

	public function get_parent() {
        return $this->category;
    }

    public function get_children() {
        return $this->categories;
    }


    /**
    * Return values of database with pagination
    *	
    * @return mixed
    */
	static function findby($category_id)
	{
		if ($category_id){
			$result = Category::find('all', array('conditions' => 'category_id = '.$category_id.'', 'order' => 'orden asc') );
		}
		else
		{
			$result = Category::find('all', array( 'conditions' => 'category_id is null', 'order' => 'orden asc' ));
		}	

		if ($result) {
			return $result;
		}else{
			return FALSE;
		}
	}


	static function get_formatted_combo($category_id = NULL, $space = '-'){

		global $combo ;
		
		if ($category_id)
			$categories = Category::find('all', array('conditions' => "category_id = ".$category_id ));
		else
			$categories = Category::find('all', array('conditions' => "category_id is null" ));


		foreach ($categories as $category) {

			$combo[] =  (object) array( "id" => $category->id, "name" => $space." ".$category->name );
			
			if ( $category->categories )
			{
				self::get_formatted_combo($category->id, $space.'----');
			}

		}

		return $combo;

	}

	static function reorder_rows($order = 1, $category_id = NULL )
	{
		$conn = ActiveRecord\ConnectionManager::get_connection("development");  
		
		if ($category_id)
			 $result = $conn->query('UPDATE categories SET orden = orden - 1 WHERE orden > '.$order.' and category_id = '. $category_id);
		else
			 $result = $conn->query('UPDATE categories SET orden = orden - 1 WHERE orden > '.$order.' and category_id is null');

		return $result;		 
	}


	static function change_orders_categories($category, $order = 1){

		$conn = ActiveRecord\ConnectionManager::get_connection("development");

		$result = $conn->query('UPDATE categories SET orden = '.$order.' WHERE  id = '. $category);

		return $result;
	}


}
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
			$result = Category::find( 'all', array('conditions' => 'category_id = '.$category_id.'') );
		}
		else
		{
			$result = Category::find( 'all',array( 'conditions' => 'category_id is null') );
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


}
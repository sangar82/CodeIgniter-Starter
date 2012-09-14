<?php
class Category extends ActiveRecord\Model {

	/*
	before_save: called before a model is saved
	before_create: called before a NEW model is to be inserted into the database
	before_update: called before an existing model has been saved
	before_validation: called before running validators
	before_validation_on_create: called before validation on a NEW model being inserted
	before_validation_on_update: same as above except for an existing model being saved
	before_destroy: called after a model has been deleted

	after_save: called after a model is saved
	after_create: called after a NEW model has been inserted into the database
	after_update: called after an existing model has been saved
	after_validation: called after running validators
	after_validation_on_create: called after validation on a NEW model being inserted
	after_validation_on_update: same as above except for an existing model being saved
	after_destroy: called after a model has been deleted
	*/

	// Callbacks

	static $before_create = array('set_order');
	static $after_destroy = array('set_order_after_delete');



	// Relations

	static $has_many = array(
		array('products'),
		array('categories')
    );


    static $belongs_to = array(
	    array('category')
	);


	// Validations
	
	static $validates_presence_of = array(
      array('name', 'message'=>'The name can not be blank.')
    );


/*
	static $validates_uniqueness_of = array(
      array('name', 'message' => 'taken')
    );
*/

	// Methods

	public function get_parent() {
        return $this->category;
    }


    public function get_children() {
        return $this->categories;
    }


    public function set_order()
    {
    	if ($this->category_id)
    	{
    		if ($this->id)
    		{
    			$this->orden = Category::count( array('conditions' => 'category_id = '.$this->category_id.' AND id <> ' .$this->id .'') ) + 1;
    		}
    		else
    		{
    			$this->orden = Category::count( array('conditions' => 'category_id = '.$this->category_id.'') ) + 1;
    		}
    	}
    	else
    	{
    		if ($this->id)
    		{
    			$this->orden = Category::count(  array('conditions' => 'category_id is null AND id <> ' .$this->id .'' ) ) + 1;	
    		}
    		else
    		{
    			$this->orden = Category::count(  array('conditions' => 'category_id is null' ) ) + 1;	
    		}
    	}
    		
    }


    function set_order_after_delete()
    {
		$conn = ActiveRecord\ConnectionManager::get_connection("development");  
		
		if ($this->category_id)
			 $result = $conn->query('UPDATE categories SET orden = orden - 1 WHERE orden > '.$this->orden.' and category_id = '. $this->category_id);
		else
			 $result = $conn->query('UPDATE categories SET orden = orden - 1 WHERE orden > '.$this->orden.' and category_id is null');

		return $result;	    	
    }


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


	static function change_orders_categories($category, $order = 1)
	{

		$conn = ActiveRecord\ConnectionManager::get_connection("development");

		$result = $conn->query('UPDATE categories SET orden = '.$order.' WHERE  id = '. $category);

		return $result;
	}


	static function count_by_category($category_id = NULL)
	{
		if ($category_id)
		{
			return Category::count( array('conditions' => 'category_id = '.$category_id.'') );
		}
		else
		{
			return Category::count(  array('conditions' => 'category_id is null' ) );
		}
	}


	public function validate()
	{
		if ($this->category_id)
		{
			if ($this->id)
				$rows = Category::count( array('conditions' => array('category_id = ? AND name = ? AND id <> ?', $this->category_id, $this->name, $this->id)) );
			else
				$rows = Category::count( array('conditions' => array('category_id = ? AND name = ?', $this->category_id, $this->name)) );
		}
		else
		{
			if ($this->id)
				$rows = Category::count( array('conditions' => array('category_id is null AND name = ? AND id <> ?', $this->name, $this->id)) );
			else
				$rows = Category::count( array('conditions' => array('category_id is null AND name = ?', $this->name )) );
		}

		if ($rows)
		{
			$this->errors->add('name', "El nombre no es único en esta categoría");
		}
		else
		{
			return TRUE;
		}			
	}
}
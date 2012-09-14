<?php
class Product extends ActiveRecord\Model {

	static $belongs_to = array(
	    array('category')
	);

    public function get_children() {
        return $this->categories;
    }

		
	static $validates_presence_of = array(
	  array('name'),	
      array('description'),
      array('category_id'),
      array('image')
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


	public function validate()
	{
		if ( $this->id)
		{
			$rows = Product::count( array('conditions' => array('category_id = ? AND name = ? AND id <> ?', $this->category_id, $this->name, $this->id)) );
		}
		else
		{
			$rows = Product::count( array('conditions' => array('category_id = ? AND name = ? ', $this->category_id, $this->name)) );			
		}


		if ($rows)
		{
			$this->errors->add('name', "El nombre del producto no es único en esta categoría. Por favor, escribe otro nombre");
		}
	}
	
}
<?php
class Group extends ActiveRecord\Model {

	static $has_many = array(
		array('users' , 'through' => 'usersgroups'),
		array('usersgroups')
    );
	
	static $validates_presence_of = array(
		array('name')	
    );


	static function paginate_all($limit, $page)
	{
		$offset = $limit * ( $page - 1) ;

		$result = Group::find('all', array('limit' => $limit, 'offset' => $offset, 'order' => 'id DESC' ) );

		if ($result)
		{
			return $result;
		}
		else
		{
			return FALSE;
		}
	}


}
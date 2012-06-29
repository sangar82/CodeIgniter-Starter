<?php
class Users_groups extends ActiveRecord\Model
{
	static $table_name = 'users_groups';

	static $belongs_to = array(
		array('user'),
		array('group')
    );
}
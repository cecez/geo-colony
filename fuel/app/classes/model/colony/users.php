<?php

class Model_Colony_Users extends \Orm\Model
{
	
	protected static $_primary_key = array('colony_id', 'user_id');
	protected static $_properties = array(
		'colony_id',
		'user_id'
	);
}
?>
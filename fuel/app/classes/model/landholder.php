<?php

class Model_Landholder extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'name',
		'family',
		'origin',
		'created_at',
		'updated_at'
	);

	protected static $_has_many = array('plot_landholders');

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => false,
		),
	);
}

<?php

class Model_Plot_Coordinate extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'plot_id',
		'latitude',
		'longitude'
	);

	protected static $_belongs_to = array('plots');

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

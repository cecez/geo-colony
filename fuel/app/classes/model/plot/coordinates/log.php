<?php

class Model_Plot_Coordinates_Log extends \Orm\Model
{
	protected static $_properties = array(
		'plot_id',
		'revision_id',
		'old_value',
		'new_value'
	);
	
	protected static $_table_name = 'plot_coordinates_log';
	
	protected static $_primary_key = array('plot_id', 'revision_id');
	
}

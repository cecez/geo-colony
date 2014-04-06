<?php

class Model_Plot_Landholder_Log extends \Orm\Model
{
	protected static $_properties = array(
		'plot_landholder_id',
		'revision_id',
		'attribute',
		'old_value',
		'new_value'
	);
	
	protected static $_table_name = 'plot_landholders_log';
	
	protected static $_primary_key = array('plot_landholder_id', 'revision_id', 'attribute');
	
}

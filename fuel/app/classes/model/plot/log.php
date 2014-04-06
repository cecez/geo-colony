<?php

class Model_Plot_Log extends \Orm\Model
{
	protected static $_properties = array(
		'plot_id',
		'revision_id',
		'attribute',
		'old_value',
		'new_value'
	);
	
	protected static $_table_name = 'plots_log';
	
	protected static $_primary_key = array('plot_id', 'revision_id', 'attribute');
	
}

<?php

class Model_Trail_Log extends \Orm\Model
{
	protected static $_properties = array(
		'trail_id',
		'revision_id',
		'attribute',
		'old_value',
		'new_value'
	);
	
	protected static $_table_name = 'trails_log';
	
	protected static $_primary_key = array('trail_id', 'revision_id', 'attribute');
	
}

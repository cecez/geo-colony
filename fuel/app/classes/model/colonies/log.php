<?php

class Model_Colonies_Log extends \Orm\Model
{
	protected static $_properties = array(
		'colony_id',
		'revision_id',
		'attribute',
		'old_value',
		'new_value'
	);
	
	protected static $_table_name = 'colonies_log';
	
	protected static $_primary_key = array('colony_id', 'revision_id', 'attribute');
	
}

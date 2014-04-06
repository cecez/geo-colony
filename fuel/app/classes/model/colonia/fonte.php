<?php

class Model_Colonia_Fonte extends \Orm\Model
{
	protected static $_properties = array(
		'colonia_id',
		'fonte_id',
		'revisao_id',
		'pagina',
		'observacao'
	);
	
	protected static $_table_name = 'colonia_fonte';
	
	protected static $_primary_key = array('revisao_id');
	
}

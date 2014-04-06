<?php

class Model_Linha_Fonte extends \Orm\Model
{
	protected static $_properties = array(
		'linha_id',
		'fonte_id',
		'revisao_id',
		'pagina',
		'observacao'
	);
	
	protected static $_table_name = 'linha_fonte';
	
	protected static $_primary_key = array('revisao_id');
	
}

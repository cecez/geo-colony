<?php

class Model_Lote_Fonte extends \Orm\Model
{
	protected static $_properties = array(
		'lote_id',
		'fonte_id',
		'revisao_id',
		'pagina',
		'observacao'
	);
	
	protected static $_table_name = 'lote_fonte';
	
	protected static $_primary_key = array('revisao_id');
	
}

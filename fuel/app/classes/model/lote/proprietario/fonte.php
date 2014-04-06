<?php

class Model_Lote_Proprietario_Fonte extends \Orm\Model
{
	protected static $_properties = array(
		'loteproprietario_id',
		'fonte_id',
		'revisao_id',
		'pagina',
		'observacao'
	);
	
	protected static $_table_name = 'lote_proprietario_fonte';
	
	protected static $_primary_key = array('revisao_id');
	
}

<?php

class Model_Revision extends \Orm\Model
{
	
	const REVISAO_APROVADA = 1;
	
	protected static $_properties = array(
		'id',
		'user_id',
		'date',
		'approved',
		'usuario_reprovador_id',
		'data_reprovacao'
	);

	protected static $_belongs_to = array('users');
	
}

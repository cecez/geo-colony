<?php

class Model_Notificacao_Proprietario extends \Orm\Model
{
	protected static $_properties = array(
		'id_proprietario',
		'id_usuario',
	);
	
	protected static $_table_name = 'notificacao_proprietario';
	
	protected static $_primary_key = array('id_proprietario', 'id_usuario');
	
	public static function inscreve($idProprietario, $idUsuario) {
		// insere
		$o = new Model_Notificacao_Proprietario();
		$o->id_proprietario = $idProprietario;
		$o->id_usuario = $idUsuario;
		return $o->save();
	}
	
	public static function desinscreve($idProprietario, $idUsuario) {
		// deleta proprietario
		$o = Model_Notificacao_Proprietario::find(array($idProprietario, $idUsuario));
		return $o->delete();
	}
	
}

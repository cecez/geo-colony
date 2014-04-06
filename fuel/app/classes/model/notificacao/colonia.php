<?php

class Model_Notificacao_Colonia extends \Orm\Model
{
	protected static $_properties = array(
		'id_colonia',
		'id_usuario',
	);
	
	protected static $_table_name = 'notificacao_colonia';
	
	protected static $_primary_key = array('id_colonia', 'id_usuario');
	
	public static function inscreve($idColonia, $idUsuario) {
		// insere
		$o = new Model_Notificacao_Colonia();
		$o->id_colonia = $idColonia;
		$o->id_usuario = $idUsuario;
		return $o->save();
	}
	
	public static function desinscreve($idColonia, $idUsuario) {
		// deleta linha
		$o = Model_Notificacao_Colonia::find(array($idColonia, $idUsuario));
		return $o->delete();
	}
	
}

<?php

class Model_Notificacao_Linha extends \Orm\Model
{
	protected static $_properties = array(
		'id_linha',
		'id_usuario',
	);
	
	protected static $_table_name = 'notificacao_linha';
	
	protected static $_primary_key = array('id_linha', 'id_usuario');
	
	public static function inscreve($idLinha, $idUsuario) {
		// insere
		$o = new Model_Notificacao_Linha();
		$o->id_linha = $idLinha;
		$o->id_usuario = $idUsuario;
		return $o->save();
	}
	
	public static function desinscreve($idLinha, $idUsuario) {
		// deleta linha
		$o = Model_Notificacao_Linha::find(array($idLinha, $idUsuario));
		return $o->delete();
	}
	
}

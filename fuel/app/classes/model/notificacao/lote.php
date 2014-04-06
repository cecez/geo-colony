<?php

class Model_Notificacao_Lote extends \Orm\Model
{
	protected static $_properties = array(
		'id_lote',
		'id_usuario',
	);
	
	protected static $_table_name = 'notificacao_lote';
	
	protected static $_primary_key = array('id_lote', 'id_usuario');
	
	public static function inscreve($idLote, $idUsuario) {
		// insere
		$o = new Model_Notificacao_Lote();
		$o->id_lote = $idLote;
		$o->id_usuario = $idUsuario;
		return $o->save();
	}
	
	public static function desinscreve($idLote, $idUsuario) {
		// deleta lote
		$o = Model_Notificacao_Lote::find(array($idLote, $idUsuario));
		return $o->delete();
	}
	
}

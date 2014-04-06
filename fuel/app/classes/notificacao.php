<?php

class Notificacao {

	public static function colonia($dados) {
		
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
		
		// busca usuários que estão inscritos para a colônia, exceto o usuário atual
		$usuarios = DB::query(
				'SELECT
				 u.email, u.profile_fields
		
				 FROM
				 notificacao_colonia nc INNER JOIN
				 users u ON nc.id_usuario = u.id
				
				 WHERE
				 nc.id_colonia = ' . $dados['id_colonia'] . ' AND 
				 u.id != ' . $idUsuario
		)->as_object('Model_User')->execute();
		
		// busca colônia
		$colonia = Model_Colony::find($dados['id_colonia']);
		
		// substitui nome da colônia
		$dados['frase'] = str_replace('[nome_colonia]', $colonia->name, $dados['frase']);
		
		// busca usuário que disparou evento
		$usuario_disparador = Model_User::find($dados['id_usuario']);
		
		$usuarios = $usuarios->as_array();
		
		if (count($usuarios)) {
			foreach ($usuarios as $u) {
				// envia e-mail
				
				$data['u'] = $u;
				$data['usuario_disparador'] = $usuario_disparador;
				$data['secao'] = 'Colônias';
				$data['frase'] = $dados['frase'];
				
				$email = \Email::forge();
				$email->to($u->email);
				$email->subject('Geocolony: Notificação sobre colônia');
				$email->html_body(\View::forge('email/notificacao', $data));
				$email->from('geocolony@cecez.com.br', 'Geocolony');
				
				try {
					$email->send();
				} catch(\EmailSendingFailedException $e) {
						
				} catch(\EmailValidationFailedException $e) {
						
				} catch (Exception $e) {
						
				}
			}
		}
		
	}
	
	public static function linha($dados) {
		
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
	
		// busca usuários que estão inscritos para a linha
		$usuarios = DB::query(
				'SELECT
				 u.email, u.profile_fields
	
				 FROM
				 notificacao_linha nl INNER JOIN
				 users u ON nl.id_usuario = u.id
	
				 WHERE
				 nl.id_linha = ' . $dados['id_linha'] . ' AND 
				 u.id != ' . $idUsuario
		)->as_object('Model_User')->execute();
	
		// busca linha
		$linha = Model_Trail::find($dados['id_linha']);
	
		// substitui nome da linha
		$dados['frase'] = str_replace('[nome_linha]', $linha->name, $dados['frase']);
	
		// busca usuário que disparou evento
		$usuario_disparador = Model_User::find($dados['id_usuario']);
	
		$usuarios = $usuarios->as_array();
	
		if (count($usuarios)) {
			foreach ($usuarios as $u) {
				// envia e-mail
	
				$data['u'] = $u;
				$data['usuario_disparador'] = $usuario_disparador;
				$data['secao'] = 'Linhas';
				$data['frase'] = $dados['frase'];
	
				$email = \Email::forge();
				$email->to($u->email);
				$email->subject('Geocolony: Notificação sobre linha');
				$email->html_body(\View::forge('email/notificacao', $data));
				$email->from('geocolony@cecez.com.br', 'Geocolony');
	
				try {
					$email->send();
				} catch(\EmailSendingFailedException $e) {
	
				} catch(\EmailValidationFailedException $e) {
	
				} catch (Exception $e) {
	
				}
			}
		}
	
	}
	
	public static function lote($dados) {
		
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
	
		// busca usuários que estão inscritos para o lote
		$usuarios = DB::query(
				'SELECT
				 u.email, u.profile_fields
	
				 FROM
				 notificacao_lote nl INNER JOIN
				 users u ON nl.id_usuario = u.id
	
				 WHERE
				 nl.id_lote = ' . $dados['id_lote'] . ' AND 
				 u.id != ' . $idUsuario
		)->as_object('Model_User')->execute();
	
		// busca lote
		$lote = Model_Plot::find($dados['id_lote']);
	
		// substitui nome da lote
		$dados['frase'] = str_replace('[nome_lote]', $lote->number, $dados['frase']);
	
		// busca usuário que disparou evento
		$usuario_disparador = Model_User::find($dados['id_usuario']);
	
		$usuarios = $usuarios->as_array();
	
		if (count($usuarios)) {
			foreach ($usuarios as $u) {
				// envia e-mail
	
				$data['u'] = $u;
				$data['usuario_disparador'] = $usuario_disparador;
				$data['secao'] = 'Lotes';
				$data['frase'] = $dados['frase'];
	
				$email = \Email::forge();
				$email->to($u->email);
				$email->subject('Geocolony: Notificação sobre lote');
				$email->html_body(\View::forge('email/notificacao', $data));
				$email->from('geocolony@cecez.com.br', 'Geocolony');
	
				try {
					$email->send();
				} catch(\EmailSendingFailedException $e) {
	
				} catch(\EmailValidationFailedException $e) {
	
				} catch (Exception $e) {
	
				}
			}
		}
	
	}
	
	public static function proprietario($dados) {
		
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
	
		// busca usuários que estão inscritos para o lote-proprietário
		$usuarios = DB::query(
				'SELECT
				 u.email, u.profile_fields
	
				 FROM
				 notificacao_proprietario np INNER JOIN
				 users u ON np.id_usuario = u.id
	
				 WHERE
				 np.id_proprietario = ' . $dados['id_proprietario'] . ' AND 
				 u.id != ' . $idUsuario
		)->as_object('Model_User')->execute();
	
		// busca lote-proprietario
		$loteproprietario = Model_Plot_Landholder::find($dados['id_proprietario']);
	
		// substitui nome da lote-proprietario
		$dados['frase'] = str_replace('[nome_proprietario]', $loteproprietario->landholder_name, $dados['frase']);
	
		// busca usuário que disparou evento
		$usuario_disparador = Model_User::find($dados['id_usuario']);
	
		$usuarios = $usuarios->as_array();
	
		if (count($usuarios)) {
			foreach ($usuarios as $u) {
				// envia e-mail
	
				$data['u'] = $u;
				$data['usuario_disparador'] = $usuario_disparador;
				$data['secao'] = 'Proprietários';
				$data['frase'] = $dados['frase'];
	
				$email = \Email::forge();
				$email->to($u->email);
				$email->subject('Geocolony: Notificação sobre proprietário');
				$email->html_body(\View::forge('email/notificacao', $data));
				$email->from('geocolony@cecez.com.br', 'Geocolony');
	
				try {
					$email->send();
				} catch(\EmailSendingFailedException $e) {
	
				} catch(\EmailValidationFailedException $e) {
	
				} catch (Exception $e) {
	
				}
			}
		}
	
	}
	
}
?>

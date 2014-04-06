<?php

class Controller_Dashboard extends Controller_Template
{

	public function action_index()
	{
		$colonies_data = Model_Colony::find('all', array(
			'where' => array(
				array('active', '1'),
			),
			'order_by' => 'name'
		));
		
		$trails_data = array();

		// adding select option
		$select_option = array('id' => '-', 'name' => 'Selecione...');

		array_unshift($colonies_data, $select_option);
		$colonies_data = Arr::assoc_to_keyval($colonies_data, 'id', 'name');

		array_unshift($trails_data, $select_option);
		$trails_data = Arr::assoc_to_keyval($trails_data, 'id', 'name');        

		
		
		// view
		$view = View::forge('dashboard/index');
		$view->set_global('colonies', $colonies_data);
		$view->set_global('trails', $trails_data);

		
		
		// template
		$this->template->title = 'Dashboard &raquo; Index';
		$this->template->content = $view;
	}
	
	public function action_confirmacao($email, $hash) {
		
		// verifica o hash
		$hashVerificacao = md5('geo+' . $email . '-colony');
		if ($hash != $hashVerificacao) {
			Session::set_flash('error', 'Confirmação inválida.');
		} else {
		
			// busca usuário
			$u = Model_User::query()->where('email', urldecode($email))->get();
			if ( $u ) $u = reset($u);
			
			// atualiza campo
			$u->active = '1';
			$u->save();
			
			Session::set_flash('success', 'Usuário confirmado com sucesso. Você já pode realizar o login.');
		}
		
		Response::redirect('dashboard/index');	
	}
	
	public function action_recuperacao($email, $forgot_rand)
	{
		$expire = date("U", strtotime('-2 hours'));
		$u = Model_User::query()->where('forgot_rand', $forgot_rand)->where('email', urldecode($email))->where('forgot_at', '>', $expire)->get();
		if ( $u ) $u = reset($u);
		
		if ( !isset($u->id) ) {
			Session::set_flash('error', 'Requisição inválida ou o link expirou.');
			
		} else {
			
			// gera nova senha e envia para o e-mail do usuário
			$novaSenha = Auth::reset_password($u->username);
			
			$data['u'] = $u;
			$data['senha'] = $novaSenha;
			
			$email = \Email::forge();
			$email->to($u->email);
			$email->subject('Geocolony: Nova senha');
			$email->html_body(\View::forge('email/nova_senha', $data));
			$email->from('geocolony@cecez.com.br', 'Geocolony');
				
			try {
				$email->send();
				// e-mail enviado
				
				$u->forgot_rand = '';
				$u->forgot_at = '';
				$u->save();
				
				Session::set_flash('success', 'Foi enviado um e-mail com sua nova senha.');
			
			} catch(\EmailSendingFailedException $e) {
				die('Falha ao enviar e-mail.');
			} catch(\EmailValidationFailedException $e) {
				die('Endereço de e-mail inválido.');
			}
			
		}
		
		Response::redirect('dashboard/index');
	}
	
	public function action_admin() {
		// usuário logado
		
		// view
		$view = View::forge('dashboard/admin');
		
		// menu
		$menu = View::forge('dashboard/menu');
		$view->set_global('menu', $menu);
		
		// template
		$this->template->title = 'Dashboard &raquo; Administração';
		$this->template->content = $view;
	}

}

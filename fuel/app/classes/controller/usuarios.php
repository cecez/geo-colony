<?php

use Fuel\Core\Input;
class Controller_Usuarios extends Controller_Template
{
	
	public function before() {
	
		parent::before();
	
		// verifica se está logado
		if (!Auth::check()) Response::redirect('dashboard/index');
	
	}
	
	public function action_editar() {
	
		$view = View::forge('usuarios/editar');
	
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
	
		// monta formulário
		$form = Fieldset::forge('editar');
	
		Model_User::form_editar($form);
		
		
	
		$mensagemRetorno = '';
		if (Input::post()) {
			
			$idUsuario = Input::post('id');
				
			// salva dados
			$form->repopulate();
			$result = Model_User::salva_editar($form, Input::post('id'));
	
			if ($result['e_found']) {
				$mensagemRetorno = $result['errors'];
			} else {
				
				Session::set_flash('success', 'Dados atualizados com sucesso.');
				Response::redirect('usuarios/listagem?ok');
			}
				
		} else {
			$idUsuario = Input::get('id');
		}
	
	
		// busca dados do usuário
		$usuario = Model_User::find($idUsuario);
	
	
		$form->populate($usuario);
		$form->field('id')->set_value($usuario->id);
		$form->field('nome')->set_value($usuario->profile_fields['nome']);
		
	
		$view->set('form', $form->build(), false);
		$view->set('mensagem', $mensagemRetorno, false);
	
		$this->template->title = 'Usuários &raquo; Editar';
		$this->template->content = $view;
	}
	
	public function action_inserir() {
		
		$view = View::forge('usuarios/inserir');
		
		// formulario
		$form = Fieldset::forge('register');
		
		Model_User::register($form);
		
		// campo para permissão
		
		$form->add_before('group', 'Permissão:', array('options' => array(Model_User::PERMISSAO_ADMINISTRADOR => 'Administrador', Model_User::PERMISSAO_EDITOR => 'Editor'), 'type' => 'radio', 'value' => '1'), array(), 'submit');
		
			
		
		if (Input::post())
		{
			
				$form->repopulate();
				$result = Model_User::validate_registration($form, Auth::instance());
			
				if ($result['e_found'])
				{
					$view->set('errors', $result['errors'], false);
				}
				else
				{
					Session::set_flash('success', 'Usuário criado com sucesso.');
					Response::redirect('usuarios/listagem?ok');
				}
			
			
		} 
			
			
			
			// menu
			$view->set_global('menu', View::forge('dashboard/menu'));
			
			
			
			$view->set('reg', $form->build(), false);
			
			$this->template->title = 'Usuários &raquo; Inserir usuário';
			$this->template->content = $view;
		
	}
	
	public function action_toggle_status() {
		
		$usuario = Model_User::find(Input::get('id'));
		
		// altera coluna que representa estado
		if ($usuario->active == '1') {
			$usuario->active = '0';
			$mensagem = 'Usuário desativado com sucesso';
		} else {
			$usuario->active = '1';
			$mensagem = 'Usuário reativado com sucesso';
		}
		$usuario->save();
		
		Session::set_flash('success', $mensagem);
		Response::redirect('usuarios/listagem?ok');
		
	}
	
	public function action_listagem() {
		
		$view = View::forge('usuarios/listagem');
		
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
		
		// busca todas as colônias
		$dadosUsuarios = DB::query(
				'SELECT
				 *
		
				 FROM
				 users
		
				 ORDER BY
				 username ASC'
		)->as_object('Model_User')->execute();
		
		$view->set_global('usuarios', $dadosUsuarios->as_array());
		$auth = Auth::instance();
		$view->set_global('idUsuarioAtual', $auth->get_user_id()[1]);
		$this->template->title = 'Usuários &raquo; Listagem';
		$this->template->content = $view;
	}
	
	public function action_meus_dados() {
		
		$view = View::forge('usuarios/meus_dados');
		
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
		
		// monta formulário
		$form = Fieldset::forge('meusdados');
		
		Model_User::form_meus_dados($form);
		
		$mensagemRetorno = '';
		if (Input::post()) {
			
			// salva dados
			$form->repopulate();
			$result = Model_User::salva_meus_dados($form);
				
			if ($result['e_found']) {
				$mensagemRetorno = $result['errors'];
			} else {
				$mensagemRetorno = 'Dados atualizados com sucesso.';
			}
			
		}
		
		
		// busca dados do usuário
		$auth = Auth::instance();
		$idUsuarioAtual = $auth->get_user_id()[1];
		
		$usuario = Model_User::find($idUsuarioAtual);
		
		
		$form->populate($usuario);
		$form->field('nome')->set_value($usuario->profile_fields['nome']);
		
		$view->set('form', $form->build(), false);
		$view->set('mensagem', $mensagemRetorno, false);
		
		$this->template->title = 'Usuários &raquo; Meus dados';
		$this->template->content = $view;
	}
	
}

?>
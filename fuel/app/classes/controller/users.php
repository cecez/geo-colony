<?php

use Fuel\Core\Input;
class Controller_Users extends Controller_Template {

	public function action_esqueci_a_senha() {
		
		if (Input::post()) {
			
			// valida e salva
			$result = Model_User::esqueci_a_senha();
				
			if ($result['e_found']) {
				Session::set_flash('error', $result['errors']);
			} else {
				Session::set_flash('success', 'Um e-mail foi enviado com as instruções para gerar uma nova senha');
			}
		}
		Response::redirect('dashboard/index');
	}
	
	public function action_login()
	{
	    if (Input::post())
	    {
	    	$auth = Auth::instance();
			if ($auth->login(Input::post('email'), Input::post('senha'))) {
				
				
				
				
		    	Response::redirect('dashboard/admin');
			}
			else
			{
		    	Session::set_flash('error', 'Dados invalidos.');
			}
	    }
	    Response::redirect('dashboard/index');
	}

	public function action_logout()
	{
	    $auth = Auth::instance();
	    $auth->logout();
	    Session::set_flash('success', 'Logout efetuado com sucesso.');
	    Response::redirect('./');
	}
	
	public function action_registro() {
		
		if (Input::post()) {
			// valida e salva
			$result = Model_User::valida_registro();
			
			if ($result['e_found']) {
				Session::set_flash('error', $result['errors']);
			} else {
				Session::set_flash('success', 'Para completar o cadastro acesse seu e-mail e clique no link de confirmação.');
			}
		}
		Response::redirect('dashboard/index');
		
	}

	public function action_register()
	{
		$auth = Auth::instance();
		$view = View::forge('users/register');
		$form = Fieldset::forge('register');
		Model_User::register($form);

		if (Input::post())
		{
			$form->repopulate();
			$result = Model_User::validate_registration($form, $auth);

			if ($result['e_found'])
			{
			    $view->set('errors', $result['errors'], false);
			}
			else
			{
			    Session::set_flash('success', 'Usuário criado com sucesso. Foi enviado um e-mail de confirmação para o usuário.');
			    Response::redirect('./');
			}
		}

		$view->set('reg', $form->build(), false);
		$this->template->title = 'Usuario &raquo; Registro';
		$this->template->content = $view;
	}

}

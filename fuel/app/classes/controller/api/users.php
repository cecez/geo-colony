<?php

class Controller_Api_Users extends Controller_Rest
{

	public function post_index()
	{	
		// TODO validacao
		$data = array(
			'permission_id' => 1, // TODO constante
			'name' => Input::json('name'),
			'email' => Input::json('email'),
			'password' => md5(Input::json('password'))
		);

		// salva 
		$user = new Model_User($data);
		$user->save();

		return $this->response(array(
            		'chave' => 'ID: ' . $user->id
		));
	}

	
	
}

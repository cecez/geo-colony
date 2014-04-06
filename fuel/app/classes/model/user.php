<?php

use Fuel\Core\Input;
use Fuel\Core\Fieldset;
use Fuel\Core\Model;
use Auth\Auth;
class Model_User extends \Orm\Model
{
	// coluna group
	const PERMISSAO_ADMINISTRADOR = 1;
	const PERMISSAO_EDITOR = 2;
	
	protected static $_properties = array(
		'id',
		'username',
		'password',
		'group',
		'email',
		'last_login',
		'login_hash',
		'profile_fields' => array(
			'data_type' => 'serialize',
		),
		'active',
		'forgot_at',
		'forgot_rand'
	);
	
	protected static $_has_many = array('colonies');

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => false,
		),
			'Orm\Observer_Typing' => array(
					'events' => array('before_save', 'after_save', 'after_load'),
					),
	);
	
	public static function confirmacao_email($mail) {
		
		// gera link
		$rand = md5('geo+' . $mail . '-colony');
		$link = Uri::base(false) .'confirmacao/' .urlencode($mail) .'/' .$rand;
			
		// envia e-mail
		$data['link'] = $link;
		$email = \Email::forge();
		$email->to($mail);
		$email->subject('Geocolony: Confirmação de e-mail');
		$email->html_body(\View::forge('email/confirmacao', $data));
		
		try {
			$email->send();
			// e-mail enviado
			return true;
		} catch(\EmailSendingFailedException $e) {
			
		} catch(\EmailValidationFailedException $e) {
			
		} catch (Exception $e) {
			
		}
		
		return false;
	}
	
	public static function esqueci_a_senha() {
		
		$email = filter_var(Input::post('email'), FILTER_SANITIZE_EMAIL);
		$u = Model_User::query()->where('email', $email)->get();
		$id = reset($u)['id'];
		if (isset($u) && !empty($u)) {
			$user = Model_User::find($id);
			
			
			$rand = Str::random('unique');
			$link = Uri::base(false) .'recuperacao/' .urlencode($email) .'/' .$rand;
			$user->forgot_rand = $rand;
			$user->forgot_at = date("U");
			$user->save();
			
			$data['u'] = $user;
			$data['link'] = $link;
			$email = \Email::forge();
			$email->to($user->email);
			$email->subject('Geocolony: Recuperação de Senha');
			$email->html_body(\View::forge('email/recuperacao_senha', $data));
			$email->from('geocolony@cecez.com.br', 'Geocolony');
			
			try {
				$email->send();
				// e-mail enviado
				Session::set_flash('success', 'Foi enviado um e-mail com as instruções para recuperação de senha.');
				
			} catch(\EmailSendingFailedException $e) {
				die('Falha ao enviar e-mail.');
			} catch(\EmailValidationFailedException $e) {
				die('Endereço de e-mail inválido.');
			}
		} else {
			Session::set_flash('error', 'Não foi encontrado usuário com este e-mail.');
		}
		
		Response::redirect('dashboard/index');		
	}

	public static function register(Fieldset $form) {
		$form->add('nome', 'Nome:')->add_rule('required');
    		$form->add('password', 'Senha:', array('type'=>'password'));
    		$form->add('password2', 'Confirme a senha:', array('type' => 'password'));
    		$form->add('email', 'E-mail:')->add_rule('required')->add_rule('valid_email');
    		$form->add('submit', ' ', array('type'=>'submit', 'value' => 'Criar usuário'));

    		return $form;
	}
	
	public static function form_meus_dados(Fieldset $form) {
		$form->add('nome', 'Nome:')->add_rule('required');
		$form->add('password', 'Senha antiga:', array('type'=>'password'));
		$form->add('password2', 'Senha nova:', array('type' => 'password'));
		$form->add('email', 'E-mail:')->add_rule('required')->add_rule('valid_email');
		$form->add('submit', ' ', array('type'=>'submit', 'value' => 'Atualizar meus dados'));
	
		return $form;
	}
	
	public static function form_editar(Fieldset $form) {
		$form->add('id', '', array('type' => 'hidden'));
		$form->add('nome', 'Nome:')->add_rule('required');
		$form->add('email', 'E-mail:')->add_rule('required')->add_rule('valid_email');
		$form->add('group', 'Permissão:', array('options' => array(Model_User::PERMISSAO_ADMINISTRADOR => 'Administrador', Model_User::PERMISSAO_EDITOR => 'Editor'), 'type' => 'radio', 'value' => '1'), array(), 'submit');
		
		$form->add('submit', ' ', array('type'=>'submit', 'value' => 'Atualizar'));
	
		return $form;
	}
	
	public static function salva_editar(Fieldset $form, $id) {
		
		$val = $form->validation();
		$val->set_message('required', 'O campo ":field" e obrigatorio');
		$val->set_message('valid_email', 'O campo ":field" deve ser um endereço de e-mail');
		
		// valida
		if ($val->run()) {
				
			$usuario = Model_User::find($id);
			$usuario->group = Input::post('group');
			$usuario->profile_fields['nome'] = Input::post('nome');
			$usuario->email = Input::post('email');
			try {
				$resultado = $usuario->save();
			} catch (Exception $e) {
				
			}
			
			if (!$resultado) {
				return array('e_found' => true, 'errors' => 'Falha ao atualizar usuário.');
			}
			
		} else {
			$errors = $val->show_errors();
			
			return array('e_found' => true, 'errors' => $errors);
		}
		
		return array('e_found' => false);
		
	}
	
	public static function salva_meus_dados(Fieldset $form) {
		
		$val = $form->validation();
		$val->set_message('required', 'O campo ":field" e obrigatorio');
		$val->set_message('valid_email', 'O campo ":field" deve ser um endereço de e-mail');
		
		// valida
		if ($val->run()) {
			
			$dados['email'] = Input::post('email');
			$dados['nome'] = Input::post('nome');
			
			if (Input::post('password') && Input::post('password2')) {
				$dados['old_password'] = Input::post('password');
				$dados['password'] = Input::post('password2');
			}
			
			// salva
			try {
				$resultado = Auth::update_user($dados);
			} catch (\SimpleUserWrongPassword $e) {
				return array('e_found' => true, 'errors' => 'Senha antiga não confere');
			} catch (Exception $e) {
				
			}
			
			if (!$resultado) {
				return array('e_found' => true, 'errors' => 'Falha ao atualizar usuário');
			}
			
		} else {
			$errors = $val->show_errors();
			return array('e_found' => true, 'errors' => $errors);
		}
		
		return array('e_found' => false);
	}
	
	public static function valida_registro() {

		$email = Input::post('email');
		$senha = Input::post('senha');
		$arrayCamposPersonalizados['nome'] = Input::post('nome');
		
		// valida
		$form = Fieldset::forge('form_registro');
		$form->add('nome')->add_rule('required');
		$form->add('email')->add_rule('required')->add_rule('valid_email');
		$form->add('senha')->add_rule('required');
		
		$val = $form->validation();
		$val->set_message('required', 'O campo ":field" é obrigatório');
		$val->set_message('valid_email', 'O campo ":field" deve ser um endereço de e-mail');
		
		if ($val->run()) {
		
			// salva
			$auth = Auth::instance();
			try {
				$user = $auth->create_user($email, $senha, $email, Model_User::PERMISSAO_EDITOR, $arrayCamposPersonalizados);
				
				// envia e-mail para confirmação
				self::confirmacao_email($email);
				
			} catch (\SimpleUserUpdateException $e) {
				$mensagem = $e->getMessage();
				if ($mensagem == 'Email address already exists' || $mensagem == 'Username already exists') {
					$mensagem = 'Já existe um usuário com este endereço de e-mail.';
				}
				return array('e_found' => true, 'errors' => $mensagem);
				
			} catch (Exception $e) {
				return array('e_found' => true, 'errors' => $e->getMessage());
			}
			
		} else {
			$errors = $val->show_errors();
			return array('e_found' => true, 'errors' => $errors);
		}
		
		return array('e_found' => false);
	}

	public static function validate_registration(Fieldset $form, $auth)
	{
    		$form->field('password')->add_rule('match_value', $form->field('password2')->get_attribute('value'));
		$val = $form->validation();
    		$val->set_message('required', 'O campo ":field" e obrigatorio');
    		$val->set_message('valid_email', 'O campo ":field" deve ser um endereço de e-mail');
   		$val->set_message('match_value', 'As senhas devem ser iguais');

		if ($val->run())
		{
			$username = $form->field('email')->get_attribute('value');
        		$password = $form->field('password')->get_attribute('value');
        		$email = $form->field('email')->get_attribute('value');
        		$grupo = $form->field('group')->get_attribute('value');
        		$arrayCamposPersonalizados['nome'] = $form->field('nome')->get_attribute('value');
        		
        		
        		
			try {
           			$user = $auth->create_user($username, $password, $email, $grupo, $arrayCamposPersonalizados);
        		
           			self::confirmacao_email($email);
        		}
        		catch (Exception $e)
        		{
            			$error = $e->getMessage();
        		}

			if (isset($user))
        		{
            			//$auth->login($username, $password);
        		}
			else
			{
			    if (isset($error))
			    {
				$li = $error;
			    }
			    else
			    {
				$li = 'Erro ao criar o usuario!';
			    }
			    $errors = Html::ul(array($li));
			    return array('e_found' => true, 'errors' => $errors);
			}
		}
		else
		{
			$errors = $val->show_errors();
			return array('e_found' => true, 'errors' => $errors);
		}
	}
}

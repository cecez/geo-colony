<?php

class Model_Colony extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'user_id',
		'name',
		'public',
		'created_at',
		'updated_at',
		'active',
		'last_editor_id',
		'reprovacao'
	);

	protected static $_belongs_to = array('users');
	protected static $_has_many = array('trails');

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => false,
		),
	);
	
	public static function register(Fieldset $form, $edit = false) {
		
		
		if ($edit) {
			$form->add('id', 'ID', array('type' => 'hidden'));
		}
		
		$form->add('name', 'Nome:')->add_rule('required');
		$form->add('public', '', array('options' => array('1' => 'Colônia Pública'), 'type' => 'checkbox', 'value' => '1'));
		
		// Buscar usuários do sistema
		$usuarios = Model_User::find('all', array('select' => array('username', 'id')));
		$array_usuarios = array();
		foreach ($usuarios as $u) {
			$array_usuarios[$u->id] = $u->username;
		}
		
		$array_usuarios_selecionados = array();
		if ($edit) {
			//$array_usuarios_selecionados = array('2');
		}
				
		$form->add('colaborators', 'Colaboradores:', array('options' => $array_usuarios, 'value' => $array_usuarios_selecionados, 'type' => 'select', 'multiple' => 'true'));
		
		// formulário de fontes
		//Model_Fonte::formulario($form);
			

		$form->add('submit', ' ', array('type'=>'submit', 'value' => 'Inserir'));
		
	
		return $form;
	}
	
	public static function validate(Fieldset $form) {
		// todo
	}
	
	// busca as fontes de revisões aprovadas da colônia
	public function buscaFontes() {
		
		$consulta = DB::query(
				'SELECT
				f.titulo, f.editora, f.autor, cf.pagina, cf.observacao
		
				FROM
				colonia_fonte cf inner join
				revisions r on (r.id = cf.revisao_id) inner join
				fontes f on (f.id = cf.fonte_id)
		
				WHERE
				cf.colonia_id = ' . $this->id . ' AND
				r.approved = ' . Model_Revision::REVISAO_APROVADA . '
		
				ORDER BY
				r.date DESC, r.id DESC'
				)->execute()->as_array();
		
		return $consulta;
		
	}
}

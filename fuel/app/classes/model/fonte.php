<?php

class Model_Fonte extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'usuario_id',
		'titulo',
		'editora',
		'autor',
		'data_de_cadastro'
	);
	
	public static function formulario(Fieldset $form) {
		
		$form->add('titulo', 'Título:', array('id' => 'fonte_titulo', 'style' => 'display:none'))->add_rule('required');
		$form->add('autor', 'Autor:')->add_rule('required');
		$form->add('editora', 'Editora:');
		$form->add('ano_publicacao', 'Ano de publicação:');

		return $form;
	}
}

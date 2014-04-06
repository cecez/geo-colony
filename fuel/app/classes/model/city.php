<?php

class Model_City extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'state_id',
		'name',
		'created_at',
		'updated_at'
	);

	protected static $_belongs_to = array('state');
	protected static $_has_many = array('plots');

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

	public static function findByNameWithPlots($search)
	{
		return DB::query('
			SELECT DISTINCT t.id, t.name 
			FROM cities t JOIN plots ON plots.city_id = t.id AND plots.active = 1
			WHERE t.name LIKE '.DB::quote($search.'%').' ORDER BY t.name
		')->execute();
	}
	
	/**
	 * Cadastra uma nova cidade.
	 * 
	 * @param string $nome Nome da Cidade.
	 * @param string $siglaEstado Sigla do Estado.
	 * @return integer ID da nova cidade.
	 */
	public static function insereCidade ($nome, $siglaEstado) {
		
		// busca ID do estado
		$estado = Model_State::find('first', array(
    										  'where' => array(
        												  array('code', $siglaEstado)
    													 )
    									 	 )
								   );
		
		if ($estado->id) {
			// insere cidade
			$cidade = new Model_City();
			$cidade->name = $nome;
			$cidade->state_id = $estado->id;
			if ($cidade->save()) {
				return $cidade->id;
			}
		}
		
		return false;
	}
}

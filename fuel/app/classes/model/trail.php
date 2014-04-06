<?php

class Model_Trail extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'user_id',
		'colony_id',
		'name',
		'created_at',
		'updated_at',
		'active',
		'last_editor_id',
		'reprovacao'
	);

	protected static $_belongs_to = array('colony');
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

	public static function findByIds($ids, $options) {
		foreach ($ids as $id)
		{
			if (!isset($where))
			{
				$where = array(
					array('id', $id)
				);
			}
			else
			{
				$where = array(
					array('id', $id),
					'or' => $where
				);
			}
		}
		$options['where'] = $where;
		return self::find('all', $options);
	}
	
	// busca as fontes de revisões aprovadas
	public function buscaFontes() {
	
		$consulta = DB::query(
				'SELECT 
				f.titulo, f.editora, f.autor, lf.pagina, lf.observacao 
				
				FROM 
				linha_fonte lf inner join 
				revisions r on (r.id = lf.revisao_id) inner join
				fontes f on (f.id = lf.fonte_id)
				
				WHERE 
				lf.linha_id = ' . $this->id . ' AND
				r.approved = ' . Model_Revision::REVISAO_APROVADA . '
	
				ORDER BY
				r.date DESC, r.id DESC'
		)->execute()->as_array();
	
		return $consulta;
	
	}
}

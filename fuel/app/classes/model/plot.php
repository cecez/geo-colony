<?php

class Model_Plot extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'user_id',
		'colony_id',
		'trail_id',
		'city_id',
		'number',
		'elevation',
	    'area',
		'edge',
		'nucleu',
		'section',
		'active',
		'last_editor_id',
		'reprovacao'
	);

	protected static $_belongs_to = array('colony', 'trail', 'city');
	protected static $_has_many = array('plot_coordinates', 'plot_landholders');

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
	
	// busca as fontes de revisões aprovadas
	public function buscaFontes() {
	
		$consulta = DB::query(
				'SELECT 
				f.titulo, f.editora, f.autor, lf.pagina, lf.observacao 
				
				FROM 
				lote_fonte lf inner join 
				revisions r on (r.id = lf.revisao_id) inner join
				fontes f on (f.id = lf.fonte_id)
				
				WHERE 
				lf.lote_id = ' . $this->id . ' AND
				r.approved = ' . Model_Revision::REVISAO_APROVADA . '
	
				ORDER BY
				r.date DESC, r.id DESC'
		)->execute()->as_array();
	
		return $consulta;
	
	}
}

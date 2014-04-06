<?php

class Model_Plot_Landholder extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'user_id',
		'plot_id',
		'granting',
		'release',
		'area',
		'price',
		'observation',
		'landholder_name',
		'landholder_family',
		'landholder_origin',
		'last_editor_id',
		'active'
	);

	protected static $_belongs_to = array('plot');

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
				lote_proprietario_fonte lf inner join 
				revisions r on (r.id = lf.revisao_id) inner join
				fontes f on (f.id = lf.fonte_id)
				
				WHERE 
				lf.loteproprietario_id = ' . $this->id . ' AND
				r.approved = ' . Model_Revision::REVISAO_APROVADA . '
	
				ORDER BY
				r.date DESC, r.id DESC'
		)->execute()->as_array();
	
		return $consulta;
	
	}
}

<?php

use Orm\BelongsTo;
use Fuel\Core\Input;
class Controller_Api_Linhas extends Controller_Rest
{
	/**
	 * Busca linhas a partir de um id de colônia
	 *
	 */
	public function get_search()
	{
		$consulta = Input::get('id_colonia');
		
		$resultados = array();
		
		// realiza a busca por registros que contenham o termo da pesquisa, em qualquer campo, seja no título, autor, editora
		
		$linhas = DB::query(
				'SELECT
				 id, name
		
				 FROM
				 trails
		
				 WHERE
				 colony_id = ' . $consulta
		)->as_object('Model_Trail')->execute();
		
		// processa resultados
		if (!count($linhas)) {
			$resultados[] = array('nome' => 'nop');
		} else {
			foreach ($linhas as $linha) {
				$resultados[] = array(
								 'id' => $linha->id,
								 'nome' => $linha->name
								);
				
			}
		}
		
		return $this->response($resultados);
	}
	
}
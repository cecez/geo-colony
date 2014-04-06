<?php

use Orm\BelongsTo;
use Fuel\Core\Input;
class Controller_Api_Lotes extends Controller_Rest
{
	/**
	 * Busca lotes a partir de um id de linha
	 *
	 */
	public function get_search()
	{
		$consulta = Input::get('id_linha');
		
		$resultados = array();
		
		$lotes = DB::query(
				'SELECT
				 id, number
		
				 FROM
				 plots
		
				 WHERE
				 trail_id = ' . $consulta
		)->as_object('Model_Plot')->execute();
		
		// processa resultados
		if (!count($lotes) || ($consulta == '0')) {
			$resultados[] = array('nome' => 'nop');
		} else {
			foreach ($lotes as $lote) {
				$resultados[] = array(
								 'id' => $lote->id,
								 'numero' => $lote->number
								);
				
			}
		}
		
		return $this->response($resultados);
	}
	
}
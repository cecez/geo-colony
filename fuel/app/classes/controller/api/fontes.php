<?php

use Orm\BelongsTo;
use Fuel\Core\Input;
class Controller_Api_Fontes extends Controller_Rest
{
	/**
	 * Busca fontes a partir de uma consulta textual
	 *
	 */
	public function get_search()
	{
		$consulta = Input::get('term');
		
		$resultados = array();
		
		// realiza a busca por registros que contenham o termo da pesquisa, em qualquer campo, seja no título, autor, editora
		
		$fontes = DB::query(
				'SELECT
				 id, titulo, editora, autor
		
				 FROM
				 fontes
		
				 WHERE
				 titulo LIKE \'%' . $consulta . '%\' OR 
				 autor LIKE \'%' . $consulta . '%\' OR 
				 editora LIKE \'%' . $consulta . '%\''
		)->as_object('Model_Fonte')->execute();
		
		// processa resultados
		if (!count($fontes)) {
			$resultados[] = array('titulo' => 'nop');
		} else {
			foreach ($fontes as $fonte) {
				$resultados[] = array(
								 'id' => $fonte->id,
								 'titulo' => $fonte->titulo,
								 'autor' => $fonte->autor,
								 'editora' => $fonte->editora				
								);
				
			}
		}
		
		return $this->response($resultados);
	}
	
}
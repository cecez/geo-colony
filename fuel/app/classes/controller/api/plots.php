<?php

class Controller_Api_Plots extends Controller_Rest
{

	public function get_id()
	{
		$data = Model_Plot::find($this->param('id'), array(
			'related' => array(
				'colony' => array('select' => array('id', 'name')),
				'trail' => array('select' => array('id', 'name')),
				'city' => array('select' => array('id', 'name'), 'related' => array('state' => array('select' => array('id', 'code')))),
				'plot_landholders' => array(
					'select' => array('id', 'granting', 'release', 'area', 'price', 'landholder_name', 'landholder_family')
				),
				'plot_coordinates' => array(
					'select' => array('id', 'latitude', 'longitude')
				)
			)

		));

		$data = Format::forge($data)->to_array();
		//$data["area"] = !empty($data["plot_landholders"][0]) ? $data["plot_landholders"][0]["area"] : '0';
		
		$data['cidade'] = '';
		if (isset($data['city']) && isset($data['city']['state'])) {
			$data['cidade'] = $data['city']['name'] . ' (' . $data['city']['state']['code'] . ')';
		}
		
		// busca as fontes relacionadas
		
		// da colônia
		$data['fontes_colonia'] = array();
		if ($data['colony_id']) {
			// busca as fontes das revisões aprovadas da colônia
			$colonia = new Model_Colony();
			$colonia->id = $data['colony_id'];
			$fontesColonia = $colonia->buscaFontes();
			
			if (count($fontesColonia)) {
				$data['fontes_colonia'] = $fontesColonia;
			}
		}
		
		// da linha
		$data['fontes_linha'] = array();
		if ($data['trail_id']) {
			// busca as fontes das revisões aprovadas da linha
			$linha = new Model_Trail();
			$linha->id = $data['trail_id'];
			$fontesLinha = $linha->buscaFontes();
				
			if (count($fontesLinha)) {
				$data['fontes_linha'] = $fontesLinha;
			}
		}
		
		// do lote
		$data['fontes_lote'] = array();
		
		// busca as fontes das revisões aprovadas do lote
		$lote = new Model_Plot();
		$lote->id = $this->param('id');
		$fontesLote = $lote->buscaFontes();
		
		if (count($fontesLote)) {				
			$data['fontes_lote'] = $fontesLote;
		}
		
		// dos proprietários-lote
		$data['fontes_proprietarios'] = array();
		
		$consulta = Model_Plot_Landholder::query()->where('plot_id', $this->param('id'))->get();
		
		if ($consulta) {
			foreach ($consulta as $c) {
				$fontesProprietario = $c->buscaFontes();
				if ($fontesProprietario) {
					$data['fontes_proprietarios'][] = 
					array(
					 'nome'   => $c->landholder_name, 
					 'fontes' => $fontesProprietario
					);
				}
			}
		}
		

		return $this->response($data);
	}
}

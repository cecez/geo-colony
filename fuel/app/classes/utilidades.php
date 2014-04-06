<?php

class Utilidades {
	
	
	/**
	 * Calcula área de um polígono.
	 * 
	 * A partir de um array de coordenadas calcula a área em hectares do polígono.
	 * 
	 * @param array $coordenadas
	 * 
	 * @return double Área calculada.
	 */
	public static function calculaAreaLote($coordenadas) {
		
		$earth_radius = 6371009;
		$latDist = pi() * $earth_radius / 180.0;
		
		// limpa arrays com coordenadas do lote
		$latProj = array();
		$longProj = array();
		
		// monta arrays com coordenadas do lote
		foreach ($coordenadas as $c) {
			$latProj[]  = $c['lat'] * $latDist;
			$longProj[] = $c['lon'] * $latDist * cos(deg2rad($c['lat']));
		};
		
		$area = 0.0;
		for ($i = 0; $i < (count($latProj) - 1); $i++) {
			$area = $area + ( $latProj[$i] * $longProj[$i+1] ) - ( $latProj[$i+1] * $longProj[$i] );
		};
		
		$area = $area / 2 /10000; // area em hectares
		
		return abs($area);
	}
	
	/**
	 * Retorna a cidade mais próxima de uma coordenada.
	 * 
	 * Utiliza dados do Google Maps para retornar a cidade mais próxima de uma latitude, longitude.
	 * Retorna o ID da cidade no banco de dados.
	 * 
	 * @param double $latitude
	 * @param double $longitude
	 * @return number ID da cidade.
	 */
	public static function calculaCidadeMaisProxima($latitude, $longitude) {
		
		$url = 'http://maps.googleapis.com/maps/api/geocode/xml?latlng=' . $latitude . ',' . $longitude . '&sensor=false';
		
		$xml = simplexml_load_string(@file_get_contents($url));
		
		if (!is_a($xml, 'SimpleXMLElement')) {
			return false;
		}
		
		// busca nome da cidade
		$nomeCidade = '';
		$cidade = $xml->xpath("/GeocodeResponse/result/address_component[type='administrative_area_level_2']");
		if (count($cidade)) {
			$cidade = array_pop($cidade); 
			if (isset($cidade->long_name)) {
				$nomeCidade = $cidade->long_name;
			}
		}
		
		// busca sigla do estado
		$siglaEstado = '';
		$estado = $xml->xpath("/GeocodeResponse/result/address_component[type='administrative_area_level_1']"); 
		if (count($estado)) {
			$estado = array_pop($estado); 
			if (isset($estado->short_name)) {
				$siglaEstado = $estado->short_name;
			}
		}
		
		if ($nomeCidade && $siglaEstado) {
			
			$consulta = 
				DB::query(
				'SELECT 
				 id 
						
				 FROM 
				 cities 
						
				 WHERE 
				 name = \''.$nomeCidade.'\' AND 
				 state_id = (SELECT id FROM states WHERE code = \''.$siglaEstado.'\')'
				)->execute()->as_array();
			
			if (isset($consulta[0]['id'])) {
				return $consulta[0]['id'];
			} else {
				// insere cidade
				return Model_City::insereCidade($nomeCidade, $siglaEstado);
			}
				
		}
		
		return false;		
	}
	
	/**
	 * Retorna a elevação de um dado ponto a partir dos dados do Google.
	 * 
	 * @param double $latitude
	 * @param double $longitude
	 * @return double Elevação.
	 */
	public static function calculaElevacaoPonto($latitude, $longitude) {
		
		$url = 'http://maps.googleapis.com/maps/api/elevation/xml?locations=' . $latitude . ',' . $longitude . '&sensor=false';
		
		$xml = simplexml_load_string(@file_get_contents($url));
		
		if (isset($xml->result->elevation)) {
			$elevacao = $xml->result->elevation;
		} else {
			$elevacao = 0;
		}
		
		return (float) $elevacao;
		
	}
	
	
	/**
	 * Dado um conjunto de coordenadas, calcula o ponto central.
	 * 
	 * @param array $coordenadas
	 * 
	 * @return array Latitude e longitude do ponto central.
	 */
	public static function calculaPontoCentralDoLote($coordenadas) {
		
		$latOrig = array();
		$longOrig = array();
		
		foreach ($coordenadas as $c) {
			$latOrig[]  = $c['lat'];
			$longOrig[] = $c['lon'];
		}
		
		$midLat = ( min($latOrig) + max($latOrig) ) / 2;
		$midLong = ( min($longOrig) + max($longOrig) ) / 2;
		
		return array(
				'lat' => $midLat,
				'lon' => $midLong
			   );
		
	}

	/**
	 * Processa arquivo com coordenadas de longitude e latitude, retornando um array com as coordenadas
	 */
	public static function processaArquivoComCoordenadas() {
		
		// salvando arquivo
		$arr = Upload::get_files();
		Upload::save(APPPATH.'tmp', array_keys($arr));
			
		// lendo e processando o arquivo
		$kml = simplexml_load_file(APPPATH .'tmp/'. $arr[0]['name']);
		
		$a = trim($kml->Document->Placemark->Polygon->outerBoundaryIs->LinearRing->coordinates);
		$b = explode(' ', $a);
		
		$saida = array();
		if (count($b)) {
			
			// remove última coordenada pois é a repetição da primeira
			//unset($b[count($b)-1]);
			
			foreach ($b as $c) {
				list($longitude, $latitude, $altura) = explode(',', $c);
				$saida[] = array(
							'lon' => $longitude,
						    'lat' => $latitude
						   );
			}
		}
		
		
			
		// removendo arquivo
		File::delete(APPPATH.'tmp/'. $arr[0]['name']);
		
		return $saida;
		
	}

}
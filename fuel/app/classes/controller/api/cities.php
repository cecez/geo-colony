<?php

class Controller_Api_Cities extends Controller_Rest
{

	public function get_search()
	{
		$query = Input::get('query');
		if (empty($query))
		{
			throw new Exception('search can provide query');
		}

		$data = Model_city::findByNameWithPlots($query);
		return $this->response($data);
	}

	public function get_search_trails()
	{
		$query = Input::get('query');
		if (empty($query))
		{
			throw new Exception('search can provide query');
		}

		$cities = Format::forge(Model_city::findByNameWithPlots($query))->to_array();
		if (!empty($cities))
		{
			$ids = array_map(function($city){
				return $city['id'];
			}, $cities);

			$data = Model_Trail::find('all', array(
				'select' => array('id', 'name'),
				'related' => array(
					'plots' => array(
						'select' => array('id'),
						'where' => array(
							array('city_id', 'IN', $ids),
							array('active', '1')									 
						)
					)
				),
				'where' => array(
						array('active', '1'),
				),
				'order_by' => 'name'
			));
		}
		else
		{
			$data = (object) array();
		}
			
		return $this->response($data);
	}
	
}
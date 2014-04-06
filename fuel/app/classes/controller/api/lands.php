<?php

class Controller_Api_Lands extends Controller_Rest
{

	static private $trail_options = array(
		'select' => array('id', 'name'),
		'related' => array(
			'plots' => array(
				'select' => array('id'),
				'related' => array(
					'plot_coordinates' => array(
						'select' => array('id', 'latitude', 'longitude')
					)
				)
			)
		)
	);

	static private $landholder_options = array(
		'select' => array('id', 'name'),
		'related' => array(
			'plot_landholders' => array(
				'select' => array('id', 'plot_id'),
				'related' => array(
					'plot' => array(
						'select' => array('id'),
						'related' => array(
							'plot_coordinates' => array(
								'select' => array('id', 'latitude', 'longitude')
							)
						)
					)
				)
			)
		)
	);

	private static function to_array($result, $type)
	{
		$data = array();
		foreach ($result as $row) {
			if (empty($data))
			{
				$data = array(
					'id' => $type.$row['id'],
					'name' => $row['name'],
					'plots' => array()
				);
			}
			$plots = &$data['plots'];
			if (empty($plots[$row['plot_id']])) {
				$plots[$row['plot_id']] = array(
					'id' => $row['plot_id'],
					'plot_coordinates' => array()
				);
			}
			$plot_coordinates = &$plots[$row['plot_id']]['plot_coordinates'];
			$plot_coordinates[] = array(
				'latitude' => $row['latitude'],
				'longitude' => $row['longitude']
			);
		}
		return $data;
	}

	private static function factory($param)
	{
		$type = !empty($param) ? $param[0] : 'X';
		$id = substr($param, 1);

		if ($type == "T")
		{
			$result = DB::query('
				SELECT `trails`.`id`, `trails`.`name`, `plots`.`id` as `plot_id`, `plot_coordinates`.`latitude`, `plot_coordinates`.`longitude` 
				FROM `trails` LEFT JOIN 
					 `plots` ON `trails`.`id` = `plots`.`trail_id` AND `plots`.active = 1 LEFT JOIN 
					 `plot_coordinates` ON `plots`.`id` = `plot_coordinates`.`plot_id`
				WHERE `trails`.`id` = '.intval($id) . ' AND `trails`.active = 1 ORDER BY plot_coordinates.id')->execute();
		}
		else if ($type == "L")
		{
			$result = DB::query('
				SELECT `plot_landholders`.`id`, CONCAT("P: ", `plot_landholders`.`landholder_name`) AS name, `plots`.`id` as `plot_id`, `plot_coordinates`.`latitude`, `plot_coordinates`.`longitude` 
				FROM `plot_landholders` LEFT JOIN 
					 `plots` ON `plots`.`id` = `plot_landholders`.`plot_id` AND `plots`.active = 1 LEFT JOIN 
					 `plot_coordinates` ON `plots`.`id` = `plot_coordinates`.`plot_id`
				WHERE `plot_landholders`.`id` = '.intval($id) . ' AND `plot_landholders`.active = 1')->execute();
		}
		if (!empty($result)) {
			return static::to_array($result, $type);
		}
	}


	public function get_index()
	{
		$params = Input::get('id');
		$data = array();
		foreach ($params as $param)
		{
			$obj = static::factory($param);
			if (!empty($obj))
			{
				$data[] = $obj;
			}
		}
		return $this->response($data);
	}

	public function get_id()
	{
		$param = $this->param('id');
		$data = static::factory($param);
		return $this->response($data);
	}
}

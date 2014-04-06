<?php

class Controller_Api_Colonies extends Controller_Rest
{

	public function get_search_trails()
	{
		$data = Model_Colony::find($this->param('id'), array(
			'related' => array(
				'trails' => array(
					'select' => array('id', 'name'),
					'where' => array(
							array('active', '1'),
					),
				)
			)
		));
		$data = !empty($data['trails']) ? $data['trails'] : array();
		return $this->response($data);
	}
}
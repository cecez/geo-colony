<?php

class Controller_Api_Landholders extends Controller_Rest
{

	public function get_search()
	{
		$query = Input::get('query');
		if (empty($query) || strlen($query) < 3)
		{
			throw new Exception('search can provide query with at least three of lenght');
		}
		$data = Qgram::search(
			'plot_landholders', 
			$query
		);
		return $this->response($data);
	}
}
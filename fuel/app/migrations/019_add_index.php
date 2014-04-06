<?php

namespace Fuel\Migrations;

class Add_index
{
	public function up()
	{
		\DBUtil::create_index('cities', 'state_id');

		\DBUtil::create_index('colonies', 'user_id');

		\DBUtil::create_index('edges', 'user_id');

		\DBUtil::create_index('nucleus', 'user_id');

		\DBUtil::create_index('plot_coordinates', 'plot_id');

		\DBUtil::create_index('plot_landholders', 'user_id');
		\DBUtil::create_index('plot_landholders', 'plot_id');
		\DBUtil::create_index('plot_landholders', 'landholder_id');

		\DBUtil::create_index('plots', 'user_id');
		\DBUtil::create_index('plots', 'colony_id');
		\DBUtil::create_index('plots', 'trail_id');
		\DBUtil::create_index('plots', 'nucleu_id');
		\DBUtil::create_index('plots', 'section_id');
		\DBUtil::create_index('plots', 'edge_id');
		\DBUtil::create_index('plots', 'city_id');

		\DBUtil::create_index('sections', 'user_id');

		\DBUtil::create_index('sources', 'user_id');

		\DBUtil::create_index('states', 'country_id');

		\DBUtil::create_index('trails', 'user_id');
		\DBUtil::create_index('trails', 'colony_id');
	}

	public function down()
	{
		\DBUtil::drop_index('cities', 'state_id');

		\DBUtil::drop_index('colonies', 'user_id');

		\DBUtil::drop_index('edges', 'user_id');

		\DBUtil::drop_index('nucleus', 'user_id');

		\DBUtil::drop_index('plot_coordinates', 'plot_id');

		\DBUtil::drop_index('plot_landholders', 'user_id');
		\DBUtil::drop_index('plot_landholders', 'plot_id');
		\DBUtil::drop_index('plot_landholders', 'landholder_id');

		\DBUtil::drop_index('plots', 'user_id');
		\DBUtil::drop_index('plots', 'colony_id');
		\DBUtil::drop_index('plots', 'trail_id');
		\DBUtil::drop_index('plots', 'nucleu_id');
		\DBUtil::drop_index('plots', 'section_id');
		\DBUtil::drop_index('plots', 'edge_id');
		\DBUtil::drop_index('plots', 'city_id');

		\DBUtil::drop_index('sections', 'user_id');

		\DBUtil::drop_index('sources', 'user_id');

		\DBUtil::drop_index('states', 'country_id');

		\DBUtil::drop_index('trails', 'user_id');
		\DBUtil::drop_index('trails', 'colony_id');		
	}
}
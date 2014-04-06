<?php

namespace Fuel\Migrations;

class Create_plot_coordinates
{
	public function up()
	{
		\DBUtil::create_table('plot_coordinates', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'plot_id' => array('constraint' => 11, 'type' => 'int'),
			'latitude' => array('type' => 'float'),
			'longitude' => array('type' => 'float'),
			'created_at' => array('constraint' => 11, 'type' => 'int'),
			'updated_at' => array('constraint' => 11, 'type' => 'int'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('plot_coordinates');
	}
}
<?php

namespace Fuel\Migrations;

class Create_plot_landholders
{
	public function up()
	{
		\DBUtil::create_table('plot_landholders', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'plot_id' => array('constraint' => 11, 'type' => 'int'),
			'landholder_id' => array('constraint' => 11, 'type' => 'int'),
			'source_id' => array('constraint' => 11, 'type' => 'int'),
			'granting' => array('constraint' => 11, 'type' => 'int'),
			'release' => array('constraint' => 11, 'type' => 'int'),
			'area' => array('constraint' => 11, 'type' => 'int'),
			'price' => array('type' => 'double'),
			'observation' => array('type' => 'text'),
			'created_at' => array('constraint' => 11, 'type' => 'int'),
			'updated_at' => array('constraint' => 11, 'type' => 'int'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('plot_landholders');
	}
}
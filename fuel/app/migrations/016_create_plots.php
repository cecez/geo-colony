<?php

namespace Fuel\Migrations;

class Create_plots
{
	public function up()
	{
		\DBUtil::create_table('plots', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'colony_id' => array('constraint' => 11, 'type' => 'int'),
			'trail_id' => array('constraint' => 11, 'type' => 'int'),
			'nucleu_id' => array('constraint' => 11, 'type' => 'int'),
			'section_id' => array('constraint' => 11, 'type' => 'int'),
			'edge_id' => array('constraint' => 11, 'type' => 'int'),
			'source_id' => array('constraint' => 11, 'type' => 'int'),
			'city_id' => array('constraint' => 11, 'type' => 'int'),
			'number' => array('constraint' => 11, 'type' => 'int'),
			'elevation' => array('constraint' => 11, 'type' => 'int'),
			'approved_by' => array('constraint' => 11, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'type' => 'int'),
			'updated_at' => array('constraint' => 11, 'type' => 'int'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('plots');
	}
}
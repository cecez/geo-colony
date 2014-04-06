<?php

namespace Fuel\Migrations;

class Create_landholders
{
	public function up()
	{
		\DBUtil::create_table('landholders', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'name' => array('constraint' => 255, 'type' => 'varchar'),
			'lastname' => array('constraint' => 255, 'type' => 'varchar'),
			'origin' => array('constraint' => 255, 'type' => 'varchar'),
			'created_at' => array('constraint' => 11, 'type' => 'int'),
			'updated_at' => array('constraint' => 11, 'type' => 'int'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('landholders');
	}
}
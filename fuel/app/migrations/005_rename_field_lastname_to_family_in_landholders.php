<?php

namespace Fuel\Migrations;

class Rename_field_lastname_to_family_in_landholders
{
	public function up()
	{
		\DBUtil::modify_fields('landholders', array(
			'lastname' => array('name' => 'family', 'type' => 'varchar', 'constraint' => 255)
		));
	}

	public function down()
	{
	\DBUtil::modify_fields('landholders', array(
			'family' => array('name' => 'lastname', 'type' => 'varchar', 'constraint' => 255)
		));
	}
}
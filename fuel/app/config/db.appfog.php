<?php

$services_json = json_decode(getenv("VCAP_SERVICES"),true);
$mysql_config = $services_json["mysql-5.1"][0]["credentials"];

return array(
	'default' => array(
		'connection'  => array(
			'dsn'        => 'mysql:host='.$mysql_config['hostname'].';port='.$mysql_config['port'].';dbname='.$mysql_config['name'],
			'username'   => $mysql_config['username'],
			'password'   => $mysql_config['password'],
		),
	),
);

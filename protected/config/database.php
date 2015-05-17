<?php

// This is the database connection configuration.
return array(
	//'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
	// uncomment the following lines to use a MySQL database
	
	'connectionString' => 'mysql:host=appgtadb.db.12460763.hostedresource.com;dbname=appgtadb',
	'emulatePrepare' => true,
	'username' => 'appgtadb',
	'password' => 'jEU5SnjVX2IleTu%',
	'charset' => 'utf8',
        'initSQLs'=>array("set time_zone='+08:00';"),
	
);
<?php

// This is the database connection configuration.
return array(
	//'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
	// uncomment the following lines to use a MySQL database
	
	'connectionString' => 'mysql:host=appgtadevdb.db.12460763.hostedresource.com;dbname=appgtadevdb',
	'emulatePrepare' => true,
	'username' => 'appgtadevdb',
	'password' => 'P2W!TW6x',
	'charset' => 'utf8',
        'initSQLs'=>array("set time_zone='+08:00';"),
	
);
<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Grand Time Automobile',
        'defaultController'=>'site/login',
        //'timeZone' => 'Asia/Manila',
	// preloading 'log' component
	'preload'=>array('log'),
        'catchAllRequest'=>file_exists(dirname(__FILE__).'/.maintenance')? array('site/maintenance') : null,
        // path aliases
        'aliases' => array(
            'bootstrap' => realpath(__DIR__ . '/../extensions/bootstrap'), // change this if necessary
            'yiiwheels' => realpath(__DIR__ . '/../extensions/yiiwheels')
        ),
    
	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
                'application.controllers.*',
                'bootstrap.helpers.TbHtml',
                'bootstrap.helpers.TbArray',
                'bootstrap.components.TbApi',
                'bootstrap.behaviors.TbWidget',
	),
        'behaviors'=>array(
            //'class'=>'application.components.ApplicationBehavior',
            'onBeginRequest' => array(
                'class' => 'application.components.RequireLogin'
            )
        ),
	'modules'=>array(
		// uncomment the following to enable the Gii tool
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'Enter Your Password Here',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		*/
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
                        'class'=>'UserRights',
		),            
                'bootstrap' => array(
                    'class' => 'bootstrap.components.TbApi',
                ),            
                'yiiwheels' => array(
                    'class' => 'yiiwheels.YiiWheels',   
                ),            
                'mailer' => array(
                    'class' => 'application.extensions.mailer.EMailer',
                    'pathViews' => 'application.views.email',
                    'pathLayouts' => 'application.views.email.layouts'
                 ),
                 'file'=>array(
                    'class'=>'application.extensions.file.CFile',
                 ),

		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
                        'showScriptName'=>true,
                        'caseSensitive'=>true,
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
		// database settings are configured in database.php
		'db'=>require(dirname(__FILE__).'/local_database.php'),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
                                 * 
                                 */
				
			),
		),

	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
                'sessionTimeOut'=>3600 * 1, // 1 hour
		// this is used in contact page
		'adminEmail'=>'owliber@yahoo.com',
                'companyName'=>'Grand Time Auto',
	),
);

<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
$console = array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',

	// preloading 'log' component
	'preload'=>array('log'),

	// application components
	'components'=>array(
		// uncomment the following to use a MySQL database
		/*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=testdrive',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
		*/
		'user'=>array(
			'class'=>'application.modules.users.components.WebUser',
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'loginUrl'=>array('users/user/login'),
		),
		// uncomment the following to enable URLs in path-format
		
		// Системные настройки для системы учета времени
		'settings'=>array(
			'class'=>'application.modules.settings.components.Settings',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
	),
	
	'import'=>array(
		'application.components.*',
	),
);

$console['params']['settings.json'] = __DIR__ . DIRECTORY_SEPARATOR .'other'. DIRECTORY_SEPARATOR .'settings.json';
$settings = json_decode(file_get_contents($console['params']['settings.json']), true);

// Объекты баз данных
$console['components']['db'] = $settings['dbEmployees']['value']; // База данных

// Миграции
$console['commandMap']['migrate'] = array(
	'class'=>'system.cli.commands.MigrateCommand',
	'migrationPath'=>'application.migrations',
	'migrationTable'=>'yii_migrations',
	'connectionID'=>'dbTable',
	'templateFile'=>'application.commands.migrationsTemplate.template',
);

return $console;
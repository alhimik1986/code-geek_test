<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

include_once('/../components/CMssqlCommandBuilder.php'); // Костыль с пейджером в mssql
include_once('/../components/CFileCache.php'); // Костыль с кэшированием в файл

$main = array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'УРВ',
	'homeUrl'=>'/timesheet/timesheet/index',
	'defaultController'=>'Site',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.helpers.*',
		'application.components.controller.*',
		'application.modules.users.models.*',
		'application.modules.users.components.*',
		'application.modules.settings.models.SettingsModel',
		'application.modules.js_plugins.components.*',
	),

	'modules'=>array(
		'installer',
		'settings',
		'users',
		'mail',
		'library',
	),

	// application components
	'components'=>array(
		
		// Защита от атак
		'request'=>array(
			'enableCsrfValidation'=>true,
			'enableCookieValidation'=>true,
		),
		
		'user'=>array(
			'class'=>'application.modules.users.components.WebUser',
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'loginUrl'=>array('//users/user/login'),
		),
		// uncomment the following to enable URLs in path-format
		
		// Системные настройки для системы учета времени
		'settings'=>array(
			'class'=>'application.modules.settings.components.Settings',
		),
		
		// Настройки url-адресов
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false, // hide index.php
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
		// Кэширование
		'cache'=>array(
			'class'=>'system.caching.CFileCache', // кэширование в файл
		),
		
		/*'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),*/
		// uncomment the following to use a MySQL database
		/*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=testdrive',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),*/
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction' => ((defined('YII_DEBUG') AND YII_DEBUG) AND ! (defined('PHPUNIT_TEST_MODE') AND PHPUNIT_TEST_MODE)) 
				? (Yii::getVersion() == '1.1.16') ? 'site/error' : array('site/error')
				: (Yii::getVersion() == '1.1.16') ? 'site/error' : array('site/error'),
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
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
	
);

include_once '/../modules/settings/models/SettingsModel.php';
$settings = SettingsModel::getSettings();

$main['components']['db'] = $settings['db']['value']; // База данных

// Отладочная панель, портированная из Yii 2.
if ( defined('YII_DEBUG') AND YII_DEBUG ) {
	// Перезаписываю ключи-значения массива настроек для подключения отладочной панели, портированной из Yii 2.
	$main['preload'] = array('debug');
	$main['components']['debug']['class'] = 'ext.yii2-debug.Yii2Debug';
	$main['components']['db']['enableProfiling'] = true;
	$main['components']['db']['enableParamLogging'] = true;
} else {
	unset($main['components']['log']['routes']['class']);
}

// Генератор кода Gii
if ( defined('YII_DEBUG') AND YII_DEBUG ) {
	$main['modules']['gii'] = array(
		'class'=>'system.gii.GiiModule',
		'password'=>'123',
		'ipFilters'=>array('127.0.0.1','::1'),
		'generatorPaths'=>array(
			'ext.gii',   // псевдоним пути
		),
	);
}

// Интернационализация
$main['components']['messages'] = require('i18n.php');
$main['language'] = $settings['language']['value'];

return $main;
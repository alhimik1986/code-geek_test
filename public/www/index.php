<?php
if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
	// удалить эти строчки на боевом сервере
	defined('YII_DEBUG') or define('YII_DEBUG',true);
	// удалить эти строчки на боевом сервере
	defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
	
	// Эти строки тоже удалить, т.к. они будут выдавать сообщения по всяким мелочам.
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
}

// При необходимости укажите правильный путь к этим файлам
$yii=dirname(__FILE__).'/../../vendors/vendor/yiisoft/yii/framework/yii.php';
$config=dirname(__FILE__).'/../../application/config/main.php';

require_once($yii);
Yii::createWebApplication($config)->run();

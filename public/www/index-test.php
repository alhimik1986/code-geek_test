<?php
/**
 * Это загрузочный файл для тестирования приложения.
 * Этот файл нужно удалить на боевом сервере.
 */

if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1') die();

// Эти строки тоже удалить, т.к. они будут выдавать сообщения по всяким мелочам.
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
	// удалить эти строчки на боевом сервере
	defined('YII_DEBUG') or define('YII_DEBUG',true);
	// удалить эти строчки на боевом сервере
	defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
}
// Этой строкой показываю, что идет тестовый режим, чтобы тестовые сценарии не могли отправлять email-сообщения
// удалить эту строчку на боевом сервере
defined('PHPUNIT_TEST_MODE') or define('PHPUNIT_TEST_MODE',true);


// При необходимости укажите правильный путь к этим файлам
$yii=dirname(__FILE__).'/../../vendors/vendor/yiisoft/yii/framework/yii.php';
$config=dirname(__FILE__).'/../../application/config/test.php';

require_once($yii);
Yii::createWebApplication($config)->run();
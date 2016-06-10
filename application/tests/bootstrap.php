<?php

// Предотвращает закрывание консоля, если возникает ошибка.
// Спасибо: http://stackoverflow.com/questions/19483057/phpunit-close-cmd-windows-when-get-error/19483149#19483149
if (PHP_SAPI === "cli") {
    function __php_cli_press_any_key_shutdown(){
        echo PHP_EOL.PHP_EOL."Press any key to continue...";
        exec('pause');
    }

    register_shutdown_function('__php_cli_press_any_key_shutdown');
}


// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../vendors/vendor/yiisoft/yii/framework/yiit.php';
$config=dirname(__FILE__).'/../config/test.php';

require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');

Yii::createWebApplication($config);

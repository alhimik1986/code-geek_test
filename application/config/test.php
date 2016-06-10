<?php

$main = require(dirname(__FILE__).'/main.php');

$main['language'] = 'en';

$main['params']['settings.json'] = __DIR__ . DIRECTORY_SEPARATOR .'other'. DIRECTORY_SEPARATOR .'settings-test.json';
$settings = CJSON::decode(file_get_contents($main['params']['settings.json']));

// НИ В КОЕМ СЛУЧАЕ НЕ ВПИСЫВАЙТЕ СЮДА РАБОЧИЕ БАЗЫ ДАННЫХ, Т.К. ПРИ ТЕСТИРОВАНИИ ОНИ БУДУТ ЗАТЕРТЫ!!!
// В ФАЙЛЕ settings-test.json ТАКЖЕ НЕЛЬЗЯ УКАЗЫВАТЬ НА РАБОЧИЕ БАЗЫ ДАННЫХ, ИНАЧЕ ОНИ ТАКЖЕ БУДУТ ЗАТЕРТЫ!!!
// Объекты баз данных
$main['components']['db'] = $settings['db']['value'];     // База данных
$main['components']['db']['class'] = 'system.db.CDbConnection';

$main['components']['urlManager']['showScriptName'] = true; // показываю index-test.php, чтобы ссылки содержали этот файл

return CMap::mergeArray(
	$main,
	array(
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
			/* uncomment the following to provide test database connection
			'db'=>array(
				'connectionString'=>'DSN for test database',
			),
			*/
		),
	)
);

<?php

//api.php
// Конфигурация для сбора документации в стиле yii
return array(
	'exclude' => array(
		'/extensions/htmlpurifier',
		'/extensions/yii2-debug',
		'/extensions/yii-EClientScript',
		'/extensions/YiiFirebird',
		'/modules/mail/extensions',
		'/modules/users/extensions',
		'views',
	),
);
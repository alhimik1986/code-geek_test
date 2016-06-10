<?php

// добавляю в файл vendor\composer\autoload_classmap.php возвращаемый ключ-значение:
// 'PHP_Invoker' => $vendorDir . '/phpunit/php-invoker/PHP/Invoker.php',
$orig_string = file_get_contents('../vendor/composer/autoload_classmap.php');
$replace = "    'PHP_Invoker' => \$vendorDir . '/phpunit/php-invoker/PHP/Invoker.php',\n);\n";
if ( ! strpos($orig_string, $replace)) {
	$string = preg_replace("/\);[\n\s]+$/", $replace, $orig_string, $count=1);
	file_put_contents('../vendor/composer/autoload_classmap.php', $string);
}


// добавляю в файл vendor\composer\include_paths.php следующий возвращаемый ключ-значение:
// $vendorDir . '/phpunit/phpunit-invoker',
$orig_string = file_get_contents('../vendor/composer/include_paths.php');
$replace = "    \$vendorDir . '/phpunit/phpunit-invoker',\n);\n";
if ( ! strpos($orig_string, $replace)) {
	$string = preg_replace("/\);[\n\s]+$/", $replace, $orig_string, $count=1);
	file_put_contents('../vendor/composer/include_paths.php', $string);
} else {
	echo iconv('utf-8', 'cp866', 'Замена уже произведена.');
}
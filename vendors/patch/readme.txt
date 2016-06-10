Чтобы тестирование ошибок не выбрасывало исключение, типа:
yii include(PHP_Invoker.php) failed to open stream no such file or directory

добавьте в файл vendor\composer\autoload_classmap.php следующий возвращаемый ключ-значение:
return array(
	'PHP_Invoker' => $vendorDir . '/phpunit/php-invoker/PHP/Invoker.php',
);
и добавьте в файл vendor\composer\include_paths.php следующий возвращаемый ключ-значение:
return array(
    $vendorDir . '/phpunit/phpunit-invoker',
);

Если у вас нет пакета phpunit/phpunit-invoker, то его нужно скачать, например, здесь: https://github.com/sebastianbergmann/php-invoker/tree/master
и распаковать в папку: vendor\phpunit\ , обозвав ее phpunit-invoker

Это приходится делать после каждой установки-обновления пакетов (обновления карты классов).


Все эти операции выполняет patch.bat. Поделайте все это вручную если патч не помогает.
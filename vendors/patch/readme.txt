����� ������������ ������ �� ����������� ����������, ����:
yii include(PHP_Invoker.php) failed to open stream no such file or directory

�������� � ���� vendor\composer\autoload_classmap.php ��������� ������������ ����-��������:
return array(
	'PHP_Invoker' => $vendorDir . '/phpunit/php-invoker/PHP/Invoker.php',
);
� �������� � ���� vendor\composer\include_paths.php ��������� ������������ ����-��������:
return array(
    $vendorDir . '/phpunit/phpunit-invoker',
);

���� � ��� ��� ������ phpunit/phpunit-invoker, �� ��� ����� �������, ��������, �����: https://github.com/sebastianbergmann/php-invoker/tree/master
� ����������� � �����: vendor\phpunit\ , ������� �� phpunit-invoker

��� ���������� ������ ����� ������ ���������-���������� ������� (���������� ����� �������).


��� ��� �������� ��������� patch.bat. ��������� ��� ��� ������� ���� ���� �� ��������.
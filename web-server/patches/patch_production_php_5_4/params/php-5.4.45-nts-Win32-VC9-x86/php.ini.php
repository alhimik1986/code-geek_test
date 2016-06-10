<?php

$php_dir_name = basename(__DIR__);
$tmp_path = __DIR__ .'/../../../../vendor/'.$php_dir_name.'/tmp';
if (!file_exists($tmp_path)) mkdir($tmp_path, 0777, true);
$tmp_path = realpath($tmp_path);
$tmp_path = str_replace('\\', '/', $tmp_path);

return array(
	'{max_execution_time}'  => 'max_execution_time = 600',
	'{error_reporting}'     => 'error_reporting = E_ALL & ~E_DEPRECATED',
	'{post_max_size}'       => 'post_max_size = 100M',
	'{max_input_vars}'      => 'max_input_vars = 10000',
	'{extension_dir}'       => 'extension_dir = "./ext"',
	'{upload_max_filesize}' => 'upload_max_filesize = 100M',
	
	'{extensions}'          => implode("\n", array(
		'extension=php_bz2.dll',
		'extension=php_curl.dll',
		'extension=php_gd2.dll',
		'extension=php_imap.dll',
		'extension=php_mbstring.dll',
		'extension=php_openssl.dll',
		'extension=php_pdo_sqlite.dll',
		'extension=php_xmlrpc.dll',
		'extension=php_xsl.dll',
		
		'extension=php_pdo_sqlsrv_54_nts.dll',
		'extension=php_pdo_firebird.dll',
		'extension=php_pdo_odbc.dll',
		'extension=php_soap.dll',
	)),
	
	'{date.timezone}'       => 'date.timezone = Asia/Irkutsk',
	
	'{session.save_path}'   => 'session.save_path = "'.$tmp_path.'"',
	'{soap.wsdl_cache_dir}' => 'soap.wsdl_cache_dir = "'.$tmp_path.'"',
	
	
	'{xdebug}'              => implode("\n", array(
		//'zend_extension="./ext/php_xdebug-2.2.0-5.3-vc9.dll"',
		'zend_extension="./ext/ZendLoader.dll"',
		'zend_loader.enable=1',
		'zend_loader.disable_licensing=0',
		';zend_loader.obfuscation_level_support=3',
		'zend_loader.license_path='.realpath(__DIR__ .'/../../../../'),
		'',
	)),
);
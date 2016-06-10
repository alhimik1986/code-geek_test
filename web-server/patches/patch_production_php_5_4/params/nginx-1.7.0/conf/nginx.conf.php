<?php

$root_path = realpath(__DIR__ .'/../../../../../../public/www');
$root_path = str_replace('\\', '/', $root_path);
$port = 80;

return array(
	'{root}'                 => 'root "'.$root_path.'";',
	'{set_host_path}'        => 'set $host_path "'.$root_path.'";',
	'{listen_host_ip}'       => 'listen '.gethostbyname(gethostname()).':'.$port.';',
	'{server_name_domain}'   => 'server_name '.gethostname().';',
	'{port}'                 => $port,
	'{client_max_body_size}' => 'client_max_body_size 100M;',
	
	// ssl-сертификаты
	'{https_certificate}'                => implode("\n", array(
		'ssl_certificate     cert/cert.crt;',
		'	ssl_certificate_key cert/cert.key;',
		'	ssl_protocols       SSLv3 TLSv1 TLSv1.1 TLSv1.2;',
		'	ssl_ciphers         HIGH:!aNULL:!MD5;',
	)),
	// указываю порт https-протокола и автоматическое перенаправление на протокол https://
	'{https_protocol}'         => implode("\n", array(
		'listen       443 ssl;',
		'		if ($ssl_protocol = "") {',
		'			rewrite ^   https://$host$request_uri? permanent;',
		'		}',
	)),
	// https-настройки для оптимизации
	'{https_settings}'        => implode("\n", array(
		'ssl_session_timeout 5m;',
		'		ssl_prefer_server_ciphers on;',
		'		resolver 8.8.8.8;',
		'		#ssl_session_cache builtin:1000 shared:SSL:10m;',
		"		add_header Strict-Transport-Security 'max-age=604800';",
		'		ssl_buffer_size 4k;',
	)),
	
	// Максимальное время выполнения
	'{set_timeout_part1}' => implode("\n", array(
		'send_timeout 300;',
		'	proxy_read_timeout 3600;',
	)),
	'{set_timeout_part2}' => 'fastcgi_read_timeout 3600;',
	
	// Для работоспособности функции flush(); на языке php
	'{php_flush}' => 'gzip off; proxy_buffering off;',
	
);
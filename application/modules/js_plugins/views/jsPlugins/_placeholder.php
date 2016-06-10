<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
// Модуль: placeholder - надпись внутри текстового поля для старого Internet Explorer
// **************************************************************************************************************
JSPlugins::setIfNotIsset($plugin_params, 'selector', 'input, textarea');
JSPlugins::setIfNotIsset($plugin_params, 'color', '#aaa');
JsCssFiles::jsFile($baseUrl.'/js/placeholder/jquery.placeholder.js');
JsCssFiles::css('.placeholder {color: '.$plugin_params['color'].';}');
?>
	window.urv.launch.push(function(){
		$('<?php echo $plugin_params['selector']; ?>').placeholder();
	});
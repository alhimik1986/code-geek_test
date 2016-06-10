<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
// Показать-скрыть при клике
// **************************************************************************************************************

JSPlugins::setIfNotIsset($plugin_params, 'selector', '.toggle-parent-next');
JSPlugins::setIfNotIsset($plugin_params, 'focus', '[type=text]:first');
JsCssFiles::css(
	$plugin_params['selector'].':hover{background-color:#ddd; color:#844;}'.
	$plugin_params['selector'].'{
		background-color:#eee;
		border-radius: 5px;
		color: #9f0000;
		cursor:pointer;
		font-size: 17px;
		font-weight: bold;
		padding: 5px;
	}'
);
?>
	$('<?php echo $plugin_params['selector']; ?>').click(function(){
		$(this).parent().next().stop(true, true).slideToggle(500, function(){$('<?php echo $plugin_params['focus'] ? $plugin_params['focus'] : 'false'; ?>:visible').focus();});
	});

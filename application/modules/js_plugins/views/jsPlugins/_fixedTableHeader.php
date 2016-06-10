<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
// модуль: fixedTableHeader - прилипание шапки таблицы.
// **************************************************************************************************************
JsCssFiles::jsFile($baseUrl.'/js/fixedTableHeader/jquery-fixed-table-header.js');
foreach($plugin_params as $key=>$value)
	JSPlugins::setIfNotIsset($plugin_params[$key], 'selector', '#fixed-table-header');
?>
<?php foreach($plugin_params as $key => $value): ?>
	if ($('<?php echo $value['selector']; ?>').length != 0)
		$('<?php echo $value['selector']; ?>').fixedTableHeader();
<?php endForeach; ?>

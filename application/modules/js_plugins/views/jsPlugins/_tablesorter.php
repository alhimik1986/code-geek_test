<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
// модуль: tablesorter - сортировка в таблице и прилипание шапки fixedTableHeader
// **************************************************************************************************************
JsCssFiles::jsFile($baseUrl.'/js/tablesorter/js/jquery.tablesorter.min.js');
JsCssFiles::jsFile($baseUrl.'/js/fixedTableHeader/jquery-fixed-table-header.js');
JSPlugins::setIfNotIsset($plugin_params, 'selector', '#tablesorter');
JSPlugins::setIfNotIsset($plugin_params, 'columnFilters', 'false');
JSPlugins::setIfNotIsset($plugin_params, 'html', false);

if (isset($plugin_params['tablefilter'])) {
	JsCssFiles::jsFile($baseUrl.'/js/tablesorter/js/jquery.tablesorter.widgets.min.js');
	JSPlugins::setIfNotIsset($plugin_params['tablefilter'], 'selector', '#tablefilter');
	JSPlugins::setIfNotIsset($plugin_params['tablefilter'], 'search', '#quickfind');
	JSPlugins::setIfNotIsset($plugin_params['tablefilter'], 'reset', '#cleanfilters');
	JSPlugins::setIfNotIsset($plugin_params['tablefilter'], 'showFilters', 'false');
	JSPlugins::setIfNotIsset($plugin_params['tablefilter'], 'delay', 100);
}
?>
(function($){
	var selector = '<?php echo $plugin_params['selector']; ?>';
	if ($(selector).length) $(selector).fixedTableHeader();
	
	var options = {};
	<?php // Первое число - номер столбца (нумерация с 0), второе - сортировка (0 - по возр., 1 - по убыванию) ?>
	//options['sortList'] = [[1,0]],
	
	<?php if ($plugin_params['html']): // Сортирую только текст в тегах, а не html-теги. ?>
	//options['textExtraction'][0] = function(node) { return $(node).text(); };
	<?php endif; ?>
	
	options['widgets'] = [];
	options['widgetOptions'] = {};
	<?php if (isset($plugin_params['tablefilter'])): // Поиск в таблице ?>
	options['widgets'].push('filter');
	options['widgetOptions']['filter_external'] = '<?php echo $plugin_params['tablefilter']['search']; ?>';
	options['widgetOptions']['filter_columnFilters'] = <?php echo $plugin_params['tablefilter']['columnFilters']; ?>;
	options['widgetOptions']['filter_saveFilters'] = true;
	options['widgetOptions']['filter_reset'] = '<?php echo $plugin_params['tablefilter']['reset']; ?>';
	options['widgetOptions']['filter_searchDelay'] = '<?php echo $plugin_params['tablefilter']['delay']; ?>';
	<?php endif; ?>
		
	/*,widgets: ['stickyHeaders'],
	widgetOptions: {
		stickyHeaders : 'tablesorter-stickyHeader'
	}*/
	
	window.urv.launch.push(function(){
		var $table = $(selector).tablesorter(options);
	}, 100);
})(jQuery);
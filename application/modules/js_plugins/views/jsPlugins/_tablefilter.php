<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
// модуль: tablefilter - для поиска в таблице picnet.table.filter
// **************************************************************************************************************
JsCssFiles::jsFile($baseUrl.'/js/tablefilter/picnet.table.filter.min.js');
JsCssFiles::jsFile($baseUrl.'/js/fixedTableHeader/jquery-fixed-table-header.js');
// Значения по умолчанию, если они не установлены
JSPlugins::setIfNotIsset($plugin_params, 'selector', '#tablefilter');
JSPlugins::setIfNotIsset($plugin_params, 'search', '#quickfind');
JSPlugins::setIfNotIsset($plugin_params, 'clear', '#cleanfilters');
JSPlugins::setIfNotIsset($plugin_params, 'showFilters', false);
if ($plugin_params['showFilters'])
	JsCssFiles::css($plugin_params['selector'] . ' thead tr.filters {display:table-row;}');
?>
	window.urv.tableFilter = function(selector){
		$(selector).not('.table-filter-inited').addClass('table-filter-inited').tableFilter({
			additionalFilterTriggers: [$('<?php echo $plugin_params['search']; ?>')],
			clearFiltersControls: 
<?php if ($plugin_params['clear']): ?>
[$('<?php echo $plugin_params['clear']; ?>')],
<?php else: ?>
[],
<?php endIf; ?>
			filterDelay: '100',
			enableCookies: false
		});
	};

	$('<?php echo $plugin_params['search']; ?>').keyup(function(e){
		window.urv.tableFilter('<?php echo $plugin_params['selector']; ?>');
		if (e.keyCode==27) $(this).val('').trigger('keyup'); <?php // Если нажат Esc, то очищаю результаты поиска ?>
	}).focus();
	
	
	<?php // Не даю сортировщику таблиц сортировать таблицу, если был клик по текстовому полю поиска в шапке таблицы ?>
	<?php if ($plugin_params['showFilters'] AND isset($plugins['tablesorter'])): ?>
		$('<?php echo $plugin_params['selector'];?> thead .filters td').addClass('sorter-false').sortDisabled = true;
	<?php endif; ?>

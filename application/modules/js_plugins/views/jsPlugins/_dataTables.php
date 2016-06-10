<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
// Модуль: DataTables - Поиск и сортировка в таблице
// **************************************************************************************************************
JsCssFiles::jsFile($baseUrl.'/js/DataTables/media/js/jquery.dataTables.js');
JsCssFiles::jsFile($baseUrl.'/js/DataTables_plugins/api/fnLengthChange.js');
JsCssFiles::cssFile($baseUrl.'/css/urv-table.css');
JsCssFiles::cssFile($baseUrl.'/js/DataTables/media/css/jquery.dataTables.css');

//JsCssFiles::jsFile($baseUrl.'/js/DataTables/extras/FixedHeader/js/dataTables.fixedHeader.min.js');
JsCssFiles::jsFile($baseUrl.'/js/fixedTableHeader/jquery-fixed-table-header.js');

JsCssFiles::jsFile($baseUrl.'/js/DataTables/extras/ColVis/media/js/dataTables.colVis.min.js');
JsCssFiles::cssFile($baseUrl.'/js/DataTables/extras/ColVis/media/css/dataTables.colVis.css');

if (isset($plugin_params[0]['options']['tooltip'])) {
	JsCssFiles::jsFile($baseUrl.'/js/tooltip/jquery.tooltip.js');
	JsCssFiles::cssFile($baseUrl.'/js/tooltip/jquery.tooltip.css');
}

foreach($plugin_params as $key=>$value) {
	JSPlugins::setIfNotIsset($plugin_params[$key], 'selector', '#datatables');
	JSPlugins::setIfNotIsset($plugin_params[$key], 'onload', true);
	JSPlugins::setIfNotIsset($plugin_params[$key], 'options', array());
	
	if (isset($plugin_params[$key]['options']['tooltip'])) {
		JSPlugins::setIfNotIsset($plugin_params[$key]['options']['tooltip'], 'selector', 'tr');
		JSPlugins::setIfNotIsset($plugin_params[$key]['options']['tooltip'], 'options', array(
			'delay' => 0,
			'track' => true,
			'fade'  => 250,
		));
	}
}
?>
window.urv.dataTables = {};
window.urv.updateTooltip = {};

(function($){
<?php foreach($plugin_params as $key=>$value): ?>
	var selector = '<?php echo $plugin_params[$key]['selector']; ?>';
	
	var options = {
		// bInfo: false,                                                      // Показывать число записей в таблице
		// bPaginate: false,                                                  // Пейджер
		
		sPaginationType: 'full_numbers',                                      // Тип пейджера (расширенный)
		bStateSave: false,                                                    // Сохранять в cookie строку поиска
		sDom: 'C<"top"flip><"clear">rt<"bottom"ip>',                          // Расположение элементов
		oColVis: {                                                            // Панель "Показывать-скрыть колонки" при наведении мыши
			//activate: 'mouseover'
		},
		aLengthMenu: [[10, 30, 100, -1], [10, 30, 100, '<?php echo Yii::t('settings.app', 'All'); ?>']], // Варианты числа строк на странице
		iDisplayLength: -1,                                                   // Число строк в странице по умолчанию (Все)
		
        // aoColumnDefs: [{'bSortable': false, 'aTargets':[1]}],              // Игнорировать сортировку
		// aaSorting: [[ 1, 'asc' ]],                                         // Сортировка колонок по умолчанию
		aaSorting: [],                                                        // Не сортировать по умолчанию
		
		fnDrawCallback: function(o){                                          // Автоматически скрываю пейджер, если в нем нечего листать
			if (o._iDisplayLength == -1) {
				$(o.nTableWrapper).find('.dataTables_paginate').hide();
			} else if (o.aoData.length <= o._iDisplayLength) {
				$(o.nTableWrapper).find('.dataTables_paginate').hide();
			} else {
				$(o.nTableWrapper).find('.dataTables_paginate').show();
			}
		},
		fnInitComplete: function(){                                           // Очищаю поиск при нажатии кнопки "Esc"
			var that = this;
			that.parents('.dataTables_wrapper').find('.dataTables_filter input').on('keyup', function(e){
				if (e.which == 27) {
					$(this).val('');
					that.fnFilter('');
				}
			}).attr('title', '<?php echo Yii::t('settings.app', 'Press "Esc" to clear the search field'); ?>.')
			.parents('.dataTables_wrapper').find('.ColVis_MasterButton span').html('<?php echo Yii::t('settings.app', 'Show / Hide columns'); ?>');
		},
		// Перевод интерфейса
		oLanguage: {
			sLengthMenu: '<?php echo Yii::t('settings.app', 'Show: _MENU_ rows'); ?>',
			sZeroRecords: '<?php echo Yii::t('settings.app', 'No data.'); ?>',
			sInfo: '<?php echo Yii::t('settings.app', 'Rows: _START_ - _END_ of _TOTAL_'); ?>',
			sInfoEmpty: '<?php echo Yii::t('settings.app', 'Rows: 0 - 0 of 0'); ?>',
			sInfoFiltered: '<?php echo Yii::t('settings.app', '(Found in _MAX_ rows)'); ?>',
			sSearch: '<?php echo Yii::t('settings.app', 'Search:'); ?>',
			oPaginate: {
				sNext: '<?php echo Yii::t('settings.app', 'Next'); ?>',
				sPrevious: '<?php echo Yii::t('settings.app', 'Prev'); ?>',
				//sFirst: '<?php echo Yii::t('settings.app', 'First'); ?>',
				sFirst: '<?php echo Yii::t('settings.app', 'First.'); ?>',
				//sLast: '<?php echo Yii::t('settings.app', 'Last'); ?>'
				sLast: '<?php echo Yii::t('settings.app', 'Last.'); ?>'
			},
			sLoadingRecords: '<?php echo Yii::t('settings.app', 'Loading...'); ?>',
			sProcessing: '<?php echo Yii::t('settings.app', 'Processing...'); ?>',
			oAria: {
				sSortAscending: '<?php echo Yii::t('settings.app', ': enable sort by asc'); ?>',
				sSortDescending: '<?php echo Yii::t('settings.app', ': enable sort by desc'); ?>'
			}
		}
	};
	$.fn.dataTable.ext.oPagination.iFullNumbersShowPages = 6; // Число страниц в пейджере
	
	var _options = <?php echo CJavaScript::encode($plugin_params[$key]['options']); ?>;
	for(var key in _options) {
		options[key] = _options[key];
	}
	
	if (typeof(options['tooltip']) !== 'undefined') delete options['tooltip']; // Удаляю постороннюю настройку
	
	<?php  // Настройки для каждого dataTables ?>
	window.urv.dataTables[selector] = {
		selector: selector,
		options: options
	};



<?php if (isset($plugin_params[$key]['options']['tooltip'])): // Продедура для обновления всплывающих подсказок в таблице ?>
	// Чтобы обновить tooltip во всей таблице (или во всем блоке), необходимо в параметре $dataTables указать jQuery-объект этой таблицы (блока)
	// Если нужно обновить tooltip только одного элемента, то указывают jQuery-объект этого элемента
	window.urv.updateTooltip[selector] = function($dataTables) {
		if ($dataTables.is('<?php echo $plugin_params[$key]['options']['tooltip']['selector']; ?>')) {
			$dataTables
				.tooltip(<?php echo CJavaScript::encode($plugin_params[$key]['options']['tooltip']['options']); ?>);
		} else {
			$dataTables
				.find('<?php echo $plugin_params[$key]['options']['tooltip']['selector']; ?>')
				.tooltip(<?php echo CJavaScript::encode($plugin_params[$key]['options']['tooltip']['options']); ?>);
		}
	};
<?php endif; ?>


<?php if ($plugin_params[$key]['onload']): // Если разрешен запуск во время загрузки страницы ?>
	window.urv.launch.push(function(){
		var selector = '<?php echo $plugin_params[$key]['selector']; ?>';
		var options  = window.urv.dataTables[selector]['options'];
		
		if ($(selector).length != 0) {
			if (typeof(window.urv.updateTooltip[selector]) != 'undefined') window.urv.updateTooltip[selector]($(selector));
			window.urv.dataTables[selector]['dom'] = $(selector).dataTable(options);
			<?php if ( ! isset($plugin_params[$key]['fixedTableHeader']) OR $plugin_params[$key]['fixedTableHeader']): ?>
			$(selector).fixedTableHeader();  // Прилипание шапки таблицы
			//new FixedHeader( window.urv.dataTables ); // Прилипание шапки таблицы (Не довели до ума разработчики)
			<?php endIf; ?>
			$('.dataTables_filter input:first').focus();
		}
	});
<?php endIf; ?>

<?php endForeach; ?>
})(jQuery);
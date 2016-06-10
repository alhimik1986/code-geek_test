<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
// Поиск сотрудника (для админов) 
// **************************************************************************************************************
?>
<?php if (Yii::app()->user->inGroup['ADMINS']): ?>
<?php
JsCssFiles::cssFile($baseUrl.'/css/urv-table.css');
?>
(function($){
	// Инициализация поиска сотрудника при нажатии на клавишу в текстовом поле или изменении любого из параметров поиска
	var searchEmployee_timeout = 300; searchEmployee_timer = function(){};	
	var init_search_form = function(form) {
		form.find('.search-on-change').on('keyup', function(e){
			var self = $(this);
			if (e.keyCode == 27) self.val('');
			
			// задежка между отправками формы
			if (searchEmployee_timer) {
				clearTimeout(searchEmployee_timer);
			}
			searchEmployee_timer = setTimeout(function(){
				self.parents('form').trigger('search');
			}, searchEmployee_timeout);
		}).on('change', function(e){
			var self = $(this);
			
			// задежка между отправками формы
			if (searchEmployee_timer) {
				clearTimeout(searchEmployee_timer);
			}
			searchEmployee_timer = setTimeout(function(){
				self.parents('form').trigger('search');
			}, searchEmployee_timeout);
		});
	};
	// Появление формы поиска сотрудников
	new ajaxForm({
		csrf: {<?php echo Yii::app()->request->csrfTokenName . ":'" . Yii::app()->request->csrfToken."'"; ?>},
		loadingDom: function(settings){return '#urv-table'},
 		form: {
			selector: '#search-employee-ajax-form'
		},
		create: {
			delegator: document,
			selector: '.search-employee',
			on: 'click',
			ajax: function(settings) {
				return {
					url: '<?php echo Yii::app()->getController()->createUrl('//employees/search/form'); ?>'
				};
			},
			success: function(data, settings) {
				$(settings.form.selector).remove();
				// В этой функции обязательно нужно вернуть jQuery-объект полученной формы для ее последующей обработки
				return $(data).appendTo('body');
			},
			afterSuccess: function(settings) {
				var form = settings.form.dom;
				init_search_form(form);
				init_sort_table('.search-employee-results');
				form.find('input[name="Employees[fio]"]').focus();
				form.find('form').trigger('search');
			}
		}
	});
	//--------------------------------------------------------------
	// Поиск при изменении любого из параметров поиска
	//--------------------------------------------------------------
	new ajaxForm({
		csrf: {<?php echo Yii::app()->request->csrfTokenName . ":'" . Yii::app()->request->csrfToken."'"; ?>},
		loadingDom: function(settings){return '.search-employee-results-table';},
		create: {
			selector: '#search-employee-form',
			on: 'search next',
			ajax: function(settings) {
				var form = settings.create.dom;
				var data = form.serializeArray();
				
				// Добавляю параметры сортировки в отравляемые данные
				var key, column;
				for (key in sorting) {
					column = key.replace(new RegExp(',', 'g'),  ' '+sorting[key]+','); // Чтобы вместо "{last_name}, {first_name}, {middle_name} desc" получилось "{last_name} desc, {first_name} desc, {middle_name} desc"
					data.push({
						name: 'order['+column+']',
						value: sorting[key]
					});
				}
				
				return {
					url: form.attr('action'),
					data: data,
					type: 'post'
				};
			},
			success: function(data, settings) {
				var form = settings.create.dom;
				form.find('.search-employee-results').html(data);
				// Пейджер таблиц
				form.find('.urv-pager .pager-links a').on('click', function(){
					$(this).parents('form').trigger('next');
					return false;
				});
				// Увеличение размера при наведении
				window.urv.zoomImages(form);
				
				// Перехожу на первую страницу пейджера, если на текущей странице нет результатов
				var $table = settings.create.dom;
				if ($table.find('tbody tr.tr-pager').length > 0 && $table.find('tbody tr:not(.tr-pager)').length == 0) {
					$table.find('tr.tr-pager').append('<div class="pager-links"><input type="hidden" id="page" name="page" value="1"></div>');
					$table.trigger('search');
				}
				// Обновляю всплывающую подсказку
				var tooltip_selector = '<?php //echo $tooltip_selector; ?>';
				if (tooltip_selector)
					$(window.urv.tooltip[tooltip_selector]['selector']).tooltip(window.urv.tooltip[tooltip_selector]['options']);
			},
			_afterSuccess: function(){}
		}
	});
	//--------------------------------------------------------------
	// Сортировка
	//--------------------------------------------------------------
	var sorting = {};
	// Установить сортировку по возрастанию
	var set_asc = function($th, e){
		if ( ! e.ctrlKey && ! e.shiftKey) {
			$th.parent().find('th').removeClass('sorting_asc').removeClass('sorting_desc');
		}
		$th.removeClass('sorting_desc').addClass('sorting_asc');
	};
	// Установить сортировку по убыванию
	var set_desc = function($th, e){
		if ( ! e.ctrlKey && ! e.shiftKey) {
			$th.parent().find('th').removeClass('sorting_asc').removeClass('sorting_desc');
		}
		$th.removeClass('sorting_asc').addClass('sorting_desc');
	};
	// Убрать сортировку
	var set_default = function($th, e){
		if ( ! e.ctrlKey && ! e.shiftKey) {
			$th.parent().find('th').removeClass('sorting_asc').removeClass('sorting_desc');
		}
		$th.removeClass('sorting_asc').removeClass('sorting_desc');
	}
	// Действие при клике на шапку таблицы, возвращает порядок сортировки для данной таблицы
	var get_order = function($th, e){
		var order;
		if ($th.hasClass('sorting_asc')){
			set_desc($th, e);
			order = 'desc';
		} else if ($th.hasClass('sorting_desc')){
			set_default($th, e);
			order = '';
		} else {
			set_asc($th, e);
			order = 'asc';
		}
		return order;
	};
	// Устанавливает сортировку при клике по шапкам таблиц
	var init_sort_table = function(delegator) {
		$(delegator).on('click', 'table.search-employee-results-table thead th[column_name]', function(e){
			var $th = $(this);
			if ( ! e.ctrlKey && ! e.shiftKey)
				sorting = {};
			
			sorting[$th.attr('column_name')] = get_order($th, e);
			
			$('#search-employee-form').trigger('search');
		});
	}
	//--------------------------------------------------------------



	// Закрываю изображение при клике на пустое место
	$(document).on('click', function(e){
		if ( $(e.target).css('position') != 'fixed') {
			$('.ajax-form').find('img').filter(function(){
				return ($(this).css('position') === 'fixed');
			}).trigger('zoomOut');
		}
	});

	// Увеличение размера при наведении
	window.urv.zoomImages = function(form) {
		form.find('img').load(function(e) {
			var img = $(this);
			// Сохраняю размеры и позищию миниатюры изобразения
			img.data('width', img.width());
			img.data('height', img.height());
			img.data('top', img.offset().top);
			img.data('left', img.offset().left);
			// Сохраняю оригинальные размеры изображения
			img.removeAttr('width');
			img.data('origWidth', img.width());
			img.css({'width': img.data('width')});
		}).on('zoomIn', function(e) {
			// Закрываю предыдущие изображения
			form.find('img').filter(function(){
				return ($(this).css('position') === 'fixed');
			}).trigger('zoomOut');
				
			var img = $(this);
			if ( ! $.hasData(img[0])) return;
			// Заглушка вместо рисунка
			img.after('<div class="image-height" style="height:'+img.data('height')+'px; width:'+img.data('width')+'px;"></div>');
			
			// Определяю позицию изображения
			var left = (img.offset().left - $(window).scrollLeft());
			var top  = (img.offset().top - $(window).scrollTop());
			img.css({'position': 'fixed', 'left': left+'px', 'top': top+'px'});
			
			img.stop().animate({
				width: img.data('origWidth')+'px'
				}, function(){
					var left = (img.offset().left - $(window).scrollLeft());
					var top  = (img.offset().top - $(window).scrollTop());
					if (top > $(window).height() - img.height()) top = $(window).height() - img.height() - 5;
					if (left > $(window).width() - img.width()) left = $(window).width()  - img.width() - 5;
					if (top < 0 ) top = 0;
					if (left < 0 ) left = 0;
					img.css({'left': left+'px', 'top': top+'px'});
					if (img.width() > $(window).width()) {
						img.css({height: 'auto'});
						img.css({width: $(window).width()-10});
					}
					if (img.height() > $(window).height()) {
						img.css({width: 'auto'});
						img.css({height: $(window).height()-10});
					}
					
					// Делаю фотку перемещаемой
					if ($.fn.drag) {
						img.drag(function(ev, dd){
							$(this).css({
								top: dd.offsetY - $(window).scrollTop(),
								left: dd.offsetX - $(window).scrollLeft()
							});
						});
					}
			});
		}).on('zoomOut', function(e) {
			var img = $(this);
			if ( ! $.hasData(img[0])) return;
			img.css({'left': img.data('left') - $(window).scrollLeft(), 'top': img.data('top') - $(window).scrollTop()});
			
			img.stop().animate({
				width: img.data('width'),
				height: 'auto'
			}, function(){
				img.parent().find('div.image-height').remove();
				img.css({'position': 'static'});
				img.css({'height': 'auto'});
			});
		}).on('click', function(){
			var img = $(this);
			if (img.css('position') == 'fixed') img.trigger('zoomOut');
			else img.trigger('zoomIn');
		});
	};
})(jQuery);
	<?php JSPlugins::css('
		table.search-employee-results-table thead {
			background-color: transparent !important;
			background-image: none;
		}
		table.search-employee-results-table th {
			border-color: #aaa #aaa #aaa #fff !important;
			border-style: none;
			border-width: 0px;
			border-top: none ;
			
			border-bottom: 1px solid #aaa !important;
			border-right: 1px solid #aaa !important;
		}
		table.search-employee-results-table {
			background-color: transparent;
			border-left: 1px solid #aaa !important;
			border-spacing: 0;
		}
		table.search-employee-results-table thead tr th:first-child {
			border-radius: 0px;
		}
		table.search-employee-results-table thead tr th:last-child {
			border-radius: 0px;
		}
		table.search-employee-results-table td {
			padding: 3px;
		}
		table.search-employee-results-table tbody tr.odd, table.search-employee-results-table tbody tr.even {
			cursor: default;
		}
		table.search-employee-form-table th {
			vertical-align:bottom;
			font-weight:bold;
		}
		#search-employee-ajax-form input[type="text"] {
			padding:2px 3px;
		}
		table.search-employee-results-table > tbody > tr > td {
			height: 15px;
			vertical-align: middle;
			padding: 3px 5px;
		}
		table.search-employee-results-table > tbody > tr.tr-pager > td {
			height: 30px;
		}
		');
	?>
<?php endIf; ?>
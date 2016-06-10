<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
//модуль: chosen - выпадающий список с поиском. chosen.jquery.js 
// **************************************************************************************************************
JsCssFiles::cssFile($baseUrl.'/js/chosen/chosen.css');
JsCssFiles::jsFile($baseUrl.'/js/chosen/chosen.jquery.js');
JSPlugins::setIfNotIsset($plugins['chosen'], 'selector', '.chosen');

JsCssFiles::css('
	.chzn-container .chzn-results {max-height: 220px;}
');
?>
	window.urv.chosen = function (selector, css){
		var $this = $(selector);
		var dom = $this.chosen({search_contains: true, results_none_found: 'Ничего не найдено по'});
		dom = dom.next();
		if (css !== false && ! $this.hasClass('no-css')) {
			dom.find('.chzn-results .group-result').css('color', '#222');
			dom.find('.chzn-results').css('padding','5px 0');
			dom.find('.chzn-results li').css('padding','0 0 0 5px');
			dom.find('.chzn-results li.group-option').css('margin-left', '10px');
		}
	};
	window.urv.chosenUpdate = function (selector, css){
		var $this = $(selector);
		var dom = $this.trigger('liszt:updated');
		dom = dom.next();
		if (css !== false && ! $this.hasClass('no-css')) {
			dom.find('.chzn-results .group-result').css('color', '#222');
			dom.find('.chzn-results').css('padding','5px 0');
			dom.find('.chzn-results li').css('padding','0 0 0 5px');
			dom.find('.chzn-results li.group-option').css('margin-left', '10px');
		}
	};
	// Выбирает все дочерние опции при клике на родителя в выпадающем списке
	window.urv.chosenSelectParents = function(id) {
		$(document).on('click', id+'_chzn .chzn-results li', function(){
			if ( ! $(this).hasClass('group-result')) return;
			var regExp = /.+_(\d+)$/gi;
			var i = regExp.exec($(this).attr('id'))[1];
			i = parseInt(i);
			var values = $(id).val();
			values = (values === null) ? [] : values;
			$(id).find('*').eq(i).children('option').each(function(){
				values.push($(this).attr('value'));
			});
			$(id).val(values);
			window.urv.chosenUpdate(id);
			$(id).trigger('change');
		});
	};
	// Очищает выбранные элементы в выпадающем списке
	window.urv.chosenReset = function(id) {
		if ($(id).val() == null) return;
		$(id).val({});
		window.urv.chosenUpdate(id);
		$(id).trigger('change');
	};
	window.urv.choosen = window.urv.chosen;
	window.urv.choosenUpdate = window.urv.chosenUpdate;
	window.urv.choosenReset = window.urv.chosenReset;

	window.urv.launch.push(function(){
		window.urv.chosen('<?php echo $plugin_params['selector']; ?>');
	});
<?php if (false): ?></script><?php endif; ?>
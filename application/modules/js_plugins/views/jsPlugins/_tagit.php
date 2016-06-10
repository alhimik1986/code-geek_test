<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
// Модуль: tagit - теггер (инструмент для выбора адресатов почты)
// **************************************************************************************************************
JsCssFiles::jsFile($baseUrl.'/js/jquery-tagit/lib/jquery.tagit.js');
JsCssFiles::cssFile($baseUrl.'/js/jquery-tagit/css/tagit.css');
?>
	/**
	 * @param string selector css-селектор элемента, к которому применить плагин
	 * @param array  tags массив имеющихся адресатов почты (тегов)
	 * @param string name имя поля, в котором хранятся выбранные адресаты почты (теги)
	 */
	window.urv.tagit = function(selector, tags, name) {
		$(selector).tagit({
			tags: tags,
			field: name,
			maxCount: 9
		});
	};
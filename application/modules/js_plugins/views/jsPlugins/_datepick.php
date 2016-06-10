<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
// Модуль: календарь (datepicker) - jquery-каледарь, превращающий в календарь простое текстовое поле.
// **************************************************************************************************************	
JsCssFiles::cssFile($baseUrl.'/js/datepick/jquery.datepick.css');
JsCssFiles::jsFile($baseUrl.'/js/datepick/jquery.datepick.min.js');
JsCssFiles::jsFile($baseUrl.'/js/datepick/jquery.datepick-ru.js');

$paramString = array();
if ($plugin_params) {
	foreach($plugin_params as $key=>$value) {
		JSPlugins::setIfNotIsset($plugin_params[$key]['options'], 'dateFormat', 'dd-mm-yyyy');
		JSPlugins::setIfNotIsset($plugin_params[$key]['options'], 'defaultDate', '');
		JSPlugins::setIfNotIsset($plugin_params[$key]['options'], 'minDate', '');
		JSPlugins::setIfNotIsset($plugin_params[$key]['options'], 'maxDate', '');
		JSPlugins::setIfNotIsset($plugin_params[$key]['options'], 'onSelect', '');
	}
}
?>
(function($){
<?php foreach($plugin_params as $key => $value): ?>
	var selector = '<?php echo trim($plugin_params[$key]['selector']); ?>';
	$(document).on('click', selector+':not(.hasDatepick)', function(){
		$(this).datepick(<?php echo CJavaScript::encode($plugin_params[$key]['options']); ?>);
	}).on('focus', selector+':not(.hasDatepick)', function(){
		$(this).datepick(<?php echo CJavaScript::encode($plugin_params[$key]['options']); ?>);
	});
<?php endforeach; ?>
})(jQuery);
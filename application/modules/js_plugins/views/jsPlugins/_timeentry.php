<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
// Модуль: время (timeentry) - модуль превращающий текстовое поле в поле для ввода времени.
// **************************************************************************************************************
JsCssFiles::cssFile($baseUrl.'/js/timeentry/jquery.timeentry.css');
JsCssFiles::jsFile($baseUrl.'/js/timeentry/jquery.timeentry.js');
JsCssFiles::jsFile($baseUrl.'/js/timeentry/jquery.mousewheel.js');
JsCssFiles::jsFile($baseUrl.'/js/jquery.browser.js');

if ($plugin_params) {
	foreach($plugin_params as $key=>$value) {
		JSPlugins::setIfNotIsset($plugin_params[$key]['options'], 'timeSteps', array(1,30,0));
		JSPlugins::setIfNotIsset($plugin_params[$key]['options'], 'spinnerImage', '');
		JSPlugins::setIfNotIsset($plugin_params[$key]['options'], 'defaultTime', '00:00');
		JSPlugins::setIfNotIsset($plugin_params[$key]['options'], 'scrollOnFocus', true);
	}
}
?>
(function($){
<?php foreach($plugin_params as $key => $value): ?>
	var selector = '<?php echo trim($plugin_params[$key]['selector']); ?>';
	$(document).on('click', selector+':not(.hasTimeEntry)', function(){
		$(this).timeEntry(<?php echo CJavaScript::encode($plugin_params[$key]['options']); ?>);
	}).on('focus', selector+':not(.hasTimeEntry)', function(){
		$(this).timeEntry(<?php echo CJavaScript::encode($plugin_params[$key]['options']); ?>);
	});
<?php if ($plugin_params[$key]['options']['scrollOnFocus']): ?>
	$(document).on('click', selector+'.hasTimeEntry', function(){
		$(this).timeEntry('option', {useMouseWheel: true});
	});
	$(document).on('blur', selector+'.hasTimeEntry', function(){
		$(this).timeEntry('option', {useMouseWheel: false});
	});
<?php endif; ?>
	
<?php endforeach; ?>
})(jQuery);
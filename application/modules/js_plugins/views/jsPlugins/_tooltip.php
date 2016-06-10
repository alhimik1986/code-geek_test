<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
// Модуль: tooltip - всплывающие подсказки
// **************************************************************************************************************
JsCssFiles::jsFile($baseUrl.'/js/tooltip/jquery.tooltip.js');
JsCssFiles::cssFile($baseUrl.'/js/tooltip/jquery.tooltip.css');

foreach($plugin_params as $key=>$value) {
	if ( ! isset($plugin_params[$key]['options']))
		$plugin_params[$key]['options'] = array();
	JSPlugins::setIfNotIsset($plugin_params[$key]['options']['tooltip'], 'selector', 'tr');
	JSPlugins::setIfNotIsset($plugin_params[$key]['options']['tooltip'], 'onload',  true);
	JSPlugins::setIfNotIsset($plugin_params[$key]['options']['tooltip'], 'dataTables',  false);
	JSPlugins::setIfNotIsset($plugin_params[$key]['options']['tooltip']['options'], 'delay', 0);
	JSPlugins::setIfNotIsset($plugin_params[$key]['options']['tooltip']['options'], 'track', true);
	JSPlugins::setIfNotIsset($plugin_params[$key]['options']['tooltip']['options'], 'fade',  250);
}
?>

window.urv.tooltip = {};
window.urv.updateTooltip = {};
<?php foreach($plugin_params as $key=>$value): ?>
	window.urv.tooltip['<?php echo $plugin_params[$key]['selector']; ?>'] = {
		selector: '<?php echo $plugin_params[$key]['selector']; ?>',
		options: <?php echo CJavaScript::encode($plugin_params[$key]['options']); ?>
	};
	window.urv.updateTooltip['<?php echo $plugin_params[$key]['selector']; ?>'] = function($dom) {
		if ($dom.is('<?php echo $plugin_params[$key]['selector']; ?>')) {
			$dom
				.tooltip(<?php echo CJavaScript::encode($plugin_params[$key]['options']); ?>);
		} else {
			$dom
				.find('<?php echo $plugin_params[$key]['selector']; ?>')
				.tooltip(<?php echo CJavaScript::encode($plugin_params[$key]['options']); ?>);
		}
	};

<?php if ($plugin_params[$key]['onload']): // Если разрешен запуск во время загрузки страницы ?>
	window.urv.launch.push(function(){
		var selector = '<?php echo $plugin_params[$key]['selector']; ?>';
		var options  = window.urv.tooltip[selector]['options'];
		var $dom = $(selector);
		if ($dom.length != 0) {
			$dom.tooltip(options);
		}
	});
<?php endIf; ?>

<?php endForeach; ?>
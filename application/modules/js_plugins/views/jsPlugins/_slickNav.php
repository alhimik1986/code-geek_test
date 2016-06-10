<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
// Модуль: slickNav - меню для мобильных приложений
// **************************************************************************************************************
JsCssFiles::jsFile($baseUrl.'/js/SlickNav-master/dist/jquery.slicknav.min.js');
JsCssFiles::jsFile($baseUrl.'/js/modernizr.min.js');
JsCssFiles::cssFile($baseUrl.'/js/SlickNav-master/dist/slicknav.css');
?>
<?php foreach($plugins['slickNav'] as $_plugin_key=>$_plugin): ?>
	<?php
	JSPlugins::setIfNotIsset($_plugin, 'selector', '#mobile-menu');
	JSPlugins::setIfNotIsset($_plugin, 'options', array());
	JSPlugins::setIfNotIsset($_plugin, 'onload', true);
	?>

	<?php if ($_plugin['onload']): // Если разрешен запуск во время загрузки страницы ?>
		// Мобильное меню
		window.urv.launch.push(function(){
			var selector = '<?php echo $_plugin['selector']; ?>';
			var options  = <?php echo CJavaScript::encode($_plugin['options']); ?>;
			var $dom = $(selector);
			if ($dom.length != 0) {
				$dom.slicknav(options);
			}
		});
	<?php endIf; ?>
	
	<?php JsCssFiles::css('
		.slicknav_menu {display:none;}
		
		@media screen and (max-width: 40em) {
			/* '.$_plugin['selector'].' is the original menu */
			.js '.$_plugin['selector'].' {
				display:none;
			}
			
			.js .slicknav_menu {
				display:block;
			}
		}
	'); ?>

<?php endForeach; ?>
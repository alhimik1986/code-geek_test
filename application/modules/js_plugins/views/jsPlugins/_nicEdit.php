<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
// Модуль: NicEdit - простой html-редактор
// **************************************************************************************************************
JsCssFiles::jsFile($baseUrl.'/js/nicEdit/nicEdit.js');
JSPlugins::setIfNotIsset($plugin_params, 'id', 'nice-edit');
JSPlugins::setIfNotIsset($plugin_params, 'buttonList', array('bold','italic','underline','left','center','right','justify','ol','ul','subscript','superscript','strikethrough','removeformat','indent','outdent'));
JSPlugins::setIfNotIsset($plugin_params, 'onload', false);
JsCssFiles::css('
	.nicEdit-main {
		background: none repeat scroll 0 0 #FFFFFF;
		overflow-y: auto !important;
		resize: both;
		margin: 0px !important;
		width:100% !important;
	}
');

// Регистрирую глобальные переменные
?>
	window.urv.nicEdit = function(id) {
		var $nicEdit = $('textarea#'+id);
		if ($nicEdit.length != 0) {
			new nicEditor({
				buttonList : <?php echo CJavaScript::encode($plugin_params['buttonList']); ?>,
				iconsPath: '<?php echo Yii::app()->baseUrl.'/js/nicEdit/nicEditorIcons.gif'; ?>'
			}).panelInstance(id);
			$nicEdit.prev().find('.nicEdit-main').attr('tabindex', $nicEdit.attr('tabindex'));
		}
	};
	window.urv.nicEditSaveContent = function(id) {
		if ($('textarea#'+id).length > 0)
			nicEditors.findEditor(id).saveContent();
	};
	window.urv.niceEdit = window.urv.nicEdit;
	window.urv.niceEditSaveContent = window.urv.nicEditSaveContent;
	
	<?php if ($plugin_params['onload']):  // Если разрешен запуск во время загрузки страницы ?>
	window.urv.launch.push(function(){
		window.urv.nicEdit('<?php echo $plugin_params['id']; ?>');
	});
	<?php endIf; ?>
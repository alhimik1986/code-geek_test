<?php if ( ! Yii::app()->user->isGuest AND Yii::app()->user->inGroup): ?>
	<?php // Подключаю файлы стилей и скриптов для меню
		JSPlugins::cssFile(Yii::app()->request->baseUrl.'/css/urv-menu.css');
		JSPlugins::jsFile(Yii::app()->request->baseUrl.'/js/urvMenu/urv-menu.js');
	?>
	
	<?php // Уведомление о том, что сайт заблокирован ?>
	<?php $this->renderPartial('//layouts/menu/_menu_siteBlockedNotation'); ?>
	
	<?php  // Меню пользователя ?>
	<?php echo $this->renderPartialCache('//layouts/menu/_menu_items_user', array(), 'Menu'); ?>
<?php endIf; ?>
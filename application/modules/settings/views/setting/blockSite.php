<?php
/*
	Страница для настройки блокировки сайта.
	
	@var $this   SettingController
	@var $model  Settings (CActiveRecord)  - Модель настроек (для отображения подписей аттрибутов)
	$var $data   array                     - Список записей
*/
?>

<?php
$pageTitle = Yii::t('settings.app', 'Blocking site');
$this->setPageTitle($pageTitle);        // Заголовок страницы
$this->breadcrumbs = array($pageTitle); // Хлебные крошки (навигация сайта)

$this->renderPartial('_js_plugins');    // Подключаю java-скрипты
$this->renderPartial('_blockSite_js');  // Подключаю java-скрипты
?>


<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'block-site-form',
	'enableClientValidation' => true,
)); ?>

	<div>
		<?php echo CHtml::hiddenField('Site[blocked]', 0, array('id'=>'')); ?>
		<?php echo CHtml::checkbox('Site[blocked]', $data['blocked']); ?>
		<label for="Site_blocked"><?php echo Yii::t('settings.app', 'Block the site'); ?></label>
		<div style="display:none" id="Site_blocked_em_"></div>
	</div>

	<br>

	<div>
		<label for="Site_blockedReason" style="font-weight:bold;"><?php echo Yii::t('settings.app', 'Reason (This text will be displayed on a web-page)'); ?></label>:
		<?php echo CHtml::textArea('Site[blockedReason]', $data['blockedReason'], array(
			'style'=>'width:99%;',
			'placeholder'=>Yii::t('settings.app', 'Empty...'),
		)); ?>
		<div style="display:none" id="Site_blockedReason_em_"></div>
	</div>
	
	<br>
	<button id="urv-form-button-submit" class="ajax-form-button-submit" type="button"><?php echo Yii::t('settings.app', 'Save'); ?></button>
	
<?php $this->endWidget(); ?>
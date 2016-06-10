<?php
/*
	Форма создания и редактирования.
	
	@var $this   SettingController
	@var $model  Settings (CActiveRecord) - Модель настроек
	@var $model->dataTypes()              - Доступные типы данных для настроек (для выпадающего списка)
	@var $formTitle                       - Заголовок формы
*/

function textFields($className, $name, $value, $yii_t_label) {
	if (is_array($value)) {
		$result = '';
		foreach($value as $key=>$val) {
			$result .= textFields($className, $name .'['.$key.']', $val, $yii_t_label.'.'.$key);
		}
	} else {
		$id = $className.'_value';
		$result = 
			'<label for="'.$id.'">'.
				Yii::t('settings.settings', $yii_t_label).':<br>'.
				CHtml::textField($name, $value, array('style'=>'width:97%;')).
			'</label><br>';
	}
	return $result;
}

function getDriverFiles() {
	$results = scandir(Yii::getPathOfAlias('application.modules.employees.drivers'));
	$files = array();
	foreach($results as $result) {
		$info = new SplFileInfo($result);
		//if ($info->getExtension() != 'php') continue;
		if (pathinfo($info->getFilename(), PATHINFO_EXTENSION) != 'php') continue;
		$file_name = basename($result, '.php');
		$files[$file_name] = Yii::t('settings.drivers', $file_name);
	}
	return $files;
}
?>

<div class="ajax-form" style="width:600px;" id="settings-ajax-form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'urv-form',
		'enableClientValidation' => true,
	)); ?>

		<?php // Шапка формы ?>
		
		<table class="ajax-form-head"><tr>
			<td style="width:24px;"><div class="ajax-form-close" title="Закыть"></div></td>
			<td class="ajax-form-title"><?php echo $formTitle; ?></td>
			<td style="width:24px;"><div class="ajax-form-close" title="Закыть"></div></td>
		</tr></table>
		
		<?php // Тело формы ?>
		
		<div class="ajax-form-body format">
			<div>
				<?php // Если тип json (массив), то вывожу все элементы массива в виде текстовых полей, иначе вывожу одну текстовую область. ?>
				<?php if ($model->name == 'Driver'): ?>
					<?php echo $form->dropDownList($model, 'value', getDriverFiles()); ?>
				<?php elseIf ($model->name == 'language'): ?>
					<?php echo $form->dropDownList($model, 'value', array(''=>null, 'en'=>'English', 'ru'=>'Русский')); ?>
				<?php else: ?>
					<?php echo textFields($model::className(), $model::className().'[value]', $model->value, $model->name); ?>
				<?php endIf; ?>
				
				<?php echo $form->error($model, 'value'); ?>
			</div>
			
			<br>
			
			<div>
				<?php echo $form->label($model, 'label'); ?>:
				<?php echo $form->textArea($model, 'label', array(
					'style'=>'width:99%;height:40px;',
					'placeholder'=> Yii::t('settings.app', 'Empty...'),
				)); ?>
				<?php echo $form->error($model, 'label'); ?>
			</div>
			
			<br>
			
			<div>
				<?php echo $form->label($model, 'description'); ?>:
				<?php echo $form->textArea($model, 'description', array(
					'style'=>'width:99%;',
					'placeholder'=> Yii::t('settings.app', 'Empty...'),
				)); ?>
				<?php echo $form->error($model, 'description'); ?>
			</div>
		</div>
		
		<?php // Подвал формы ?>
		
		<div class="ajax-form-footer">
			<table style="width:100%;"><tr>
				<td style="text-align:right;">
					<button id="urv-form-button-submit" class="ajax-form-button-submit" type="button"><?php echo Yii::t('settings.app', 'Save'); ?></button>
					<button id="urv-form-button-cancel" class="ajax-form-button-cancel" type="button" style="margin-left:10px;"><?php echo Yii::t('settings.app', 'Cancel'); ?></button>
				</td>
			</tr></table>
		</div>
	<?php $this->endWidget(); ?>
	<div class="resizable"></div>
</div>
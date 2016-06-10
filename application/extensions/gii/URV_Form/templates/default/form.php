<?php
/**
 * This is the template for generating a form script file.
 * The following variables are available in this template:
 * - $this: the FormCode object
 */
?>
<?php echo "<?php\n"; ?>
<?php
	// Получаю список комментарии=>строка_кода, чтобы их выравнить
	$strings = array(
		' - Текущий контроллер' => '@var $this  '.$this->getModelClass().'Controller',
		' - Модель'             => '@var $model '.$this->getModelClass().' ('.get_parent_class($this->getModelClass()).')',
		' - Форма'              => '@var $form CActiveForm',
	);
	$maxLengthName = 0;
	foreach($strings as $name) {
		if ($maxLengthName < strlen($name)) $maxLengthName = strlen($name);
	}
?>
/*
	Форма.
	
<?php foreach($strings as $comment=>$string): ?>
	<?php echo $string . str_repeat(' ', $maxLengthName - strlen($string)) . "$comment\n"; ?>
<?php endforeach; ?>
*/
<?php echo "?>\n"; ?>

<div class="ajax-form" style="width:600px;z-index:105;">
	<?php echo "<?php \$form=\$this->beginWidget('CActiveForm', array(
		'id'=>'urv-".$this->getModelClass()."-form',
		'enableClientValidation' => true,
	)); ?>\n"; ?>
		
		<!-- Шапка формы -->
		<table class="ajax-form-head"><tr>
			<td style="width:24px;"><div class="ajax-form-close" title="Закыть"></div></td>
			<td class="ajax-form-title"><?php echo '<?php echo $formTitle; ?>'; ?></td>
			<td style="width:24px;"><div class="ajax-form-close" title="Закыть"></div></td>
		</tr></table>
		
		<!-- Тело формы -->
		<div class="ajax-form-body">
<?php foreach($this->getModelAttributes() as $attribute): ?>
			<div class="row">
				<?php echo "<?php echo \$form->label(\$model,'$attribute'); ?>".($this->isAttributeRequired($attribute) ? ': <span class="required">*</span>' : '')."\n"; ?>
				<?php echo "<?php echo \$form->textField(\$model,'$attribute'); ?>\n"; ?>
				<?php echo "<?php echo \$form->error(\$model,'$attribute'); ?>\n"; ?>
			</div>
<?php endforeach; ?>
		</div>
		
		<!-- Подвал формы -->
		<div class="ajax-form-footer">
			<table><tr>
				<td style="text-align:left;">
					<?php echo '<?php if ( ! $model->isNewRecord): ?>
						<button id="'.$this->getModelClass().'-button-delete" class="ajax-form-button-delete" type="button">Удалить</button>
					<?php endif; ?>'."\n"; ?>
				</td>
				<td style="text-align:right;">
					<button id="<?php echo $this->getModelClass(); ?>-button-submit" class="ajax-form-button-submit" type="button">Сохранить</button>
					<button id="<?php echo $this->getModelClass(); ?>-button-cancel" class="ajax-form-button-cancel" type="button" style="margin-left:10px;">Отмена</button>
				</td>
			</tr></table>
		</div>
	<?php echo "<?php \$this->endWidget(); ?>\n"; ?>
	<div class="resizable" style=";cursor:se-resize;position:absolute;bottom:0px;right:0px;width:10px;height:10px;background:#aab;"></div>
</div>
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
		' - Текущий контроллер' => '@var $this  '.$this->getControllerClass(),
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

<div class="ajax-form" style="width:600px;z-index:105;" id="<?php echo '<?php echo lcfirst($model::className()); ?>'; ?>-ajax-form">
	<?php echo "<?php \$form=\$this->beginWidget('CActiveForm', array(
		'id'=>\$model::className().'-form',
		'enableClientValidation' => true,
		'htmlOptions'=>array('tabindex'=>2),
	)); ?>\n"; ?>
		
		<!-- Шапка формы -->
		<table class="ajax-form-head"><tr>
			<td style="width:24px;"><div class="ajax-form-close" title="<?php echo "<?php echo Yii::t('app', 'Close'); ?>"; ?>"></div></td>
			<td class="ajax-form-title"><?php echo '<?php echo $formTitle; ?>'; ?></td>
			<td style="width:24px;"><div class="ajax-form-close" title="<?php echo "<?php echo Yii::t('app', 'Close'); ?>"; ?>"></div></td>
		</tr></table>
		
		<!-- Тело формы -->
		<div class="ajax-form-body format">
			<table>
<?php foreach($this->tableSchema->columns as $column): ?>
<?php if ( ! $column->isPrimaryKey): ?>
				<tr>
					<td><?php echo "<?php echo \$form->label(\$model,'$column->name'); ?>:".($column->allowNull ? '' : ' <span class="required">*</span>'); ?></td>
					<td>
						<?php echo "<?php echo \$form->textField(\$model,'$column->name'); ?>\n"; ?>
						<?php echo "<?php echo \$form->error(\$model,'$column->name'); ?>\n"; ?>
					</td>
				</tr>
<?php endif; ?>
<?php endforeach; ?>
			</table>
		</div>
		
		<!-- Подвал формы -->
		<?php echo
<<<EOL
		<div class="ajax-form-footer">
			<table style="width:100%;"><tr>
				<td style="text-align:left;">
					<?php if ( ! \$model->isNewRecord): ?>
						<!--
						<?php //if (\$model->removed): ?>
							<button id="<?php echo lcfirst(\$model::className()); ?>-button-delete" class="ajax-form-button-delete" type="button" style="margin-right:20px;"><?php echo Yii::t('app', 'Delete'); ?></button>
						<?php //endIf; ?>
						<?php //echo CHtml::hiddenField(\$model::className().'[removed]', '0', array('id'=>\$model::className().'_removed4')); ?>
						<?php //echo CHtml::checkbox(\$model::className().'[removed]', \$model->removed, array('id'=>\$model::className().'_removed3', 'class'=>'delete')); ?>
						<?php //echo CHtml::label(Yii::t('app', 'Remove'), \$model::className().'_removed3', array('style'=>'color:#d00;font-weight:normal;font-style:normal;', 'class'=>'delete')); ?>
						-->
						<button id="<?php echo lcfirst(\$model::className()); ?>-button-delete" class="ajax-form-button-delete" type="button" style="margin-right:20px;"><?php echo Yii::t('app', 'Delete'); ?></button>
					<?php endIf; ?>
				</td>
				<td style="text-align:right;">
					<button id="<?php echo lcfirst(\$model::className()); ?>-button-submit" class="ajax-form-button-submit" type="button"><?php echo Yii::t('app', 'Save'); ?></button>
					<button id="<?php echo lcfirst(\$model::className()); ?>-button-cancel" class="ajax-form-button-cancel" type="button" style="margin-left:10px;"><?php echo Yii::t('app', 'Cancel'); ?></button>
				</td>
			</tr></table>
		</div>
EOL;
?>
	<?php echo "<?php \$this->endWidget(); ?>\n"; ?>
	<div class="resizable"></div>
</div>
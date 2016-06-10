<?php
/*
	Форма.
	
	@var $this  CategoriesController - Текущий контроллер
	@var $model Categories (Model)   - Модель
	@var $form CActiveForm           - Форма
*/
?>

<div class="ajax-form" style="width:600px;z-index:105;" id="<?php echo lcfirst($model::className()); ?>-ajax-form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>$model::className().'-form',
		'enableClientValidation' => true,
		'htmlOptions'=>array('tabindex'=>2),
	)); ?>
		
		<!-- Шапка формы -->
		<table class="ajax-form-head"><tr>
			<td style="width:24px;"><div class="ajax-form-close" title="<?php echo Yii::t('app', 'Close'); ?>"></div></td>
			<td class="ajax-form-title"><?php echo $formTitle; ?></td>
			<td style="width:24px;"><div class="ajax-form-close" title="<?php echo Yii::t('app', 'Close'); ?>"></div></td>
		</tr></table>
		
		<!-- Тело формы -->
		<div class="ajax-form-body format">
			<div><?php echo $form->label($model,'name'); ?>:</div>
			<div>
				<?php echo $form->textField($model,'name', array('style'=>'width:95%;')); ?>
				<?php echo $form->error($model,'name'); ?>
			</div>
		</div>
		
		<!-- Подвал формы -->
		<div class="ajax-form-footer">
			<table style="width:100%;"><tr>
				<td style="text-align:left;">
					<?php if ( ! $model->isNewRecord): ?>
						<?php if ($model->removed): ?>
							<button id="<?php echo lcfirst($model::className()); ?>-button-delete" class="ajax-form-button-delete" type="button" style="margin-right:20px;"><?php echo Yii::t('app', 'Delete'); ?></button>
						<?php endIf; ?>
						<?php echo CHtml::hiddenField($model::className().'[removed]', '0', array('id'=>$model::className().'_removed4')); ?>
						<?php echo CHtml::checkbox($model::className().'[removed]', $model->removed, array('id'=>$model::className().'_removed3', 'class'=>'delete')); ?>
						<?php echo CHtml::label(Yii::t('app', 'Remove'), $model::className().'_removed3', array('style'=>'color:#d00;font-weight:normal;font-style:normal;', 'class'=>'delete')); ?>
					<?php endIf; ?>
				</td>
				<td style="text-align:right;">
					<button id="<?php echo lcfirst($model::className()); ?>-button-submit" class="ajax-form-button-submit" type="button"><?php echo Yii::t('app', 'Save'); ?></button>
					<button id="<?php echo lcfirst($model::className()); ?>-button-cancel" class="ajax-form-button-cancel" type="button" style="margin-left:10px;"><?php echo Yii::t('app', 'Cancel'); ?></button>
				</td>
			</tr></table>
		</div>
	<?php $this->endWidget(); ?>
	<div class="resizable"></div>
</div>
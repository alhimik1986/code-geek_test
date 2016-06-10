<?php
/*
	Форма создания, редактирования и удаления пользователей.
	
	@var $this   UsersController
	@var $model  Users (CActiveRecord) - Модель пользователя
	@var $formTitle                    - Заголовок формы
*/
?>
<div class="ajax-form" style="width:750px;" id="<?php echo lcfirst($model::className()); ?>-ajax-form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>$model::className().'-form',
		'enableClientValidation' => true,
		'htmlOptions' => array(
			'tabindex' => 2,
		),
	)); ?>

		<!-- Шапка формы -->
		<table class="ajax-form-head"><tr>
			<td style="width:24px;"><div class="ajax-form-close" title="<?php echo Yii::t('app', 'Close'); ?>"></div></td>
			<td class="ajax-form-title"><?php echo $formTitle; ?></td>
			<td style="width:24px;"><div class="ajax-form-close" title="<?php echo Yii::t('app', 'Close'); ?>"></div></td>
		</tr></table>
		
		<!-- Тело формы -->
		<div class="ajax-form-body format">
			<table class="width-100">
				<tr>
					<td>
						<?php echo $form->label($model, 'email_class'); ?>:
						<?php echo $form->dropDownList($model, 'email_class', UsersMailHelper::email_class_label()); ?>
						<?php echo $form->error($model, 'email_class'); ?>
					</td>
					<td style="width: 75px;">
						<?php echo $form->label($model, 'email_smtp_secure'); ?>:
						<?php echo $form->textField($model, 'email_smtp_secure', array('placeholder'=>'...', 'style'=>'width:65px;')); ?>
						<?php echo $form->error($model, 'email_smtp_secure'); ?>
					</td>
					<td>
						<?php echo $form->label($model, 'email_host'); ?>:
						<?php echo $form->textField($model, 'email_host', array('placeholder'=>'...', 'style'=>'width:95%;')); ?>
						<?php echo $form->error($model, 'email_host'); ?>
					</td>
					<td style="width:120px;">
						<?php echo $form->label($model, 'email_port'); ?>:
						<?php echo $form->textField($model, 'email_port', array('placeholder'=>'...', 'style'=>'width:100px;')); ?>
						<?php echo $form->error($model, 'email_port'); ?>
					</td>
					<td class="last">
						<?php echo $form->label($model, 'email_address'); ?>:
						<?php echo $form->textField($model, 'email_address', array('placeholder'=>'...', 'style'=>'width:200px;')); ?>
						<?php echo $form->error($model, 'email_address'); ?>
					</td>
				</tr>
					<td colspan="2">
						<?php echo $form->label($model, 'email_username'); ?>:
						<?php echo $form->textField($model, 'email_username', array('placeholder'=>'...')); ?>
						<?php echo $form->error($model, 'email_username'); ?>
					</td>
					<td>
						<?php $model->email_password = ''; ?>
						<?php echo $form->label($model, 'email_password'); ?>:
						<?php echo $form->passwordField($model, 'email_password', array('placeholder'=>'...')); ?>
						<?php echo $form->error($model, 'email_password'); ?>
					</td>
					<td colspan="2" class="last">
						<?php echo $form->label($model, 'email_from_name'); ?>:
						<?php echo $form->textField($model, 'email_from_name', array('placeholder'=>'...', 'style'=>'width:332px;')); ?>
						<?php echo $form->error($model, 'email_from_name'); ?>
					</td>
				</tr>
				
				<?php if ($model->comment): ?>
				<tr>
					<td colspan="3" class="last">
						<div><b><?php echo Yii::t('app', 'Comment'); ?>:</b></div>
						<div style="border:1px solid #6996CA; padding:5px; background:#eef;"><?php echo $model->comment; ?></div>
					</td>
				</tr>
				<?php endIf; ?>
			</table>
		</div>
		
		<!-- Подвал формы -->
		<div class="ajax-form-footer">
			<table style="width:100%;"><tr>
                <td style="text-align:left;"></td>
				<td style="text-align:right;">
					<button id="<?php echo lcfirst($model::className()); ?>-button-submit" class="ajax-form-button-submit" type="button"><?php echo Yii::t('app', 'Save'); ?></button>
					<button id="<?php echo lcfirst($model::className()); ?>-button-cancel" class="ajax-form-button-cancel" type="button" style="margin-left:10px;"><?php echo Yii::t('app', 'Cancel'); ?></button>
				</td>
			</tr></table>
		</div>
	<?php $this->endWidget(); ?>
	<div class="resizable"></div>
</div>
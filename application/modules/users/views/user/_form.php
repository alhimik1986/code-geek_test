<?php
/*
	Форма создания, редактирования и удаления пользователей.
	
	@var $this   UsersController
	@var $model  Users (CActiveRecord) - Модель пользователя
	@var $model->roleList()            - Список доступных ролей
	@var $formTitle                    - Заголовок формы
*/
?>
<div class="ajax-form" style="width:600px;" id="<?php echo lcfirst($model::className()); ?>-ajax-form">
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
				
				<?php if ($model->isNewRecord OR Yii::app()->user->role == Users::SUPER_ADMIN): ?>
				<tr title="<?php echo Yii::t('users.app', 'Please check correctness of the surname, name and patronymic, as it can not be changed.'); ?>">
					<td>
						<?php echo $form->label($model, 'last_name'); ?>: <span class="required">*</span>
						<?php echo $form->textField($model, 'last_name'); ?>
						<?php echo $form->error($model, 'last_name'); ?>
					</td>
					<td>
						<?php echo $form->label($model, 'first_name'); ?>: <span class="required">*</span>
						<?php echo $form->textField($model, 'first_name'); ?>
						<?php echo $form->error($model, 'first_name'); ?>
					</td>
					<td class="last">
						<?php echo $form->label($model, 'middle_name'); ?>:
						<?php echo $form->textField($model, 'middle_name'); ?>
						<?php echo $form->error($model, 'middle_name'); ?>
					</td>
				</tr>
				<?php endif; ?>
				
				<tr>
					<td>
						<?php echo $form->label($model, 'username'); ?>: <span class="required">*</span>
						<?php echo $form->textField($model, 'username', array('autocomplete'=>'off', 'style'=>'width:175px;')); ?>
						<?php echo $form->error($model, 'username'); ?>
					</td>
					<td>
						<?php echo $form->label($model, 'password'); ?>: <span class="required">*</span>
						<?php echo $form->passwordField($model, 'password', array('value'=>'', 'autocomplete'=>'off')); ?>
						<?php echo $form->error($model, 'password'); ?>
					</td>
					<td class="last">
						<?php echo $form->label($model, 'role'); ?>:
						<?php echo $form->dropDownList($model, 'role', $model::roleListForForm(), array(
							'class'=>'width-100',
						)); ?>
						<?php echo $form->error($model, 'role'); ?>
						<br>
						<?php if ($model->isNewRecord) $model->blocked=0; ?>
						<?php echo $form->checkbox($model, 'blocked', array('style'=>'margin:5px 3px 3px 0px;')); ?>
						<?php echo $form->label($model, 'blocked', $model->blocked ? array('style'=>'background:red;', ) : array()); ?>
						<?php echo $form->error($model, 'blocked'); ?>
					</td>
				</tr>
				
				<tr>
					<td style="width:200px;">
						<?php echo $form->label($model, 'email'); ?>:
						<?php echo $form->textField($model, 'email', array('placeholder'=>'...', 'style'=>'width:175px;')); ?>
						<?php echo $form->error($model, 'email'); ?>
						<div>
							<?php echo $form->checkbox($model, 'subscribed', array('style'=>'margin:5px 3px 3px 0px')); ?>
							<?php echo $form->label($model, 'subscribed'); ?>
							<?php echo $form->error($model, 'subscribed'); ?>
						</div>
					</td>
					<td>
						<?php echo $form->label($model, 'phone'); ?>:
						<?php echo $form->textField($model, 'phone', array('placeholder'=>'...')); ?>
						<?php echo $form->error($model, 'phone'); ?>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="last">
						<?php $model->comment = htmlspecialchars_decode($model->comment); // Декодирую заэкранированные символы для отображения в текстовой области ?>
						<?php echo $form->label($model, 'comment'); ?>:
						<?php echo $form->textArea($model, 'comment', array(
							'style'=>'width:560px;height:40px;',
							'placeholder'=>Yii::t('app', 'Empty...'),
						)); ?>
						<?php echo $form->error($model, 'comment'); ?>
					</td>
				</tr>
			</table>
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
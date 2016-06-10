<?php
/*
	Форма регистрации пользователей.
	
	@var $this   UsersController
	@var $model  Users (CActiveRecord) - Модель пользователя
	@var $formTitle                    - Заголовок формы
*/
?>
<div class="ajax-form" id="registration" style="width:600px;">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'registration-form',
		'enableClientValidation' => true,
	)); ?>

		<!-- Шапка формы -->
		<table class="ajax-form-head"><tr>
			<td style="width:24px;"><div class="ajax-form-close" title="<?php echo Yii::t('app', 'Close'); ?>"></div></td>
			<td class="ajax-form-title"><?php echo $formTitle; ?></td>
			<td style="width:24px;"><div class="ajax-form-close" title="<?php echo Yii::t('app', 'Close'); ?>"></div></td>
		</tr></table>
		
		<?php // Тело формы ?>
		
		<div class="ajax-form-body format" style="padding-bottom:0px;">
			<div><?php echo Yii::t('users.app', 'Please check correctness of the surname, name and patronymic, as it can not be changed.'); ?></div>
			<table class="width-100">
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
				
				<tr>
					<td>
						<?php echo $form->label($model, 'username'); ?>: <span class="required">*</span><br>
						<?php echo $form->textField($model, 'username', array('autocomplete'=>'off')); ?>
						<?php echo $form->error($model, 'username'); ?>
					</td>
					<td>
						<?php echo $form->label($model, 'password'); ?>: <span class="required">*</span><br>
						<?php echo $form->passwordField($model, 'password', array('value'=>'', 'autocomplete'=>'off')); ?>
						<?php echo $form->error($model, 'password'); ?>
					</td>
				</tr>
				
				<tr>
					<td>
						<?php echo $form->label($model, 'email'); ?>:
						<?php echo $form->textField($model, 'email', array('placeholder'=>'...')); ?>
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
					<td class="last">
						<?php echo $form->label($model, 'ip'); ?>:
						<?php echo $form->textField($model, 'ip', array('placeholder'=>'...', 'value'=>Controller::getIp())); ?>
						<?php echo $form->error($model, 'ip'); ?>
					</td>
				</tr>
				<?php $model->comment = htmlspecialchars_decode($model->comment); // Декодирую заэкранированные символы для отображения в текстовой области ?>
				<tr>
					<td colspan="3" class="last">
						<label for="<?php echo $model::className().'_comment'; ?>"><?php echo Yii::t('users.app', 'Application for registration'); ?></label>:
						<?php echo $form->textArea($model, 'comment', array(
							'style'=>'width:99%;height:40px;',
							'placeholder'=>Yii::t('app', 'Empty...'),
						)); ?>
						<?php echo $form->error($model, 'comment'); ?>
					</td>
				</tr>
			</table>
			
			<?php if(CCaptcha::checkRequirements() AND Yii::app()->settings->param['captcha']['enabled'] AND Yii::app()->settings->param['captcha']['registration']): ?>
			<table>
				<tr>
					<td style="width:122px;">
						<?php $this->widget('CCaptcha', array(
							'captchaAction' => '//'.$this->module->id.'/user/captcha1',
							'id'=>'registration-captcha',
							'showRefreshButton' => true,
						)); ?>
					</td>
					<td>
						<?php echo $form->labelEx($model,'verifyCode'); ?>:
						<br>
						<div>
							<?php echo $form->textField($model,'verifyCode', array('style'=>'width:100px;float:left;')); ?>
							<?php echo $form->error($model,'verifyCode', array('style'=>'float:left;margin-left:10px;')); ?>
						</div>
						<div style="clear:both;">
							<a href="#" id="registration-captcha_button"><?php echo Yii::t('users.app', 'Another code'); ?></a>
						</div>
					</td>
				</tr>
			</table>
			<?php endif; ?>
		</div>
		
		<?php // Подвал формы ?>
		
		<div class="ajax-form-footer" style="padding-top:0px;">
			<table style="width:100%;"><tr>
				<td style="text-align:right;">
					<button id="registration-form-button-registration" class="ajax-form-button-registration ajax-form-button-submit" type="button"><?php echo Yii::t('users.app', 'Sign up'); ?></button>
					<button id="registration-form-button-cancel" class="ajax-form-button-cancel" type="button" style="margin-left:10px;"><?php echo Yii::t('app', 'Cancel'); ?></button>
				</td>
			</tr></table>
		</div>
	<?php $this->endWidget(); ?>
	<div class="resizable"></div>
</div>
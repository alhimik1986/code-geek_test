<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'recovery-form',
	//'enableClientValidation'=>true,
	//'enableAjaxValidation'=>true,
	'action'=>array('recovery/request'),
)); ?>

	<!-- Поле "E-mail или Имя пользователя" -->
	<div class="email-or-username">
		<?php echo $form->textField($recovery,'username', array('placeholder'=>Yii::t('users.app', 'E-mail or username...'), 'title'=>Yii::t('users.app', 'E-mail or username...'), 'autocomplete'=>'off', 'class'=>'login-text-field', 'tabindex'=>'1')); ?><br>
		<?php echo $form->error($recovery,'username'); ?>
		<div style="display:none" id="<?php echo get_class($recovery).'_'.'username'; ?>_em_"></div>
	</div>
	
	<!-- Кнопка "Восстановить" -->
	<?php echo CHtml::submitButton(Yii::t('users.app', 'Recover'), array('id'=>'recovery-form-button', 'tabindex'=>'3')); ?>
	<div class="clear"></div>
	
	<!-- Блок для каптчи -->
	<?php if(CCaptcha::checkRequirements() AND Yii::app()->settings->param['captcha']['enabled'] AND Yii::app()->settings->param['captcha']['recovery']): ?>
	<div class="captcha-wrapper">
		<?php //echo $form->labelEx($recovery,'verifyCode'); ?>
		<div title="<?php echo Yii::t('users.app', 'Type the characters from the image. Characters are not case sensitive.'); ?>">
		<?php $this->widget('CCaptcha', array(
			'captchaAction' => '//'.$this->module->id.'/user/captcha',
			'id'=>'recovery-captcha',
			'buttonLabel'=>Yii::t('users.app', 'Another code'),
			'buttonOptions'=>array(
				'tabindex'=>'4',
			),
		)); ?>
		<?php echo $form->textField($recovery,'verifyCode', array('autocomplete'=>'off', 'placeholder'=>'Символы с картинки', 'tabindex'=>'2')); ?>
		</div>
		<?php //echo $form->error($recovery,'verifyCode'); ?>
		<div style="display:none" id="<?php echo get_class($recovery).'_'.'verifyCode'; ?>_em_"></div>
	</div>
	<?php endif; ?>
	
<?php $this->endWidget(); ?>
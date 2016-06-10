<div class="login-form-wrapper">

<!--
<div>
	<div style="float:left; margin-left:40px;">
		<a href="#" id="registration-form-link"><?php echo Yii::t('users.app', 'Registration'); ?></a>
	</div>
</div>
-->
			
<!-- Форма входа -->
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>false,
)); ?>
	<div>
		<?php echo $form->textField($model,'username', array('autocomplete'=>'off', 'placeholder'=>Yii::t('app', 'Username').'...', 'class'=>'login-text-field')); ?><br>
		<?php //echo $form->error($model,'username'); ?>
		<div style="display:none" id="<?php echo $model::className().'_'.'username'; ?>_em_"></div>
	</div>

	<div>
		<?php echo $form->passwordField($model,'password', array('autocomplete'=>'off', 'placeholder'=>Yii::t('app', 'Password').'...', 'class'=>'login-text-field')); ?><br>
		<?php //echo $form->error($model,'password'); ?>
		<div style="display:none" id="<?php echo $model::className().'_'.'password'; ?>_em_"></div>
	</div>

	<div class="rememberMe">
		<?php $model->rememberMe = $this->isLocalClient(); ?>
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton(Yii::t('users.app', 'Sign in')); ?>
	</div>

<?php $this->endWidget(); ?>



<!-- Кнопка "Забыли пароль?" -->
<div class="forgot-password-link">
	<a href="#" id="forgot-password-link" onclick="jQuery('.forgot-password-form').children().slideToggle(); jQuery('#RecoveryForm_username').focus();">
		<?php echo Yii::t('users.app', 'Forgot password?'); ?>
	</a>
</div>

</div>


<?php
/*
	Страница профиля пользователей.
	
	@var $this   ProfileController
	@var $model  Users (CActiveRecord)  - Модель пользователя (для отображения подписей аттрибутов)
	$var $user   array                  - Текущий пользователь
*/
?>

<div class="format" style="width:400px; border:1px solid #ccc; padding:20px; margin:0 auto;">
<?php
$pageTitle = Yii::t('users.app', 'User profile');
$this->setPageTitle($pageTitle);                    // Заголовок страницы
$this->breadcrumbs = array($pageTitle);             // Хлебные крошки (навигация сайта)

$this->renderPartial('_js_plugins');                // Загружаю стандарные jquery-плагины
$this->renderPartial('_index_js');                  // Подключаю java-скрипты
?>

	<h2><?php echo $user['last_name'].' '.$user['first_name'].' '.$user['middle_name']; ?></h2>


	<div>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'profile-form',
		'enableClientValidation'=>false,
	)); ?>

		<table style="width:350px;" class="padding-5px-10px-5px-0px">
			<tr>
				<td>
					<div class="row">
						<?php echo $form->label($model,'username'); ?>: <span class="required" style="color:red;">*</span><br>
						<?php echo $form->textField($model,'username', array('autocomplete'=>'off')); ?><br>
						<?php //echo $form->error($model,'username'); ?>
						<div style="display:none" id="<?php echo $model::className().'_'.'username'; ?>_em_"></div>
					</div>
				</td>
				<td>
					<div class="row">
						<?php echo $form->label($model,'password'); ?>:<br>
						<?php echo $form->passwordField($model,'password', array('autocomplete'=>'off', 'value'=>'')); ?><br>
						<?php //echo $form->error($model,'password'); ?>
						<div style="display:none" id="<?php echo $model::className().'_'.'password'; ?>_em_"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="row">
						<?php echo $form->label($model,'email'); ?>: <br>
						<?php echo $form->textField($model,'email', array('autocomplete'=>'off')); ?><br>
						<?php //echo $form->error($model,'email'); ?>
						<div style="display:none" id="<?php echo $model::className().'_'.'email'; ?>_em_"></div>
					</div>
				</td>
				<td>
					<div class="row">
						<?php echo $form->label($model,'phone'); ?>: <br>
						<?php echo $form->textField($model,'phone', array('autocomplete'=>'off')); ?><br>
						<?php //echo $form->error($model,'phone'); ?>
						<div style="display:none" id="<?php echo $model::className().'_'.'phone'; ?>_em_"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="row">
						<?php echo $form->label($model,'ip'); ?>: <br>
						<?php echo $form->textField($model,'ip', array('autocomplete'=>'off')); ?><br>
						<?php //echo $form->error($model,'ip'); ?>
						<div style="display:none" id="<?php echo $model::className().'_'.'ip'; ?>_em_"></div>
					</div>
				</td>
			</tr>
		</table>



		<div class="row buttons">
			<?php echo CHtml::submitButton(Yii::t('app', 'Save'), array('class'=>'btn-primary', 'style'=>'margin-top:20px;')); ?>
		</div>

	<?php $this->endWidget(); ?>
	</div><!-- form -->
</div>
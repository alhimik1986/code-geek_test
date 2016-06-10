<?php
/*
	Форма отправки сообщения.
	
	@var $this      DefaultController
	@var $model     MailForm (CFormModel)    - Модель для отправки сообщения
	@var $formTitle                          - Заголовок формы
*/
?>
<div class="ajax-form" style="width:610px;" id="<?php echo lcfirst($model::className()); ?>-ajax-form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>$model::className().'-form',
		'enableClientValidation' => true,
		'htmlOptions' => array(
			'tabindex' => 2,
			'enctype'=>'multipart/form-data',
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
			<?php echo CHtml::hiddenField($model::className().'[validate]', '0'); ?>
			<table class="width-100">
				<tr>
					<td>
						<div>
							<?php echo $form->label($model, 'subject'); ?>: <span class="required">*</span>
							<?php echo $form->textField($model, 'subject', array('style'=>'width:97%;')); ?>
							<?php echo $form->error($model, 'subject'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div>
							<?php echo $form->label($model, 'emails'); ?>: <span class="required">*</span>
							<?php echo $form->textField($model, 'emails', array('style'=>'width:97%;display:none;')); ?>
							<div class="input">
								<ul id="tagit" class="fake-input" tabindex="1" style="width:96%;"></ul>
							</div>
							<?php echo $form->error($model, 'emails'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div>
							<?php echo $form->label($model, 'text'); ?>: <span class="required">*</span><br>
							<?php echo $form->textArea($model, 'text', array('style'=>'width:580px; height:100px;')); // ширина указан в px для совестимости с ie7 ?>
							<?php echo $form->error($model, 'text'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div>
							<?php echo $form->label($model, 'files'); ?><br>
							<?php echo $form->fileField($model, 'files[]', array('onchange'=>"
								var $=jQuery, emptyFileField=false;
								$(this).parents('td').find('input[type=\"file\"]').each(function(){
									if ($(this).val().length == 0) {
										emptyFileField = true;
										return false;
									} else {
										if ($(this).parent().find('.mail-form-file-close').length == 0) {
											$(this).parent().append('<a href=\"#\" onclick=\"jQuery(this).parent().remove(); return false;\" class=\"mail-form-file-close\">x</a>');
										}
									}
								});
								if ( ! emptyFileField) {
									$(this).parents('td').append('<div>'+$(this).removeAttr('id')[0].outerHTML+'</div>');
								}
								")); ?>
							<?php echo $form->error($model, 'files'); ?>
						</div>
					</td>
				</tr>
			</table>
		</div>
		
		<!-- Подвал формы -->
		<div class="ajax-form-footer">
			<table style="width:100%;"><tr>
                <td style="text-align:left;"></td>
				<td style="text-align:right;">
					<button id="<?php echo lcfirst($model::className()); ?>-button-submit" class="ajax-form-button-submit" type="button"><?php echo Yii::t('app', 'Send'); ?></button>
					<button id="<?php echo lcfirst($model::className()); ?>-button-cancel" class="ajax-form-button-cancel" type="button" style="margin-left:10px;"><?php echo Yii::t('app', 'Cancel'); ?></button>
				</td>
			</tr></table>
		</div>
	<?php $this->endWidget(); ?>
	<div class="resizable"></div>
</div>
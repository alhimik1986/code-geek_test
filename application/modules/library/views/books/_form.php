<?php
/*
	Форма.
	
	@var $this  BooksController - Текущий контроллер
	@var $model Books (Model)   - Модель
	@var $form CActiveForm      - Форма
*/
?>

<div class="ajax-form" style="width:600px;z-index:105;" id="<?php echo lcfirst($model::className()); ?>-ajax-form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>$model::className().'-form',
		'enableClientValidation' => true,
		'htmlOptions'=>array(
			'tabindex'=>2,
			'enctype'=>'multipart/form-data',
			'target' => 'iframe_for_ajax_file_upload',
		),
	)); ?>
		<iframe id="iframe_for_ajax_file_upload" name="iframe_for_ajax_file_upload" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
		
		<!-- Шапка формы -->
		<table class="ajax-form-head"><tr>
			<td style="width:24px;"><div class="ajax-form-close" title="<?php echo Yii::t('app', 'Close'); ?>"></div></td>
			<td class="ajax-form-title"><?php echo $formTitle; ?></td>
			<td style="width:24px;"><div class="ajax-form-close" title="<?php echo Yii::t('app', 'Close'); ?>"></div></td>
		</tr></table>
		
		<!-- Тело формы -->
		<div class="ajax-form-body format">
			<?php echo $form->hiddenField($model,'validate'); ?>
			
			<div><?php echo $form->label($model,'title'); ?>: <span class="required">*</span></div>
			<div>
				<?php echo $form->textField($model,'title', array('style'=>'width:95%')); ?>
				<?php echo $form->error($model,'title'); ?>
			</div>
			<div style="height:10px;"></div>

			<div><?php echo CHtml::link($model->getAttributeLabel('sub_category_id'), $this->createUrl('subCategories/index'), array('target'=>'_blank')); ?>: <span class="required">*</span></div>
			<div>
				<?php echo $form->dropDownList($model,'sub_category_id', SubCategories::dropDownList(array(''=>'')), array(
					'class'=>'chosen',
					'style'=>'width:200px;',
					'data-placeholder'=>'Выберите подкатегорию',
					'data-no_results_text' => 'Ничего не найдено по',
				)); ?>
				<?php echo $form->error($model,'sub_category_id'); ?>
			</div>
			<div style="height:10px;"></div>
			
			<div><?php echo $form->label($model,'description'); ?>: <span class="required">*</span></div>
			<div>
				<div><?php echo $form->textarea($model,'description', array('style'=>'width:560px;')); ?></div>
				<?php echo $form->error($model,'description'); ?>
			</div>
			
			<!-- Файл обложки книги -->
			<div><?php echo $form->label($model, 'photo'); ?>:</div>
			<div>
				<img src="<?php echo $this->createUrl('viewPhoto', array('id'=>$model->id, '_'=>DateHelper::date('YmdHis'))); ?>" style="width:40px;" alt="Фото" title="" />
				<?php echo $form->fileField($model, 'photo'); ?>
				<?php echo $form->error($model,'photo'); ?>
			</div><br>
			
			<!-- Список прикрепленных файлов -->
			<?php if ($files): ?>
				<div><label for="">Прикрепленные файлы:</label></div>
				<table id="<?php echo BooksFiles::className(); ?>-files" style="width:auto;">
				<?php $this->renderPartial('_index_files_rows', array('files'=>$files)); ?>
				</table>
			<?php endIf; ?>
			
			<!-- Прикрепление файлов -->
			<table>
				<tr>
					<td>
						<div><?php echo $form->label($model, 'files'); ?>:</div>
						<div>
							<?php echo $form->fileField($model, 'files[]', array(
								'class' => 'add-file-field-on-change',
							)); ?>
							<?php echo $form->error($model,'files'); ?>
						</div>
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
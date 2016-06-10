<?php
$class=get_class($model);
Yii::app()->clientScript->registerScript('gii.model',"
$('#{$class}_connectionId').change(function(){
	var tableName=$('#{$class}_tableName');
	tableName.autocomplete('option', 'source', []);
	$.ajax({
		url: '".Yii::app()->getUrlManager()->createUrl('gii/model/getTableNames')."',
		data: {db: this.value},
		dataType: 'json'
	}).done(function(data){
		tableName.autocomplete('option', 'source', data);
	});
});
$('#{$class}_modelClass').change(function(){
	$(this).data('changed',$(this).val()!='');
});
$('#{$class}_tableName').bind('keyup change', function(){
	var model=$('#{$class}_modelClass');
	var tableName=$(this).val();
	if(tableName.substring(tableName.length-1)!='*') {
		$('.form .row.model-class').show();
	}
	else {
		$('#{$class}_modelClass').val('');
		$('.form .row.model-class').hide();
	}
	if(!model.data('changed')) {
		var i=tableName.lastIndexOf('.');
		if(i>=0)
			tableName=tableName.substring(i+1);
		var tablePrefix=$('#{$class}_tablePrefix').val();
		if(tablePrefix!='' && tableName.indexOf(tablePrefix)==0)
			tableName=tableName.substring(tablePrefix.length);
		var modelClass='';
		$.each(tableName.split('_'), function() {
			if(this.length>0)
				modelClass+=this.substring(0,1).toUpperCase()+this.substring(1);
		});
		model.val(modelClass);
	}
});
$('.form .row.model-class').toggle($('#{$class}_tableName').val().substring($('#{$class}_tableName').val().length-1)!='*');

// Перевод страницы
$('.form.gii .note').html('Поля со <span class=\"required\">*</span> обязательны. Кликните по <span class=\"sticky\">подсвеченным полям</span> для их редактирования.');
$('label[for=\"".$this->getId()."Code_template\"]').html('Шаблон генератора кода <span class=\"required\">*</span>')
	.next().next().next('.tooltip').html('Выберите папку с шаблонами для генерации кода.');
$('[name=\"preview\"]').val('Предварительный просмотр');
$('[name=\"generate\"]').val('Сгенерировать');
");
?>
<h1>Генератор Модели</h1>

<p>Здесь генерируется модель для таблицы в базе данных.</p>

<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model)); ?>

	<div class="row sticky">
		<?php echo $form->labelEx($model, 'connectionId')?>
		<?php echo $form->textField($model, 'connectionId', array('size'=>65))?>
		<div class="tooltip">
		Укажите компонент базы данных (имеющий класс "system.db.CDbConnection").
		</div>
		<?php echo $form->error($model,'connectionId'); ?>
	</div>
	<div class="row sticky">
		<?php echo $form->labelEx($model,'tablePrefix'); ?>
		<?php echo $form->textField($model,'tablePrefix', array('size'=>65)); ?>
		<div class="tooltip">
		Это префикс в имени таблицы в базе данных.
		Префикс таблицы влияет на то, как модель будет вызывать имя таблицы, но не влияет на имя самой модели.
		Например, таблица с префиксом <code>tbl_</code> 
		под именем <code>tbl_post</code> сгенерирует класс-модель с именем <code>Post</code>.
		<br/>
		Оставьте поле пустым, если таблицы не используют общий префикс.
		</div>
		<?php echo $form->error($model,'tablePrefix'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'tableName'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
			'model'=>$model,
			'attribute'=>'tableName',
			'name'=>'tableName',
			'source'=>Yii::app()->hasComponent($model->connectionId) ? array_keys(Yii::app()->{$model->connectionId}->schema->getTables()) : array(),
			'options'=>array(
				'minLength'=>'0',
				'focus'=>new CJavaScriptExpression('function(event,ui) {
					$("#'.CHtml::activeId($model,'tableName').'").val(ui.item.label).change();
					return false;
				}')
			),
			'htmlOptions'=>array(
				'id'=>CHtml::activeId($model,'tableName'),
				'size'=>'65',
				'data-tooltip'=>'#tableName-tooltip'
			),
		)); ?>
		<div class="tooltip" id="tableName-tooltip">
		Имя таблицы, для которой сгенерировать класс-модель (напр. <code>tbl_user</code>). <br />
		Можно указать имя схемы (schema name), например, <code>public.tbl_post</code>.<br />
		Можно ввести <code>*</code> или указать <code>schemaName.*</code>, чтобы сгенерировать класс-модели для ВСЕХ таблиц.
		</div>
		<?php echo $form->error($model,'tableName'); ?>
	</div>
	<div class="row model-class">
		<?php echo $form->label($model,'modelClass',array('required'=>true)); ?>
		<?php echo $form->textField($model,'modelClass', array('size'=>65)); ?>
		<div class="tooltip">
		Это имя генерируемого класса-модели, например, <code>Post</code>, <code>Comment</code> и т.д.
		Имя класса-модели не зависит от регистра (<code>new Post</code> - одинаково <code>new post</code>).
		</div>
		<?php echo $form->error($model,'modelClass'); ?>
	</div>
	<div class="row sticky">
		<?php echo $form->labelEx($model,'baseClass'); ?>
		<?php echo $form->textField($model,'baseClass',array('size'=>65)); ?>
		<div class="tooltip">
			Это класс, из которого наследуется модель.
			Убедитесь, что указанный класс существует и загружен (autoloaded).
		</div>
		<?php echo $form->error($model,'baseClass'); ?>
	</div>
	<div class="row sticky">
		<?php echo $form->labelEx($model,'modelPath'); ?>
		<?php echo $form->textField($model,'modelPath', array('size'=>65)); ?>
		<div class="tooltip">
			Это путь, куда сгенерируется модель.
			Его желательно указать в виде псевдонима, например, <code>application.models</code>.
		</div>
		<?php echo $form->error($model,'modelPath'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'buildRelations'); ?>
		<?php echo $form->checkBox($model,'buildRelations'); ?>
		<div class="tooltip">
			Вместе с классом-моделью генерируются зависимости таблиц.
			Во время генерирования зависимостей необходимо полное сканирование всех таблиц базы данных.
			Необходимо отключить эту опцию, если база данных содержит слишком много таблиц.
		</div>
		<?php echo $form->error($model,'buildRelations'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'commentsAsLabels'); ?>
		<?php echo $form->checkBox($model,'commentsAsLabels'); ?>
		<div class="tooltip">
			Комментарии таблицы могут быть использованы в качестве подписей аттрибутов.
			Если ваш RDBMS не поддерживает свойство комментирования колонок таблиц 
			или комментарий колонок отсутствует вообще, то в качестве подписи аттрибута будет использовано имя таблицы.
		</div>
		<?php echo $form->error($model,'commentsAsLabels'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'generateOverridableMethods'); ?>
		<?php echo $form->checkBox($model,'generateOverridableMethods'); ?>
		<div style="display:none;"></div>
		<?php echo $form->error($model,'generateOverridableMethods'); ?>
	</div>

<?php $this->endWidget(); ?>

<style type="text/css">.span-4 {width:200px;}</style>
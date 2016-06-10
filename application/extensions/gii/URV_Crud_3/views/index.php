<?php
$class=get_class($model);
Yii::app()->clientScript->registerScript('gii.crud',"
$('#{$class}_controller').change(function(){
	$(this).data('changed',$(this).val()!='');
});
$('#{$class}_model').bind('keyup change', function(){
	var controller=$('#{$class}_controller');
	if(!controller.data('changed')) {
		var id=new String($(this).val().match(/\\w*$/));
		if(id.length>0)
			id=id.substring(0,1).toLowerCase()+id.substring(1);
		controller.val(id);
	}
});

// Перевод страницы
$('.form.gii .note').html('Поля со <span class=\"required\">*</span> обязательны. Кликните по <span class=\"sticky\">подсвеченным полям</span> для их редактирования.');
$('label[for=\"".$this->getId()."Code_template\"]').html('Шаблон генератора кода <span class=\"required\">*</span>')
	.next().next().next('.tooltip').html('Выберите папку с шаблонами для генерации кода.');
$('[name=\"preview\"]').val('Предварительный просмотр');
$('[name=\"generate\"]').val('Сгенерировать');
");
?>
<h1>Генератор CRUD (меньше тормозит в Internet Explorer). Позволяет вносить более 1000 записей в отличие от предыдущего CRUD</h1>

<p>Герерирует контроллер и вьюшки (представления), которые выполняют 
типичные CRUD-операции (Create, Read, Update, Delete) для указанной модели.</p>

<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'model'); ?>
		<?php echo $form->textField($model,'model',array('size'=>65)); ?>
		<div class="tooltip">
			Класс модели чувствителен к регистру. Можно задать как имя класса (<code>Post</code>), так и
			псевдоним пути к файлу (e.g. <code>application.models.Post</code>).
			Убедитесь, что указанный класс существует и загружен (autoloaded).
		</div>
		<?php echo $form->error($model,'model'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'controller'); ?>
		<?php echo $form->textField($model,'controller',array('size'=>65)); ?>
		<div class="tooltip">
			Имя контроллера чувствительно к регистру. CRUD-контроллеры часто называют так же, как и модель,
			чтобы ассоциировать их с данной моделью. Например:
			<ul>
				<li><code>post</code> генерирует <code>PostController.php</code></li>
				<li><code>postTag</code> генерирует <code>PostTagController.php</code></li>
				<li><code>admin/user</code> генерирует <code>admin/UserController.php</code>.
					Если в приложении разрешен модуль <code>admin</code>, то
					сгенерируется контроллер <code>UserController</code> (и прочие файлы)
					в указанном модуле.
				</li>
			</ul>
		</div>
		<?php echo $form->error($model,'controller'); ?>
	</div>

	<div class="row sticky">
		<?php echo $form->labelEx($model,'baseControllerClass'); ?>
		<?php echo $form->textField($model,'baseControllerClass',array('size'=>65)); ?>
		<div class="tooltip">
			Класс, который будет наследоваться CRUD-контроллером. 
			Убедитесь, что он существует и загружен (autoloaded).
		</div>
		<?php echo $form->error($model,'baseControllerClass'); ?>
	</div>

<?php $this->endWidget(); ?>

<style type="text/css">.span-4 {width:200px;}</style>
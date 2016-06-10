<?php
Yii::app()->clientScript->registerScript('gii.model',"
// Перевод страницы
$('.form.gii .note').html('Поля со <span class=\"required\">*</span> обязательны. Кликните по <span class=\"sticky\">подсвеченным полям</span> для их редактирования.');
$('label[for=\"".$this->getId()."Code_template\"]').html('Шаблон генератора кода <span class=\"required\">*</span>')
	.next().next().next('.tooltip').html('Выберите папку с шаблонами для генерации кода.');
$('[name=\"preview\"]').val('Предварительный просмотр');
$('[name=\"generate\"]').val('Сгенерировать');
");
?>
<h1>Генератор Формы</h1>

<p>Генерирует вьюшку (представление) для отображения формы, собирающая исходные данные указанной модели.</p>

<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'model'); ?>
		<?php echo $form->textField($model,'model', array('size'=>65)); ?>
		<div class="tooltip">
			Имя класса модели чувствительно к регистру. Можно задавать как имя класса (<code>Post</code>)
			так и псевдоним пути (<code>application.models.LoginForm</code>).
			Если класс используется впервые, то он должен быть загружен (autoloaded).
		</div>
		<?php echo $form->error($model,'model'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'viewName'); ?>
		<?php echo $form->textField($model,'viewName', array('size'=>65)); ?>
		<div class="tooltip">
			Имя файла генерируемой вьюшки (представления), например, <code>site/contact</code>, <code>user/login</code>.
			Файл будет сгенерирован в папке вьюшек (представлений), указанной ниже.
		</div>
		<?php echo $form->error($model,'viewName'); ?>
	</div>
	<div class="row sticky">
		<?php echo $form->labelEx($model,'viewPath'); ?>
		<?php echo $form->textField($model,'viewPath', array('size'=>65)); ?>
		<div class="tooltip">
			Папка, в которой нужно сгенерировать вьюшку (преставление).
			Она должна быть указана в виде псевдонима пути, например, <code>application.views</code>,
			<code>mymodule.views</code>.
		</div>
		<?php echo $form->error($model,'viewPath'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'scenario'); ?>
		<?php echo $form->textField($model,'scenario', array('size'=>65)); ?>
		<div class="tooltip">
			Сценарий, под которым модель должна собирать пользовательские данные.
			Например, модель <code>User</code> может иметь сценарии: <code>login</code>, <code>register</code>.
			Чтобы создать форму для авторизации пользователя, то необходимо присвоить сценарий code>login</code>.
			Оставьте поле пустым, если модель не нуждается в конкретных сценариях.
		</div>
		<?php echo $form->error($model,'scenario'); ?>
	</div>

<?php $this->endWidget(); ?>

<style type="text/css">.span-4 {width:200px;}</style>
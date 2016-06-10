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
<h1>Генератор Контроллера</h1>

<p>Этот генератор помогает быстро сгенерировать класс контроллера, 
одно или несколько действий и их соотвествующие вьюшки (представления).</p>

<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'controller'); ?>
		<?php echo $form->textField($model,'controller',array('size'=>65)); ?>
		<div class="tooltip">
			Имя контроллера чувствительно к регистру. Например:
			<ul>
				<li><code>post</code> генерирует <code>PostController.php</code></li>
				<li><code>postTag</code> генерирует <code>PostTagController.php</code></li>
				<li><code>admin/user</code> генерирует <code>admin/UserController.php</code>.
					Т.е., если в приложении имеется включенный модуль <code>admin</code>,
					то контроллер <code>UserController</code> сгенерируется в этом модуле.
					Убедитесь, что указанное имя модуля совпадает по большим и маленьким буквам.
				</li>
			</ul>
		</div>
		<?php echo $form->error($model,'controller'); ?>
	</div>

	<div class="row sticky">
		<?php echo $form->labelEx($model,'baseClass'); ?>
		<?php echo $form->textField($model,'baseClass',array('size'=>65)); ?>
		<div class="tooltip">
			Это класс, из которого наследуется контроллер.
			Убедитесь, что указанный класс существует и загружен (autoloaded).
		</div>
		<?php echo $form->error($model,'baseClass'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'actions'); ?>
		<?php echo $form->textField($model,'actions',array('size'=>65)); ?>
		<div class="tooltip">
			Имена действий чувствительны к регистру. Действия разделяются запятыми или пробелами.
		</div>
		<?php echo $form->error($model,'actions'); ?>
	</div>

<?php $this->endWidget(); ?>

<style type="text/css">.span-4 {width:200px;}</style>
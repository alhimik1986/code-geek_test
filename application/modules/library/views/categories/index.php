<?php
/*
	Список всех записей в базе данных.
	
	@var $this  CategoriesController - Текущий контроллер
	@var $model Categories (Model)   - Модель (для отображения подписей аттрибутов)
*/
?>

<?php
$pageTitle = 'Категории';
$this->setPageTitle($pageTitle);                                 // Заголовок страницы
$this->breadcrumbs=array($pageTitle);                            // Хлебные крошки (навигация сайта)
$this->renderPartial('application.modules.library.views.books._js_plugins'); // Подключаю jquery-плагины
$this->renderPartial('application.modules.library.views.books._index_css'); // Подключаю css-стили
$this->renderPartial('_index_js_table', array('model'=>$model)); // Подключаю java-скрипты для поиска в таблице
?>



<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>$model::className().'-search-form',
	'enableClientValidation' => false,
)); ?>


<button id="urv-<?php echo lcfirst($model::className()); ?>-form-button-create" class="ajax-form-button-create" style="margin-bottom:5px;"><i class="icon-button-create"></i><?php echo Yii::t('app', 'Create'); ?></button>


<!-- Шапка таблицы -->
<div style="padding:10px; color:#333; border:1px solid #a0a0a0; border-radius:5px; background:#d0d0d0; background: linear-gradient(to bottom, rgba(228, 228, 228, 1) 0%, rgba(205, 205, 205, 1) 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);">
	<span style="margin-left:10px;"><?php echo Yii::t('app', 'Show'); ?>: 
		<span>
			<?php echo CHtml::dropDownList('limit', 10, array(10=>10, 30=>30, 100=>100, 1000=>1000), array(
				'class'=>'search-on-change'
			)); ?>
		</span> <?php echo Yii::t('app', 'rows'); ?>
	</span>
	
	<span style="margin-left:30px;">
		<?php echo CHtml::hiddenField($model::className().'[removed]', '0', array('id'=>$model::className().'_removed1')); ?>
		<?php echo CHtml::checkbox($model::className().'[removed]', false, array('id'=>$model::className().'_removed2', 'class'=>'search-on-change')); ?>
		<?php echo CHtml::label(Yii::t('app', 'Show removed'), $model::className().'_removed2'); ?>
	</span>
</div>
<!-- Таблица со всеми записями -->
<table id="urv-table" class="urv-table" style="width:100%">
	<thead>
		<tr>
			<th style="width:30px"><?php echo Yii::t('app', '№'); ?></th>
			<th style="" column_name="<?php echo $model::table(); ?>.name"><?php echo $model->getAttributeLabel('name'); ?></th>
		</tr>
		<tr class="no-top-border-in-child" style="background-color:#ddd;">
			<th></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[name]" placeholder="<?php echo $model->getAttributeLabel('name'); ?>..." class="search-on-change"></th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
<?php $this->endWidget(); ?>
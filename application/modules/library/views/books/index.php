<?php
/*
	Список всех записей в базе данных.
	
	@var $this  BooksController - Текущий контроллер
	@var $model Books (Model)   - Модель (для отображения подписей аттрибутов)
*/
?>

<?php
$pageTitle = 'Книги';
$this->setPageTitle($pageTitle);                                 // Заголовок страницы
$this->breadcrumbs=array($pageTitle);                            // Хлебные крошки (навигация сайта)
$this->renderPartial('_js_plugins');                             // Подключаю jquery-плагины
$this->renderPartial('_index_js_table', array('model'=>$model)); // Подключаю java-скрипты для поиска в таблице
$this->renderPartial('_index_js_files', array('model'=>new BooksFiles)); // Подключаю java-скрипты для обработки файлов
$this->renderPartial('_index_js_replace');                       // Подключаю java-скрипты для перемещения книг из категорий в категорию
$this->renderPartial('_index_css');                              // Подключаю css-стили
?>



<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>$model::className().'-search-form',
	'enableClientValidation' => false,
)); ?>


<div>
	<button id="urv-<?php echo lcfirst($model::className()); ?>-form-button-create" class="ajax-form-button-create" style="margin-bottom:5px; display:block; float:left;"><i class="icon-button-create"></i><?php echo Yii::t('app', 'Create'); ?></button>

	<div style="float:left; margin-left:50px;"><h2 class="toggle-parent-next">Перемещение</h2></div>
	<div style="float:left; display:none;">
		<div style="float:left; margin-left:10px;">
			<?php echo CHtml::dropDownList('sub_category_id', '', SubCategories::dropDownList(), array(
				'class'=>'chosen',
				'style'=>'width:200px;',
				'data-placeholder'=>'Подкатегории',
				'data-no_results_text' => 'Ничего не найдено по',
				'id' => $model->className().'_sub_category_id1',
			)); ?>
		</div>
		<button style="float:left; display:block; margin-left:20px;" type="button" id="replace-to-category">Перенести найденные записи в указанную категорию</button>
		<div style="clear:both;"></div>
	</div>
	<div style="clear:both;"></div>
</div>


<!-- Шапка таблицы -->
<div style="padding:10px; color:#333; border:1px solid #a0a0a0; border-radius:5px; background:#d0d0d0; background: linear-gradient(to bottom, rgba(228, 228, 228, 1) 0%, rgba(205, 205, 205, 1) 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);">
	<?php echo Yii::t('app', 'Search'); ?>: <input type="text" name="<?php echo $model::className().'[all_text]'; ?>" style="width:180px;background:white;" placeholder="<?php echo Yii::t('app', 'on all parameters'); ?>..." title="<?php echo Yii::t('app', 'Minimum 3 characters'); ?>" class="search-on-change">
	
	<span style="margin-left:10px;"><?php echo Yii::t('app', 'Show'); ?>: 
		<span>
			<?php echo CHtml::dropDownList('limit', 10, array(10=>10, 30=>30, 100=>100, 1000=>1000, 10000=>10000, 100000=>100000), array(
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
			<th style="width:30px">Обложка</th>
			<th style="width:200px" column_name="<?php echo $model::table(); ?>.category_id"><?php echo $model->getAttributeLabel('category_id'); ?></th>
			<th style="width:200px" column_name="<?php echo $model::table(); ?>.sub_category_id"><?php echo $model->getAttributeLabel('sub_category_id'); ?></th>
			<th style="width:150px" column_name="<?php echo $model::table(); ?>.title"><?php echo $model->getAttributeLabel('title'); ?></th>
			<th style="width:150px" column_name="<?php echo $model::table(); ?>.description"><?php echo $model->getAttributeLabel('description'); ?></th>
			<th style="width:150px"><?php echo $model->getAttributeLabel('files'); ?></th>
		</tr>
		<tr class="no-top-border-in-child" style="background-color:#ddd;">
			<th></th>
			<th></th>
			<th><?php echo CHtml::dropDownList($model->className().'[category_ids]', '', Categories::dropDownList(), array(
				'class'=>'chosen search-on-change',
				'style'=>'width:200px;',
				'data-placeholder'=>'Категории',
				'data-no_results_text' => 'Ничего не найдено по',
				'multiple' => 'true',
			)); ?></th>
			<th><?php echo CHtml::dropDownList($model->className().'[sub_category_ids]', '', SubCategories::dropDownList(), array(
				'class'=>'chosen search-on-change',
				'style'=>'width:200px;',
				'data-placeholder'=>'Подкатегории',
				'data-no_results_text' => 'Ничего не найдено по',
				'multiple' => 'true',
			)); ?></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[sub_category_id]" placeholder="<?php echo $model->getAttributeLabel('sub_category_id'); ?>..." class="search-on-change"></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[description]" placeholder="<?php echo $model->getAttributeLabel('description'); ?>..." class="search-on-change"></th>
			<th><!--<input type="text" name="<?php echo BooksFiles::className(); ?>[name]" placeholder="<?php echo $model->getAttributeLabel('files'); ?>..." class="search-on-change">--></th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
<?php $this->endWidget(); ?>
<?php
/*
	Список всех записей в базе данных.
	
	@var $this  BooksController - Текущий контроллер
	@var $model Books (Model)   - Модель (для отображения подписей аттрибутов)
*/
?>

<?php
$pageTitle = 'Библиотека';
$this->setPageTitle($pageTitle);                                 // Заголовок страницы
$this->breadcrumbs=array($pageTitle);                            // Хлебные крошки (навигация сайта)
$this->renderPartial('application.modules.library.views.books._js_plugins'); // Подключаю jquery-плагины
$this->renderPartial('application.modules.library.views.books._index_css'); // Подключаю css-стили
$this->renderPartial('_index_css'); // Подключаю css-стили
$this->renderPartial('_index_js_table', array('model'=>$model)); // Подключаю java-скрипты для поиска в таблице
?>

<div class="library-wrapper">

	<?php if (Yii::app()->user->isGuest): ?>
		<div style="text-align:right;">
			<?php echo CHtml::link('Вход', array($this->createUrl('//users/user/login'))); ?><br><br>
		</div>
	<?php endIf; ?>


	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>$model::className().'-search-form',
		'enableClientValidation' => false,
	)); ?>


	<!-- Шапка таблицы -->
	<div style="padding:10px; color:#333; border:1px solid #a0a0a0; border-radius:5px; background:#d0d0d0; background: linear-gradient(to bottom, rgba(228, 228, 228, 1) 0%, rgba(205, 205, 205, 1) 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);">
		<?php echo Yii::t('app', 'Search'); ?>: <input type="text" name="<?php echo $model::className().'[all_text]'; ?>" style="width:180px;background:white;" placeholder="<?php echo Yii::t('app', 'on all parameters'); ?>..." title="<?php echo Yii::t('app', 'Minimum 3 characters'); ?>" class="search-on-change">
		
		<span style="margin-left:10px;"><?php echo Yii::t('app', 'Show'); ?>: 
			<span>
				<?php echo CHtml::dropDownList('limit', 10, array(5=>5, 10=>10, 30=>30), array(
					'class'=>'search-on-change'
				)); ?>
			</span> <?php echo Yii::t('app', 'rows'); ?>
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
</div>

<div class="view-content-wrapper"></div>
<?php
/*
	Список пользователей.
	
	@var $this   UsersController
	@var $model  Users (CActiveRecord)  - Модель пользователя (для отображения подписей аттрибутов)
	$var $uses   array                  - Список пользователей
*/
?>

<?php
$pageTitle = Yii::t('users.app', 'Users');
$this->setPageTitle($pageTitle);            // Заголовок страницы
$this->breadcrumbs = array($pageTitle);     // Хлебные крошки (навигация сайта)

$this->renderPartial('_index_css');           // Подключаю java-скрипты
$this->renderPartial('_js_plugins');          // Загружаю стандарные jquery-плагины
$this->renderPartial('_index_js_table', array('model'=>$model));  // Подключаю java-скрипты
?>

<button id="urv-<?php echo lcfirst($model::className()); ?>-form-button-create" class="ajax-form-button-create" style="margin-bottom:5px;"><i class="icon-button-create"></i><?php echo Yii::t('app', 'Create'); ?></button>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>$model::className().'-search-form',
	'enableClientValidation' => false,
)); ?>

<!-- Шапка таблицы -->
<div style="padding:10px; color:#333; border:1px solid #a0a0a0; border-radius:5px; background:#d0d0d0; background: linear-gradient(to bottom, rgba(228, 228, 228, 1) 0%, rgba(205, 205, 205, 1) 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);">
	<?php echo Yii::t('app', 'Search'); ?>: <input type="text" name="<?php echo $model::className().'[all_text]'; ?>" style="width:180px;background:white;" placeholder="<?php echo Yii::t('app', 'on all parameters'); ?>..." title="<?php echo Yii::t('app', 'Minimum 3 characters'); ?>" class="search-on-change">
	
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
<!-- Таблица с пользователями -->
<table id="urv-table" class="urv-table">
	<thead>
		<tr>
			<th style="width:20px"><?php echo Yii::t('app', '№'); ?></th>
			<th style="width:100px" column_name="<?php echo $model::table(); ?>.last_name"><?php echo Yii::t('app', 'Full name'); ?></th>
			<th style="width:100px" column_name="<?php echo $model::table(); ?>.role"><?php echo $model->getAttributeLabel('role'); ?></th>
			<th style="width:50px" column_name="<?php echo $model::table(); ?>.username"><?php echo $model->getAttributeLabel('username'); ?></th>
			<th style="width:50px" column_name="<?php echo $model::table(); ?>.email" title="<?php echo $model->getAttributeLabel('email'); ?> / <?php echo $model->getAttributeLabel('subscribed'); ?>"><?php echo Yii::t('users.app', 'E-mail / mailing'); ?></th>
			<th style="width:50px" column_name="<?php echo $model::table(); ?>.phone" title="<?php echo $model->getAttributeLabel('phone'); ?> / <?php echo $model->getAttributeLabel('ip'); ?>"><?php echo Yii::t('users.app', 'Phone / IP-address'); ?></th>
			<th style="width:50px" column_name="<?php echo $model::table(); ?>.comment" title="<?php echo $model->getAttributeLabel('comment'); ?>"><?php echo $model->getAttributeLabel('comment'); ?></th>
			<th style="width:20px" column_name="<?php echo $model::table(); ?>.id"><?php echo $model->getAttributeLabel('id'); ?></th>
		</tr>
		<tr class="no-top-border-in-child" style="background-color:#ddd;">
			<th></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[fio]" placeholder="<?php echo Yii::t('app', 'Full name'); ?>..." class="search-on-change"></th>
			<th><?php echo CHtml::dropDownList($model::className().'[role]', '', ArrayHelper::merge(array(''=>Yii::t('app', 'All roles')), $model::roleListForForm()), array(
				'class'                => 'chosen search-on-change',
				'style'                => 'width:120px;',
				'data-placeholder'     => Yii::t('app', 'Role'),
				'data-no_results_text' => Yii::t('app', 'No results match'),
				'id'                   => $model::className().'_role1',
				//'multiple'             => 'true',
			)); ?></th>
			<th></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[username]" placeholder="<?php echo $model->getAttributeLabel('username'); ?>..." class="search-on-change"></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[email]" placeholder="<?php echo $model->getAttributeLabel('email'); ?>..." class="search-on-change"></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[phone]" placeholder="<?php echo $model->getAttributeLabel('phone'); ?>..." class="search-on-change"></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[comment]" placeholder="<?php echo $model->getAttributeLabel('comment'); ?>..." class="search-on-change"></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[id]" placeholder="<?php echo $model->getAttributeLabel('id'); ?>..." class="search-on-change" style="width:25px;"></th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
<?php $this->endWidget(); ?>
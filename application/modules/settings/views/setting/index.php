<?php
/*
	Настройки системы учета времени.
	
	@var $this   SettingController
	@var $model  Settings (CActiveRecord)  - Модель настроек (для отображения подписей аттрибутов)
	$var $data   array                     - Список записей
*/
?>

<?php
$pageTitle = Yii::t('settings.app', 'System settings');
$this->setPageTitle($pageTitle);           // Заголовок страницы
$this->breadcrumbs = array($pageTitle);    // Хлебные крошки (навигация сайта)
$this->renderPartial('_js_plugins'); // Загружаю стандарные jquery-плагины
$this->renderPartial('_index_js');         // Подключаю java-скрипты
JsCssFiles::css('
	table.urv-table tbody tr.odd:hover, table.urv-table tbody tr.even:hover {/*background-color:#dfe7fe;*/}
	table.urv-table tbody tr.odd, table.urv-table tbody tr.even { cursor:pointer;}
');
?>

<?php // Таблица с списком настроек ?>
<table id="urv-table" class="urv-table">
	<thead>
		<tr>
			<th style="width:80px"><?php  echo $model->getAttributeLabel('name');        ?></th>
			<th style="width:120px"><?php echo $model->getAttributeLabel('label');       ?></th>
			<th style="width:600px"><?php echo $model->getAttributeLabel('description'); ?></th>
			<th><?php                     echo $model->getAttributeLabel('value');       ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $this->renderPartial('_rows', array('data'=>$data)); ?>
	</tbody>
</table>
<?php
$pageTitle = Yii::t('settings.app', 'Clear cache');
$this->breadcrumbs = array($pageTitle); // Хлебные крошки (навигация сайта)
$this->setPageTitle($pageTitle);        // Заголовок страницы

$this->renderPartial('_js_plugins'); // Загружаю стандарные jquery-плагины
$this->renderPartial('_index_js');         // Подключаю java-скрипты
?>
<?php echo Yii::t('settings.app', 'The cache has been cleared.'); ?>
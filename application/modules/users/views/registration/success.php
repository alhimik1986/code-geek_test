<?php
$pageTitle = Yii::t('users.app', 'Application has been accepted');
$this->setPageTitle($pageTitle);              // Заголовок страницы
$this->breadcrumbs = array($pageTitle);       // Хлебные крошки (навигация сайта)
?>
<?php echo Yii::t('users.app', 'Your application has been accepted, please expect your account confirmation by the site administrator. Then you can log in with your username and password.'); ?>
<br>
<br>
<?php echo CHtml::link(Yii::t('users.app', 'Go to the login page'), $this->createUrl(reset(Yii::app()->user->loginUrl))); ?>
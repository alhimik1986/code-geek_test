<?php
$pageTitle = Yii::t('users.app', 'Password recovery');
$this->setPageTitle($pageTitle);
$this->breadcrumbs = array($pageTitle); // Хлебные крошки (навигация сайта)
?>
<?php echo Yii::t('users.app', 'Link to reset sent to your email.'); ?>

<!-- Метка, что тест успешен -->
<div id="test-recovery-success"></div>
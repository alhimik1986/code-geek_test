<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[if lt IE 7 ]> <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru"><!--<![endif]-->
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="ru" />
	<?php JSPlugins::includePlugins(JSPlugins::$includePlugins); ?>
	<?php JSPlugins::cssFile(Yii::app()->request->baseUrl.'/css/style.css'); ?>
	<!--[if lte IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" />
	<![endif]-->
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
<?php if ( ! $this->hideMenuAndBreadcrumbs): ?>
	<!-- Меню -->
	<?php $this->renderPartial('//layouts/menu/_menu'); ?>

	<!-- Навигация -->
	<?php if(isset($this->breadcrumbs) AND Yii::app()->user->inGroup['ADMINS']):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif; ?>
<?php endIf; ?>

	<!-- Вывод flash-сообщений -->
	<?php foreach(Yii::app()->user->getFlashes() as $key => $message) {
			echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
		} ?>

	<!-- Ошибки -->
	<div id="error"></div>
	
	<!-- Контент -->
	<div id="content"><?php echo $content; ?></div>

	<!-- Подвал -->
	<div id="footer"></div><!-- footer -->
</body>
</html>
<?php JSPlugins::mergeAndCompress(); // Вывод всех js- и css-файлов в head ?>
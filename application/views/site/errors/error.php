<?php
/* @var $this SiteController */
/* @var $error array */

$this->pageTitle=Yii::app()->name . ' - '.Yii::t('app', 'Error');
$this->breadcrumbs=array(
	Yii::t('app', 'Error'),
);
JSPlugins::$includePlugins['jquery'] = '';
?>

<h2><?php echo Yii::t('app', 'Error'); ?> <?php echo $code; ?></h2>

<div class="error">
<?php
	$messageText = CHtml::encode($message);
	$messageText = $messageText ? $messageText : CHtml::encode(iconv('windows-1251', 'utf-8', $message));
?>

<?php echo $messageText; ?>
</div>
<?php $this->renderPartial('errors/traces', array('data'=>array('error'=>get_defined_vars()))); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo Yii::t('app', 'The site is blocked!'); ?></title>
</head>
<body>
	<?php if (Yii::app()->controller->action->id == 'siteBlocked'): ?>
	<?php Yii::import('application.modules.users.models.LoginForm'); ?>
	<?php echo CHtml::link(Yii::t('app', 'Back'), $this->createUrl('//settings/setting/blockSite')); ?>
	&nbsp;&nbsp;&nbsp;
	<?php echo CHtml::link(Yii::t('app', 'Home'), $this->createUrl(Users::getUrlForRedirectingUser())); ?>
	<div style="font-size:40px; font-weight:bold; text-align:center; color:green;">
		<?php echo Yii::t('app', 'The page will look like this:'); ?>
	</div>
	<?php endIf; ?>
	
	<div style="width:300px; margin:0 auto; font-family:Arial;">
		<img src="/images/site_is_blocked.png" alt="site is blocked">
	</div>
	
	<div style="font-size:40px; font-weight:bold; margin-top:0px; margin-bottom: 30px; text-align:center;">
		<?php echo Yii::t('app', 'The site is blocked!'); ?>
	</div>
	
	<div style="text-align:center; font-size:20px;">
		<?php echo $blockedReason; ?>
	</div>
</body>
</html>
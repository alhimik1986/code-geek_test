<?php if (Yii::app()->settings->param['Site']['blocked']): ?>
	<div style="background:#555; position:absolute; width:100%; left:0; top:46px; padding:2px 0;">
		<span style="background:#000; color:#f00; font-weight:bold;padding:2px 10px;">
			<?php echo Yii::t('app', 'Warning! The site is blocked! Ordinary users can not work. Only administrators have access.'); ?>
			<?php echo CHtml::link(Yii::t('app', 'Unlock'), $this->createUrl('//settings/setting/blockSite'), array('style'=>'color:#5df;')); ?>
		</span>
	</div>
<?php endIf; ?>
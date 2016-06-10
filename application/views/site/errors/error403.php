<?php echo $pageTitle = Yii::t('app', 'Access is denied!'); ?>
<?php $this->pageTitle = Yii::app()->name.' - '.$pageTitle; ?>
<style type="text/css">
/*<![CDATA[*/
body {font-family:"Verdana";font-weight:normal;color:black;background-color:white;}
h1 { font-family:"Verdana";font-weight:normal;font-size:18pt;color:red }
h2 { font-family:"Verdana";font-weight:normal;font-size:14pt;color:maroon }
h3 {font-family:"Verdana";font-weight:bold;font-size:11pt}
p {font-family:"Verdana";font-weight:normal;color:black;font-size:9pt;margin-top: -5px}
.version {color: gray;font-size:8pt;border-top:1px solid #aaaaaa;}
/*]]>*/
</style>
<div class="format">
	<h1 style="color:red;"><?php echo $pageTitle; ?></h1>
	<h4><?php echo nl2br(CHtml::encode($data['message'])); ?></h4>
	<p>
		<?php echo Yii::t('app', 'You do not have the proper credential to access this page.'); ?>
	</p>
	<p>
		<?php echo Yii::t('app', 'If you think this is a server error, please contact your administrator {admin}.', array('{admin}' => $data['admin'] ? ' '.$data['admin'] : $data['admin'])); ?>
	</p>
	<div class="version">
		<?php echo $data['time']->format('Y-m-d H:i:s') .' '. $data['version']; ?>.
		Error <?php echo $data['code']; ?>.
	</div>
</div>
<?php $this->renderPartial('errors/traces', array('data'=>$data)); ?>
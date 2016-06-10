<?php if (defined('YII_DEBUG') AND YII_DEBUG): ?>
<div class="format">
	<div><?php echo $data['error']['file']; ?> (<?php echo $data['error']['line']; ?>)</div>
	<br>
	<h2><?php echo Yii::t('app', 'Trace'); ?>: </h2>
	<?php if (property_exists(Yii::app()->user, 'param') AND isset(Yii::app()->user->param['id'])): ?>
		<?php $user = Yii::app()->user->param; ?>
		<?php $roles = Users::roleList(); $role = isset($roles[$user['role']]) ? $roles[$user['role']] : 'role='.$user['role']; ?>
		<?php echo 'User: #'.$user['id'].' '.$user['FIO'].' ('.$role.')'; ?></h4>
		<br><br>
	<?php endIf; ?>
	<pre>
<?php echo $data['error']['trace']; ?>
	</pre>

	<?php foreach($data['error']['traces'] as $trace): ?>
	<?php
		$args = '';
		if (isset($trace['args'])) foreach($trace['args'] as $arg) {
			$args .= $args ? ', ' : '';
			$args .= substr(print_r($arg, true), 0, 100);
		}
		$trace['class'] = isset($trace['class']) ? $trace['class'] : '';
		$trace['type']  = isset($trace['type'])  ? $trace['type']  : '';
	?>
		<div style="margin-bottom:30px;">
			<p><b><?php echo $trace['file']; ?> (<?php echo $trace['line']; ?>)</b></p>
			<p><?php echo $trace['class'].$trace['type'].$trace['function']; ?>(<?php echo $args; ?>)</p>
		</div>
	<?php endForeach; ?>
	
</div>
<?php endIf; ?>
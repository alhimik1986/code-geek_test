<?php
$columns = 9; $table = array(); $counter = 0;
$roleList = Users::roleList();

$user_label = Yii::t('users.app', 'User:');
$registrated_label = Yii::t('users.app', 'Registrated:');
$blocked_label = Yii::t('users.app', 'Blocked!');
$notConfirmed_label = Yii::t('users.app', 'Not confirmed!');
$subscribedList = array(
	0 => '<span class="unsubscribed"> ('.Yii::t('app', 'Not subscribed').')</span>',
	1 => '<span class="subscribed"> ('.Yii::t('app', 'Subscribed').')</span>',
);

foreach($data as $value) {
	// Всплывающая подсказка
	//$reg_date = Yii::app()->dateFormatter->format('dd MMMM yyyy', strtotime($value['registration_date']));
	$reg_date = DateHelper::date('d-m-Y', substr($value['registration_date'], 0, 19));
	$title = $user_label.': <b>' . $value['FIO'] . "</b><br>\n".$registrated_label.": <b>" . $reg_date.'</b>';
	$title .= $value['blocked'] ? '<br><div style=&quotes;color:red;&quotes;>'.$blocked_label.'</div>' : '';
	$title .= ! $value['confirmed'] ? '<div style=&quotes;color:red;&quotes;>'.$notConfirmed_label.'</div>' : '';
	
	$host  = $value['email_smtp_secure'] ? $value['email_smtp_secure'].'://' : '';
	$host .= $value['email_host'];
	$host .= ($value['email_port']) ? ':'.$value['email_port'] : '';
	
	$td = array(
		array('content' => ++$counter),
		array('content' => $value['fio']),
		array('content' => isset($roleList[$value['role']]) ? $roleList[$value['role']] : 'role='.$value['role']),
		array('content' => $value['email'] ? $value['email'] . ' / ' . $subscribedList[$value['subscribed']] : ''),
		array('content' => $host),
		array('content' => $value['email_address']),
		array('content' => $value['email_username']),
		array('content' => $value['comment']),
		array('content' => $value['id']),
	);
	$class = ($value['blocked']) ? 'user-blocked' : '';
	$parityClass = ($counter%2) ? 'odd' : 'even';
	$class .= $class ? ' '.$parityClass : $parityClass;
	$table[] = array(
		'td' => $td,
		'attributes' => array(
			'data_id' => $value['id'],
			'title'   => $title,
			'class'   => $class,
		),
	);
}
?>


<!-- Пейджер -->
<?php if (($pagerInfo['count'] > 5 ) OR ($pagerInfo['count'] > $pagerInfo['limit'])): ?>
<tr class="tr-pager">
	<td colspan="<?php echo $columns; ?>">
		<div class="urv-pager"><?php $this->renderPartial('application.modules.js_plugins.views.pager._index_rows_pager', array('pagerInfo'=>$pagerInfo, 'pageName'=>'page')); ?></div>
	</td>
</tr>
<?php endIf; ?>
<!-- Конец Пейджер -->

<?php echo TableHelper::arrayToHtmlTable($table); ?>

<!-- Пейджер -->
<?php if (($pagerInfo['count'] > 5 ) OR ($pagerInfo['count'] > $pagerInfo['limit'])): ?>
<tr class="tr-pager">
	<td colspan="<?php echo $columns; ?>">
		<div class="urv-pager"><?php $this->renderPartial('application.modules.js_plugins.views.pager._index_rows_pager', array('pagerInfo'=>$pagerInfo, 'pageName'=>'page')); ?></div>
	</td>
</tr>
<?php endIf; ?>
<!-- Конец Пейджер -->
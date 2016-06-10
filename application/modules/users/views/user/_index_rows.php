<?php
$columns = 10; $table = array(); $counter = 0;
$deptStrMaxLen = 30;

$roleList = Users::roleList();

$subscribedList = array(
	0 => '<span class="unsubscribed"> ('.Yii::t('app', 'Not subscribed').')</span>',
	1 => '<span class="subscribed"> ('.Yii::t('app', 'Subscribed').')</span>',
);

$user_label = Yii::t('users.app', 'User:');
$registrated_label = Yii::t('users.app', 'Registrated:');
$blocked_label = Yii::t('users.app', 'Blocked!');
$notConfirmed_label = Yii::t('users.app', 'Not confirmed!');

foreach($data as $value) {
	// Всплывающая подсказка
	//$reg_date = Yii::app()->dateFormatter->format('dd MMMM yyyy', strtotime($value['registration_date']));
	$reg_date = DateHelper::date('d-m-Y', substr($value['registration_date'], 0, 19));
	$title = $user_label.': <b>' . $value['FIO'] . "</b><br>\n".$registrated_label.": <b>" . $reg_date.'</b>';
	$title .= $value['blocked'] ? '<br><div style=&quotes;color:red;&quotes;>'.$blocked_label.'</div>' : '';
	$title .= ! $value['confirmed'] ? '<div style=&quotes;color:red;&quotes;>'.$notConfirmed_label.'</div>' : '';

	$fio = $value['fio'];// . '<span style="font-size:0px;">'.$value['first_name'] . $value['middle_name'] . '</span>';
	$role = isset($roleList[$value['role']]) ? $roleList[$value['role']] : 'role='.$value['role'];
	$email = $value['email'] ? $value['email'] . ' / ' . $subscribedList[$value['subscribed']] : '';
	
	$email_subscribed = $value['email'] ? $value['email'] . ' / ' . $subscribedList[$value['subscribed']] : '';
	$phone = $value['phone'];
	
	$td = array(
		array('content'=>++$counter),
		array('content'=>$fio),
		array('content'=>$role),
		array('content'=>$value['username']),
		array('content'=>$email_subscribed),
		array('content'=>$phone.$ip),
		array('content'=>$value['comment']),
		array('content'=>$value['id']),
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
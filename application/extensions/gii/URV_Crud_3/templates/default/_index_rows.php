<?php
	$max_columns = 7; // Максиальное количество колонок
	$count=0; $array=array();
	foreach($this->tableSchema->columns as $column)
	{
		if($column->isPrimaryKey)
			continue;
		if(++$count==$max_columns)
			$array[] = "/*";
		$array[] = "array('content'=>\$value['$column->name']),";
	}
	if($count>=$max_columns)
		$array[] = "*/";
?>
<?php echo "<?php
/**
	
	
	@var \$this         ".$this->modelClass."Controller  - Текущий контроллер
	@var \$data         array                            - Результаты поиска
	@var \$pagerInfo    array                            - Информация о пейджере
	@var \$search       array                            - Параметры поиска
 */
\$columns = ".(count($array))."; // Число колонок в таблице
?>
";
?>

<?php echo 
"<?php
\$table = array(); \$counter = 0; \$parity = 0;
foreach(\$data as \$value) {
	\$td = array(
		//array('content'=>++\$counter),
";
?>
<?php foreach($array as $item): ?>
		<?php echo $item."\n"; ?>
<?php endforeach; ?>
<?php echo "
	);
	\$table[] = array(
		'td' => \$td,
		'attributes' => array(
			'data_id' => \$value['".$this->tableSchema->primaryKey."'],
			'class' => (++\$parity%2) ? 'odd' : 'even',
		),
	);
}
?>
";
?>


<?php echo "
<!-- Пейджер -->
<?php if ((\$pagerInfo['count'] > 5 ) OR (\$pagerInfo['count'] > \$pagerInfo['limit'])): ?>
<tr class=\"tr-pager\">
	<td colspan=\"<?php echo \$columns; ?>\">
		<div class=\"urv-pager\"><?php \$this->renderPartial('application.modules.js_plugins.views.pager._index_rows_pager', array('pagerInfo'=>\$pagerInfo, 'pageName'=>'page')); ?></div>
	</td>
</tr>
<?php endIf; ?>
<!-- Конец Пейджер -->

<?php echo TableHelper::arrayToHtmlTable(\$table); ?>

<!-- Пейджер -->
<?php if ((\$pagerInfo['count'] > 5 ) OR (\$pagerInfo['count'] > \$pagerInfo['limit'])): ?>
<tr class=\"tr-pager\">
	<td colspan=\"<?php echo \$columns; ?>\">
		<div class=\"urv-pager\"><?php \$this->renderPartial('application.modules.js_plugins.views.pager._index_rows_pager', array('pagerInfo'=>\$pagerInfo, 'pageName'=>'page')); ?></div>
	</td>
</tr>
<?php endIf; ?>
<!-- Конец Пейджер -->
"; ?>
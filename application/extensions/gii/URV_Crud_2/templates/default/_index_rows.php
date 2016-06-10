<?php
	$count=0; $array=array();
	foreach($this->tableSchema->columns as $column)
	{
		if($column->isPrimaryKey)
			continue;
		if(++$count==7)
			$array[] = "/*";
		$array[] = "\$value['$column->name'],";
	}
	if($count>=7)
		$array[] = "*/";
?>
<?php echo 
"<?php
\$table = array(); \$counter = isset(\$count) ? \$count-1 : 0;
foreach(\$data as \$value) {
	\$table[] = array(
		// ++\$counter,
";
?>
<?php foreach($array as $item): ?>
		<?php echo $item."\n"; ?>
<?php endforeach; ?>
<?php echo "
		'attributes' => array(
			'data_id' => \$value['".$this->tableSchema->primaryKey."'],
		),
	);
}

echo json_encode(
	// добавляю пробелы в пустых колонках (для ie 7)
	TableHelper::addSpacesToEmptyCells(\$table)
);";
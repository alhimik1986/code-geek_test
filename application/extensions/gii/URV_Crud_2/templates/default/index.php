<?php
	// Получаю список комментарии=>строка_кода, чтобы их выравнить
	$strings = array(
		' - Текущий контроллер'                           => '@var $this  '.$this->getControllerClass(),
		' - Модель (для отображения подписей аттрибутов)' => '@var $model '.$this->getModelClass().' ('.get_parent_class($this->getModelClass()).')',
		' - Список записей'                               => '@var $'.lcfirst($this->getModelClass()).' array',
	);
	$maxLengthName = 0;
	foreach($strings as $name) {
		if ($maxLengthName < strlen($name)) $maxLengthName = strlen($name);
	}
?>
<?php echo "<?php\n"; ?>
/*
	Список всех записей в базе данных.
	
<?php foreach($strings as $comment=>$string): ?>
	<?php echo $string . str_repeat(' ', $maxLengthName - strlen($string)) . "$comment\n"; ?>
<?php endforeach; ?>
*/
<?php echo "?>\n"; ?>

<?php
	// Получаю список комментарии=>строка_кода, чтобы их выравнить
	$strings = array(
		'Заголовок страницы'               => "\$this->setPageTitle(\$pageTitle);",
		'Хлебные крошки (навигация сайта)' => "\$this->breadcrumbs=array(\$pageTitle);",
		'Подключаю jquery-плагины'         => "\$this->renderPartial('_js_plugins');",
		'Подключаю java-скрипты'           => "\$this->renderPartial('_index_js', array('model'=>\$model));",
	);
	$maxLengthName = 0;
	foreach($strings as $name) {
		if ($maxLengthName < strlen($name)) $maxLengthName = strlen($name);
	}
?>
<?php echo "<?php\n"; ?>
$pageTitle = '<?php echo $this->modelClass; ?>';
<?php foreach($strings as $comment=>$string): ?>
<?php echo $string . str_repeat(' ', $maxLengthName - strlen($string)) . " // $comment\n"; ?>
<?php endforeach; ?>
JsCssFiles::css('
	table.urv-table tbody tr.odd, table.urv-table tbody tr.even {cursor:pointer;}
');
<?php echo '?>'; ?>


<?php
$count=0; $thead = array();
$thead[] = '<!-- <th style="width:50px"><?php echo Yii::t(\'app\', \'№\'); ?></th> -->';
foreach($this->tableSchema->columns as $column)
{
	if($column->isPrimaryKey)
		continue;
	if(++$count==7)
		$thead[] = "<!--";
	$thead[] = '<th style="width:150px"><?php echo $model->getAttributeLabel('."'$column->name'".'); ?></th>';
}
if($count>=7)
	$thead[] = "-->";
?>

<!-- Таблица со всеми записями -->
<table id="urv-table" class="urv-table">
	<thead>
		<tr>
<?php foreach($thead as $th): ?>
			<?php echo $th."\n"; ?>
<?php endforeach; ?>
		</tr>
	</thead>
	<tbody></tbody>
</table>
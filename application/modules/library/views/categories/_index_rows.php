<?php
/**
	
	
	@var $this         CategoriesController  - Текущий контроллер
	@var $data         array                            - Результаты поиска
	@var $pagerInfo    array                            - Информация о пейджере
	@var $search       array                            - Параметры поиска
 */
$columns = 2; // Число колонок в таблице
?>

<?php
$table = array(); $counter = 0; $parity = 0;
foreach($data as $value) {
	$td = array(
		array('content'=>++$counter),
		array('content'=>$value['name']),
	);
	$table[] = array(
		'td' => $td,
		'attributes' => array(
			'data_id' => $value['id'],
			'class' => (++$parity%2) ? 'odd' : 'even',
		),
	);
}

$this->renderPartial('application.modules.js_plugins.views.pager._index_rows_rowsAndPager', array(
	'pagerInfo'=>$pagerInfo, 'pageName'=>'page', 'columns'=>$columns, 'table'=>$table));
?>
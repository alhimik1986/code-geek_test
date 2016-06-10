<?php
/**
	
	
	@var $this         BooksController  - Текущий контроллер
	@var $data         array                            - Результаты поиска
	@var $pagerInfo    array                            - Информация о пейджере
	@var $search       array                            - Параметры поиска
 */
$columns = 9; // Число колонок в таблице
?>

<?php
$table = array(); $counter = 0; $parity = 0;
$subCategories = SubCategories::getArrayByArray(array('removed'=>0));
$categories = Categories::getArrayByArray(array('removed'=>0));
$files = BooksFiles::getFileNames(ArrayHelper::getListIdInKey($data, 'id'));
$rand = DateHelper::date('YmdHis');

foreach($data as $value) {
	$description = iconv_substr($value['description'], 0, 100, 'utf-8');
	$description = strip_tags($description);
	
	$category = ArrayHelper::getValue2($categories, $value['category_id'], 'name', 'category_id='.$value['category_id']);
	$subCategory = ArrayHelper::getValue2($subCategories, $value['sub_category_id'], 'name', 'sub_category_id='.$value['sub_category_id']);
	
	$_files = isset($files[$value['id']]) ? $files[$value['id']] : array();
	$file = '';
	foreach($_files as $_file) {
		$file .= $file ? ', ' : '';
		//$file .= '<a href="'.$this->createUrl('file', array('id'=>$_file['id'])).'" target="_blank">'.$_file['name'].'</a>';
		$file .= $_file['name'];
	}
	
	$photo = '<img src="'.$this->createUrl('books/viewPhoto', array('id'=>$value['id'], '_'=>$rand)).'" style="width:40px;" alt="Фото" title="" />';
	
	$td = array(
		array('content'=>++$counter),
		array('content'=>$photo),
		array('content'=>$category),
		array('content'=>$subCategory),
		array('content'=>$value['title']),
		array('content'=>$description),
		array('content'=>$file),
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
<?php
	// Получаю список комментарии=>строка_кода, чтобы их выравнить
	$strings = array(
		' - Текущий контроллер'                           => '@var $this  '.$this->getControllerClass(),
		' - Модель (для отображения подписей аттрибутов)' => '@var $model '.$this->getModelClass().' ('.get_parent_class($this->getModelClass()).')',
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
		'Заголовок страницы'                          => "\$this->setPageTitle(\$pageTitle);",
		'Хлебные крошки (навигация сайта)'            => "\$this->breadcrumbs=array(\$pageTitle);",
		'Подключаю jquery-плагины'                    => "\$this->renderPartial('_js_plugins');",
		'Подключаю java-скрипты для поиска в таблице' => "\$this->renderPartial('_index_js_table', array('model'=>\$model));",
		'Подключаю css-стили'                         => "\$this->renderPartial('_index_css');",
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
<?php echo '?>'; ?>


<?php
$max_columns = 7;
$count=0; $thead = array();
$thead[] = '<!-- <th style="width:50px"><?php echo Yii::t(\'app\', \'№\'); ?></th> -->';
foreach($this->tableSchema->columns as $column)
{
	if($column->isPrimaryKey)
		continue;
	if(++$count==$max_columns)
		$thead[] = "<!--";
	$thead[] = '<th style="width:150px" column_name="<?php echo $model::table(); ?>.'.$column->name.'"><?php echo $model->getAttributeLabel('."'$column->name'".'); ?></th>';
}
if($count>=$max_columns)
	$thead[] = "-->";
?>

<?php echo "
<?php \$form=\$this->beginWidget('CActiveForm', array(
	'id'=>\$model::className().'-search-form',
	'enableClientValidation' => false,
)); ?>


<button id=\"urv-<?php echo lcfirst(\$model::className()); ?>-form-button-create\" class=\"ajax-form-button-create\" style=\"margin-bottom:5px;\"><i class=\"icon-button-create\"></i><?php echo Yii::t('app', 'Create'); ?></button>


<!-- Шапка таблицы -->
<div style=\"padding:10px; color:#333; border:1px solid #a0a0a0; border-radius:5px; background:#d0d0d0; background: linear-gradient(to bottom, rgba(228, 228, 228, 1) 0%, rgba(205, 205, 205, 1) 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);\">
	<!-- <?php echo Yii::t('app', 'Search'); ?>: <input type=\"text\" name=\"<?php echo \$model::className().'[all_text]'; ?>\" style=\"width:180px;background:white;\" placeholder=\"<?php echo Yii::t('app', 'on all parameters'); ?>...\" title=\"<?php echo Yii::t('app', 'Minimum 3 characters'); ?>\" class=\"search-on-change\"> -->
	
	<span style=\"margin-left:10px;\"><?php echo Yii::t('app', 'Show'); ?>: 
		<span>
			<?php echo CHtml::dropDownList('limit', 10, array(10=>10, 30=>30, 100=>100, 1000=>1000), array(
				'class'=>'search-on-change'
			)); ?>
		</span> <?php echo Yii::t('app', 'rows'); ?>
	</span>
	
	<!--<?php echo CHtml::hiddenField(\$model::className().'[partial_match]', false); ?>
	<label style=\"color:#501010;margin-left:20px;\" title=\"<?php echo Yii::t('app', 'Search by strict condition'); ?>\">
		<?php echo CHtml::checkbox(\$model::className().'[partial_match]', true, array('class'=>'search-on-change')); ?> 
		<?php echo Yii::t('app', 'Partial match'); ?>
	</label-->
	
	<!--
	<span style=\"margin-left:30px;\">
		<?php echo CHtml::hiddenField(\$model::className().'[removed]', '0', array('id'=>\$model::className().'_removed1')); ?>
		<?php echo CHtml::checkbox(\$model::className().'[removed]', false, array('id'=>\$model::className().'_removed2', 'class'=>'search-on-change')); ?>
		<?php echo CHtml::label(Yii::t('app', 'Show removed'), \$model::className().'_removed2'); ?>
	</span>
	-->
</div>"; ?>

<!-- Таблица со всеми записями -->
<table id="urv-table" class="urv-table" style="width:100%">
	<thead>
		<tr>
<?php foreach($thead as $th): ?>
			<?php echo $th."\n"; ?>
<?php endforeach; ?>
		</tr>
		<tr class="no-top-border-in-child" style="background-color:#ddd;">
<?php
$count = 0;
echo '<!-- <th></th> -->';
foreach($this->tableSchema->columns as $column)
{
	if($column->isPrimaryKey)
		continue;
	if(++$count==$max_columns)
		echo "<!--\n";
	echo "<th><input type=\"text\" name=\"<?php echo \$model::className(); ?>[".$column->name."]\" placeholder=\"<?php echo \$model->getAttributeLabel('".$column->name."'); ?>...\" class=\"search-on-change\"></th>\n";
}
if($count>=$max_columns)
	echo "-->\n";
?>
		</tr>
	</thead>
	<tbody></tbody>
</table>
<?php echo "<?php \$this->endWidget(); ?>";
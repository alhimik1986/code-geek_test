<?php
/**
 * This is the template for generating an action view file.
 * The following variables are available in this template:
 * - $this: the ControllerCode object
 * - $action: the action ID
 */
?>
<?php echo "<?php\n"; ?>
/* 
	Вьюшка для <?php echo $this->getControllerClass().".\n"; ?>
	@var $this <?php echo $this->getControllerClass().".\n"; ?>
*/
<?php echo '?>'; ?>


<?php
	// Получаю список комментарии=>строка_кода, чтобы их выравнить
	$strings = array(
		'Заголовок страницы' => "\$this->setPageTitle('');",
		'Хлебные крошки (навигация сайта)' => "\$this->breadcrumbs=array('');",
	);
	$maxLengthName = 0;
	foreach($strings as $name) {
		if ($maxLengthName < strlen($name)) $maxLengthName = strlen($name);
	}
?>
<?php echo "<?php\n"; ?>
<?php foreach($strings as $comment=>$string): ?>
<?php echo $string . str_repeat(' ', $maxLengthName - strlen($string)) . " // $comment\n"; ?>
<?php endforeach; ?>
<?php echo '?>'; ?>


<h1>Это контроллер: <?php echo '<?php'; ?> echo $this->id . '/' . $this->action->id; ?></h1>


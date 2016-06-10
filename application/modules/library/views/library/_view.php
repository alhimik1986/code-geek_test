<?php
/*
	Просмотр книги.
	
	@var $book         array   - Книга
	@var $files        array   - Список имен прикрепленных файлов
	@var $category     string  - Категория
	@var $subCategory  string  - Подкатегория
*/
?>
<div class="format view-content">
	<a href="#" class="content-close go-back">Назад</a>
	
	<div style="font-size:11px; margin-left:5px;"><?php echo $category; ?>: <?php echo $subCategory; ?></div>
	
	<h2><?php echo $book['title']; ?></h2>
	
	<?php if ($book['photo']): ?>
	<img src="<?php echo $this->createUrl('//library/books/viewPhoto', array('id'=>$book['id'])); ?>" alt="Обложка" title="<?php echo htmlspecialchars($book['title']); ?>" style="max-width:100%;" />
	<?php endIf; ?>
	
	<div><?php echo $book['description']; ?></div>

	<?php if ($files): ?>
		<b>Ссылки для скачивания:</b>&nbsp;&nbsp;
		<?php foreach($files as $file): ?>
			<?php echo CHtml::link($file['name'], array('//library/books/file', 'id'=>$file['id'])); ?>&nbsp;&nbsp;&nbsp;
		<?php endForeach; ?>
		<br><br>
	<?php endIf; ?>

</div>
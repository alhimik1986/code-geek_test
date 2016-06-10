<?php
/**
 * Вспомогательный класс, уведомляющий о скачивании книги.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2016
 * @package application.modules.library.includes
 */
class DownloadMailNotifier extends Model
{
	/**
	 * Уведомить по почте, о скачивании файла.
	 */
	public static function notify($file, $book)
	{
		$file['name'] = iconv('windows-1251', 'utf-8', $file['name']);
		$data = array(
			'subject' => 'Скачана книга: '.$book['title'],
			'text'    => 'Скачана книга: '.$book['title'].'. Файл: '.$file['name'],
			'emails'  => array('job@code-geek.ru'),
			//'emails'  => array('sidorovich21101986@mail.ru'),
		);
		
		$model = new MailForm;
		$model->setAttr($data);
		( ! $model->hasErrors() AND $model->validate() AND $model->send());
		
		return $model;
	}
	
	public static function getBook($file_id)
	{
		$file = BooksFiles::db()->createCommand()->select('book_id')->from(BooksFiles::table())
			->where('id = :id', array(':id'=>(int)$file_id))
			->queryRow();
		$book_id = $file ? $file['book_id'] : 0;
		return Books::getArrayById($book_id);
	}
}
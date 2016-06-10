<?php
/**
 * Модель BooksFiles.
 * Пример использования: <pre>($model->setAttr($_POST['BooksFiles']) AND $model->save())</pre>
 * Более короткая запись: <pre>($model->setAttrAndSave($_POST['BooksFiles']);</pre>
 * 
 * Объект подключения: Yii->app()->db. Имеются следующие поля в таблице "books_files":
 *     id       pk                             
 *     book_id  integer              NOT NULL  
 *     name     nvarchar(255)            NULL  
 *     file     varchar(MAX)             NULL  
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2016
 * @package application.models.BooksFiles
 */
class BooksFiles extends Model
{
	// Подключение к базе данных
	public static function db() { return Yii::app()->db; }
	// Имя таблицы
	public static function table() { return 'books_files'; }
	// Имя колонки таблицы с первичным ключом
	public static function getPkColumnName() { return 'id'; }
    // Класс модели
    public static function model($className = __CLASS__) { return parent::model($className); }


	/**
	 * @return array Правила валидации.
	 */
	public function rules()
	{
		return array(
			// array('note', 'application.extensions.htmlpurifier.XSSFilter'), // Разрешает только безопасные теги и html-сущности (защита от XSS-атак).
			array('name', 'filter', 'filter'=>array($this, 'utf_htmlspecialchars')), // Полностью экранирует теги и html-сущности (защита от XSS-атак).
			array('book_id', 'required'),
			array('book_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			//array('file', 'length', 'max'=>2147483647),
			//array('id, book_id, name, file', 'safe', 'on'=>'search'),
		);
	}


	/**
	 * @return array Подписи полей (имя_поля => подпись).
	 */
	public function attributeLabels()
	{
		return array(
			'id'      => Yii::t('app', 'ID'),
			'book_id' => Yii::t('app', 'Book'),
			'name'    => Yii::t('app', 'Name'),
			'file'    => Yii::t('app', 'File'),
		);
	}


	/**
	 * Расширения, являющиеся файлом изображения.
	 */
	public static $is_image = array(
		'gif' => 'gif',
		'jpeg' => 'jpeg',
		'jpg' => 'jpg',
		'png' => 'png',
		'swf' => 'swf',
		'psd' => 'psd',
		'bmp' => 'bmp',
		'tiff' => 'tiff',
		'jpc' => 'jpc',
		'jp2' => 'jp2',
		'jpx' => 'jpx',
		'jb2' => 'jb2',
		'swc' => 'swc',
		'iff' => 'iff',
		'wbmp' => 'wbmp',
		'xbm' => 'xbm',
		'ico' => 'ico',
	);


	/**
	 * @param int $id ID загруженного файла.
	 * @return array Возвращает содержимое файла.
	 */
	public static function getFile($id)
	{
		$file = self::getArrayById($id);
		if ( ! $file)
			throw new CHttpException('404', 'Файл не найден!');
		$file['name'] = iconv('utf-8', 'windows-1251', $file['name']);
		$file['file'] = pack("H*", $file['file']);
		return $file;
	}


	public static function getFileNames($ids)
	{
		$data = self::db()->createCommand()->select('id, book_id, name')->from(self::table())
			->where(SearchHelper::inCondition($ids, 'book_id'))->queryAll();
		$result = array();
		foreach($data as $value)
			$result[$value['book_id']][$value['id']] = $value;
		return $result;
	}


	/**
	 * @return CDbCriteria Критерии поиска.
	 */
    public function getCriteria($partialMatch=true)
    {
		$criteria=new CDbCriteria;

		// Поиск, если задан список значений ID
		// SearchHelper::addConditionByListId(array('ids'), array('id'), $this, $criteria);

		$criteria->compare(self::table().'.id',(string)$this->id);
		$criteria->compare(self::table().'.book_id',(string)$this->book_id);
		$criteria->compare(self::table().'.name',(string)$this->name,true);
		//$criteria->compare(self::table().'.file',(string)$this->file,true);
		
		return $criteria;
	}


	/**
	 * Создает таблицу в базе данных для данной модели (это удобно при использовании инсталлятора).
	 */
	public static function createTable() {
		try {
			self::db()->createCommand()->select('*')->from(self::table())->limit(1)->query()->readAll();
			return false;
		} catch (CDbException $e) {
			
		}
		$is_sqlite = preg_match('/^sqlite\:[\s\S]+/', self::db()->connectionString);
		// Имя таблицы: [books_files]. Имеются следующие поля:
		self::db()->createCommand()->createTable(self::table(), array(
			'id'      => 'pk'                           , // 
			'book_id' => 'integer              NOT NULL', // 
			'name'    => 'nvarchar(255)            NULL', // 
			'file'    => 'varchar('.($is_sqlite ? '2147483647' : 'MAX').')  NOT NULL', // Содержимое файла
		));
		return true;
	}
	/**
	 * Удаляет таблицу в базе данных для этой модели (тоже необходимо для инсталлятора).
	 */
	public static function dropTable() {
		try {
			self::db()->createCommand()->select('*')->from(self::table())->limit(1)->query()->readAll();
			self::db()->createCommand()->dropTable(self::table());
			return true;
		} catch (CException $e) {
			return false;
		}
	}
}
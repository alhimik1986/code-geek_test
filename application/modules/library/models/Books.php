<?php
/**
 * Модель Books.
 * Пример использования: <pre>($model->setAttr($_POST['Books']) AND $model->save())</pre>
 * Более короткая запись: <pre>($model->setAttrAndSave($_POST['Books']);</pre>
 * 
 * Объект подключения: Yii->app()->db. Имеются следующие поля в таблице "books":
 *     id               pk                             
 *     title            nvarchar(255)            NULL  
 *     removed          boolean              NOT NULL  
 *     category_id      integer              NOT NULL  
 *     sub_category_id  integer              NOT NULL  
 *     description      text                     NULL  
 *     photo            varchar(MAX)             NULL
 *     all_text         text                     NULL  
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2016
 * @package application.models.Books
 */
class Books extends Model
{
	// Подключение к базе данных
	public static function db() { return Yii::app()->db; }
	// Имя таблицы
	public static function table() { return 'books'; }
	// Имя колонки таблицы с первичным ключом
	public static function getPkColumnName() { return 'id'; }
    // Класс модели
    public static function model($className = __CLASS__) { return parent::model($className); }
	
	// Значения по умолчанию
	public $removed=0;
	public $validate;
	public $files;
	
	// Дополнительые поля для поиска
	public $ids;
	public $category_ids;
	public $sub_category_ids;


	/**
	 * @return array Правила валидации.
	 */
	public function rules()
	{
		return array(
			array('description', 'application.extensions.htmlpurifier.XSSFilter'), // Разрешает только безопасные теги и html-сущности (защита от XSS-атак).
			array('title', 'filter', 'filter'=>array($this, 'utf_htmlspecialchars')), // Полностью экранирует теги и html-сущности (защита от XSS-атак).
			array('title, description, removed, category_id, sub_category_id', 'required'),
			array('category_id, sub_category_id', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			array('removed', 'in', 'range'=>array(0,1), 'allowEmpty'=>false, 'message'=>'"{attribute}" должен иметь значение 0 или 1'),
			array('description, all_text', 'length', 'max'=>10000000),
			array('files', 'safe'),
			array('ids, category_ids, sub_category_ids', 'safe', 'on'=>'search'),
		);
	}


	/**
	 * @return array Подписи полей (имя_поля => подпись).
	 */
	public function attributeLabels()
	{
		return array(
			'id'              => 'ID',
			'title'           => 'Название',
			'removed'         => 'Удалить',
			'category_id'     => 'Категория',
			'sub_category_id' => 'Подкатегория',
			'description'     => 'Описание',
			'photo'           => 'Фото обложки',
			'files'           => 'Файлы',
		);
	}


	/**
	 * @Override
	 * Дополнительные правила валидации после присвоения аттрибутов.
	 */
	public function afterSetAttr($data, $allowableKeys)
	{
		// Категорию проставляю автоматически
		$subCategory = SubCategories::getArrayById($this->sub_category_id);
		$this->category_id = $subCategory ? $subCategory['category_id'] : 0;
		// Извлекаю фото обложки
		$photo = Model::extractFile($_FILES, get_class(), 'photo');
		if ($photo)
			$this->photo = bin2hex($photo['content']);
		//if ($photo) die($this->photo);
		// Извлекаю файлы
		$this->files = Model::extractFiles($_FILES, get_class(), 'files');
		// Обновление поля для поиска по всем параметрам
		$this->all_text = $this->getAllText();
	}
	public function getAllText()
	{
 		$all = array('id', 'title', 'description');
		$text = '';
		foreach($all as $value) {
			$text .= ($text AND $this->$value) ? ' ' : '';
			$text .= trim($this->$value);
		}
		
		$text .= ArrayHelper::getValue1(Categories::getArrayById($this->category_id), 'name', '');
		$text .= ArrayHelper::getValue1(SubCategories::getArrayById($this->sub_category_id), 'name', '');
		
		return $text;
	}


	/**
	 * После сохранения.
	 */
	public function afterSave()
	{
		// Сохраняю прикрепленные файлы
		foreach($this->files as $file) {
			$model = new BooksFiles;
			$model->book_id = $this->id;
			$model->name = $file['name'];
			$model->file = bin2hex(file_get_contents($file['path']));
			
			if ( ! $model->validate() OR ! $model->save()) {
				$this->addError('file', 'Невозможно прикрепить файл.');
				return false;
			}
		}
		
		// Обновление поля для поиска по всем параметрам
		if ($this->isNewRecord AND ! $this->all_text)
			$this->updateByPk($this->id, array('all_text'=>$this->getAllText()));
	}


	/**
	 * После удаления.
	 */
	public function afterDelete()
	{
		BooksFiles::db()->createCommand()->delete(BooksFiles::table(), 'book_id='.$this->id);
	}


	/**
	 * @param int $id ID загруженного файла.
	 * @return array Возвращает содержимое файла.
	 */
	public static function getFile($id)
	{
		$file = self::getArrayById($id);
		if ( ! $file)
			throw new CHttpException('404', 'Файл не найден!');
		$file = pack("H*", $file['photo']);
		return $file;
	}


	/**
	 * @return CDbCriteria Критерии поиска.
	 */
    public function getCriteria($partialMatch=true)
    {
		$criteria=new CDbCriteria;

		// Поиск, если задан список значений ID
		SearchHelper::addConditionByListId(array('ids', 'category_ids', 'sub_category_ids'), array('id', 'category_id', 'sub_category_id'), $this, $criteria);

		$criteria->compare(self::table().'.id',(string)$this->id);
		$criteria->compare(self::table().'.title',(string)$this->title,true);
		$criteria->compare(self::table().'.removed',(string)$this->removed);
		$criteria->compare(self::table().'.category_id',(string)$this->category_id);
		$criteria->compare(self::table().'.sub_category_id',(string)$this->sub_category_id);
		$criteria->compare(self::table().'.description',(string)$this->description,true);
		$criteria->compare(self::table().'.all_text',(string)$this->all_text,true);
		
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
		// Имя таблицы: [books]. Имеются следующие поля:
		self::db()->createCommand()->createTable(self::table(), array(
			'id'              => 'pk'                           , // 
			'title'           => 'nvarchar(255)            NULL', // 
			'removed'         => 'boolean              NOT NULL', // 
			'category_id'     => 'integer              NOT NULL', // 
			'sub_category_id' => 'integer              NOT NULL', // 
			'description'     => 'text                     NULL', // 
			'photo'           => 'varchar('.($is_sqlite ? '2147483647' : 'MAX').')  NOT NULL',
			'all_text'        => 'text                     NULL', // 
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
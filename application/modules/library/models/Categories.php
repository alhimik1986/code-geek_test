<?php
/**
 * Модель Categories.
 * Пример использования: <pre>($model->setAttr($_POST['Categories']) AND $model->save())</pre>
 * Более короткая запись: <pre>($model->setAttrAndSave($_POST['Categories']);</pre>
 * 
 * Объект подключения: Yii->app()->db. Имеются следующие поля в таблице "categories":
 *     id       pk                       
 *     name     nvarchar(255)      NULL  
 *     removed  boolean        NOT NULL  
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2016
 * @package application.models.Categories
 */
class Categories extends Model
{
	// Подключение к базе данных
	public static function db() { return Yii::app()->db; }
	// Имя таблицы
	public static function table() { return 'categories'; }
	// Имя колонки таблицы с первичным ключом
	public static function getPkColumnName() { return 'id'; }
    // Класс модели
    public static function model($className = __CLASS__) { return parent::model($className); }

	// Значения по умолчанию
	public $removed=0;


	/**
	 * @return array Правила валидации.
	 */
	public function rules()
	{
		return array(
			// array('note', 'application.extensions.htmlpurifier.XSSFilter'), // Разрешает только безопасные теги и html-сущности (защита от XSS-атак).
			array('name', 'filter', 'filter'=>array($this, 'utf_htmlspecialchars')), // Полностью экранирует теги и html-сущности (защита от XSS-атак).
			array('name, removed', 'required'),
			array('name', 'length', 'max'=>255),
			array('id, name, removed', 'safe', 'on'=>'search'),
		);
	}


	/**
	 * @return array Подписи полей (имя_поля => подпись).
	 */
	public function attributeLabels()
	{
		return array(
			'id'      => 'ID',
			'name'    => 'Название',
			'removed' => 'Удалить',
		);
	}


	/**
	 * @return array Список специальностей для выпадающего списка.
	 */
	public static function dropDownList($defaultValue=array())
	{
		$result = $defaultValue;
		$pk_name = self::getPkColumnName();
		$data = self::getArrayByArray(array('removed'=>'0'));
		foreach($data as $value)
			$result[$value[$pk_name]] = $value['name'];
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
		$criteria->compare(self::table().'.name',(string)$this->name,true);
		$criteria->compare(self::table().'.removed',(string)$this->removed);
		
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
		// Имя таблицы: [categories]. Имеются следующие поля:
		self::db()->createCommand()->createTable(self::table(), array(
			'id'      => 'pk'                     , // 
			'name'    => 'nvarchar(255)      NULL', // 
			'removed' => 'boolean        NOT NULL', // 
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
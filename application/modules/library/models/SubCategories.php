<?php
/**
 * Модель SubCategories.
 * Пример использования: <pre>($model->setAttr($_POST['SubCategories']) AND $model->save())</pre>
 * Более короткая запись: <pre>($model->setAttrAndSave($_POST['SubCategories']);</pre>
 * 
 * Объект подключения: Yii->app()->db. Имеются следующие поля в таблице "sub_categories":
 *     id           pk                       
 *     name         nvarchar(255)      NULL  
 *     removed      boolean        NOT NULL  
 *     category_id  integer        NOT NULL  
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2016
 * @package application.models.SubCategories
 */
class SubCategories extends Model
{
	// Подключение к базе данных
	public static function db() { return Yii::app()->db; }
	// Имя таблицы
	public static function table() { return 'sub_categories'; }
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
			array('name, removed, category_id', 'required'),
			array('category_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
		);
	}


	/**
	 * @return array Подписи полей (имя_поля => подпись).
	 */
	public function attributeLabels()
	{
		return array(
			'id'          => 'ID',
			'name'        => 'Название',
			'removed'     => 'Удалить',
			'category_id' => 'Категория',
		);
	}


	/**
	 * @return array Список специальностей для выпадающего списка.
	 */
	public static function dropDownList($defaultValue=array())
	{
		$data = self::getArrayByArray(array('removed'=>'0'));
		$subCategories = array();
		foreach($data as $value)
			$subCategories[$value['category_id']][$value['id']] = $value;
		
		$categories = Categories::getArrayByArray(array('removed'=>'0'));
	
		$result = $defaultValue;
		foreach($data as $value) {
			if (isset($categories[$value['category_id']])) {
				$name = $categories[$value['category_id']]['name'];
				$result[$name][$value['id']] = $value['name'];
			} else {
				$result[$value['id']] = $value['name'];
			}
		}
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
		$criteria->compare(self::table().'.category_id',(string)$this->category_id);
		
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
		// Имя таблицы: [sub_categories]. Имеются следующие поля:
		self::db()->createCommand()->createTable(self::table(), array(
			'id'          => 'pk'                     , // 
			'name'        => 'nvarchar(255)      NULL', // 
			'removed'     => 'boolean        NOT NULL', // 
			'category_id' => 'integer        NOT NULL', // 
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
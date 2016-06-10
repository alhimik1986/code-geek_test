<?php

/**
 * 
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2016
 * @package application.helpers.components
 */
class Model extends CActiveRecord
{
	protected $oldAttributes; // Старые значения полей.
	protected $data;          // Данные, принятые с формы
	public function getData() { return $this->data; }
	public function getOldAttributes() { return $this->oldAttributes; }

	// Объект для подключения к базе
	public function getDbConnection() { return static::db(); }
	// Имя таблицы данной модели
	public function tableName() { return static::table(); }
	// Имя класса
	public static function className() { return get_called_class(); }
	// Связи таблицы
	public function relations() { return array(); }
	// Имя колонки таблицы с первичным ключом
	public static function getPkColumnName() { return 'id'; }
	// Длительность кэширования в секундах. Если установить false или '', то кэширование производиться не будет.
	public static function cacheDuration() { return false; }


	/**
	 * Присваивает аттрибуты через эту функцию, чтобы дейстовали нужные правила валидации.
	 */
	public function setAttr($data, $allowableKeys=null)
	{
		// Сохраняю старые значения при создании модели (необходимо для валидации).
		$this->oldAttributes = $this->attributes;
		// Сорхраняю данные, принятые с формы
		$this->data = $data;
		// Фильтрую данные по разрешенным ключам
		$data = static::filterData($data, $allowableKeys);
		// Обработка входных данных перед присвоением аттрибутов
		$data = $this->beforeSetAttr($data, $allowableKeys);
		
		$this->attributes = $data;
		
		// Обработка данных после присвоения аттрибутов
		$this->afterSetAttr($data, $allowableKeys);
		
		return ! $this->hasErrors();
	}


	/**
	 * @Override in Model.
	 * Этот метод, обычно перезаписывается наследуемым классом модели, чтобы обработать входные данные,
	 * например, чтобы исключить данные, которые нельзя менять.
	 */
	public function beforeSetAttr($data, $allowableKeys)
	{
		return $data;
	}


	/**
	 * @Override in Model.
	 * Этот метод, обычно перезаписывается наследуемым классом модели, чтобы обработать входные данные,
	 * например, чтобы изменить в полях формат даты-времени.
	 */
	public function afterSetAttr($data, $allowableKeys)
	{
		
	}


	/**
	 * Присваивает аттрибуты, проводит валидацию и сохраняет результат.
	 * Нужна для предотвращения повторяющихся строк.
	 */
	public function setAttrAndSave($data, $allowableKeys=null)
	{
		$this->setAttr($data, $allowableKeys);
		( ! $this->hasErrors() AND $this->validate() AND $this->save($runValidation=false));
	}


	/**
	 * 
	 */
	public function loadAndSave($data, $allowableKeys=null)
	{
		$className = static::className();
		if ( ! isset($data[$className])) {
			$this->addError(static::getPkColumnName(), 'Данные не приняты.');
			return false;
		}
		return $this->setAttrAndSave($data[$className], $allowableKeys);
	}


	public static function getModel($id)
	{
		$id = preg_replace('/[^0-9]/', '', (string)$id);
		$model = static::model()->findByPk($id);
		
		if ($model === NULL) {
			throw new CHttpException('404', 'Запись с id='.$id.' не найдена!');
		}
		
		return $model;
	}


	/**
	 * @Override in Model.
	 * Обработка полученных результатов.
	 * @param array $data Данные, полученные в результате sql-запроса.
	 * @param array $pkName Имя поля таблицы с первичным ключом.
	 * @return array Обработанный результат.
	 */
	public static function handleResults($data, $pkName=null)
	{
		$results = array();
		$pkName = ($pkName === null) ? static::getPkColumnName() : $pkName;
		foreach($data as $key=>$value)
			$results[$value[$pkName]] = $value;
		return $results;
	}


	/**
	 * @Override in Model.
	 */
	public static function getDataByCondition($condition, $pkName, $handleResults, $order)
	{
		$pkName = ($pkName === null) ? static::getPkColumnName() : $pkName;
		$results = static::db()->createCommand()->select(static::table().'.*')->from(static::table())->where($condition['condition'], $condition['args'])->order($order)->queryAll();
		if ($handleResults)
			$results = static::handleResults($results, $pkName);
		return $results;
	}


	public static function getArrayById($id, $pkName=null, $handleResults=true, $order='')
	{
		if (static::cacheDuration() !== '' AND static::cacheDuration() !== false) {$cacheId = get_called_class().__FUNCTION__.var_export(func_get_args(),true).$_SERVER['PHP_SELF']; $cache = Yii::app()->cache[$cacheId]; if ($cache !== false) return $cache; }
		
		$id = preg_replace('/[^0-9]/', '', (string)$id);
		$condition = array('condition' => static::getPkColumnName().'=:pk', 'args'=>array(':pk'=>$id));
		$results = static::getDataByCondition($condition, $pkName, $handleResults, $order);
		$results = reset($results);
		
		if (static::cacheDuration() !== '' AND static::cacheDuration() !== false) { Yii::app()->cache->set($cacheId, $results, static::cacheDuration()); }
		return $results;
	}


	public static function getArrayByIds($ids, $pkName=null, $handleResults=true, $order='')
	{
		if (static::cacheDuration() !== '' AND static::cacheDuration() !== false) {$cacheId = get_called_class().__FUNCTION__.var_export(func_get_args(),true).$_SERVER['PHP_SELF']; $cache = Yii::app()->cache[$cacheId]; if ($cache !== false) return $cache; }

		$condition = array('condition' => SearchHelper::listCondition($ids, static::getPkColumnName()), 'args'=>array());
		$results = static::getDataByCondition($condition, $pkName, $handleResults, $order);
		
		if (static::cacheDuration() !== '' AND static::cacheDuration() !== false) { Yii::app()->cache->set($cacheId, $results, static::cacheDuration()); }
		return $results;
	}


	public static function getArrayByListIds($ids, $columnName, $pkName=null, $handleResults=true, $order='')
	{
		if (static::cacheDuration() !== '' AND static::cacheDuration() !== false) {$cacheId = get_called_class().__FUNCTION__.var_export(func_get_args(),true).$_SERVER['PHP_SELF']; $cache = Yii::app()->cache[$cacheId]; if ($cache !== false) return $cache; }

		$condition = array('condition' => SearchHelper::listCondition($ids, $columnName), 'args'=>array());
		$results = static::getDataByCondition($condition, $pkName, $handleResults, $order);
		
		if (static::cacheDuration() !== '' AND static::cacheDuration() !== false) { Yii::app()->cache->set($cacheId, $results, static::cacheDuration()); }
		return $results;
	}


	public static function getArrayByArray($arrayCondition, $pkName=null, $handleResults=true, $order='')
	{
		if (static::cacheDuration() !== '' AND static::cacheDuration() !== false) {$cacheId = get_called_class().__FUNCTION__.var_export(func_get_args(),true).$_SERVER['PHP_SELF']; $cache = Yii::app()->cache[$cacheId]; if ($cache !== false) return $cache; }
		
		$condition = SearchHelper::arrayCondition($arrayCondition);
		$results = static::getDataByCondition($condition, $pkName, $handleResults, $order);
		
		if (static::cacheDuration() !== '' AND static::cacheDuration() !== false) { Yii::app()->cache->set($cacheId, $results, static::cacheDuration()); }
		return $results;
	}
	public static function getArrayByArray2($arrayCondition, $pkName=null, $handleResults=true, $order='')
	{
		if (static::cacheDuration() !== '' AND static::cacheDuration() !== false) {$cacheId = get_called_class().__FUNCTION__.var_export(func_get_args(),true).$_SERVER['PHP_SELF']; $cache = Yii::app()->cache[$cacheId]; if ($cache !== false) return $cache; }
		
		$condition = SearchHelper::arrayCondition2($arrayCondition);
		$results = static::getDataByCondition($condition, $pkName, $handleResults, $order);
		
		if (static::cacheDuration() !== '' AND static::cacheDuration() !== false) { Yii::app()->cache->set($cacheId, $results, static::cacheDuration()); }
		return $results;
	}


	// Перед сохранением (очищаю кэш).
	public function afterSave()
	{
		if (static::cacheDuration() !== '' AND static::cacheDuration() !== false)
			Yii::app()->cache->flush();
		return parent::afterSave();
	}
	// Перед удалением (очищаю кэш).
	public function afterDelete()
	{
		if (static::cacheDuration() !== '' AND static::cacheDuration() !== false)
			Yii::app()->cache->flush();
		return parent::afterDelete();
	}


	/**
	 * Фильтрует данные $data по разрешенным ключам. Для фильтрации посторонних ключей из $_POST.
	 * @param array $data Входные данные.
	 * @param array $allowableKeys Разрешенные ключи.
	 * @return array Входные данные (массив) с ключами, указанныеми в $allowableKeys.
	 */
	public static function filterData($data, $allowableKeys=null)
	{
		if ($allowableKeys !== null) {
			$allowableData = array();
			foreach($allowableKeys as $key) {
				if (isset($data[$key]))
					$allowableData[$key] = $data[$key];
			}
			$data = $allowableData;
		}
		return $data;
	}


	/**
	 * @return array Извлеченные файлы из глобальной переменной $_FILES.
	 * @param array $_files Содержимое глобальной переменной $_FILES.
	 * @param string $className Название класса модели, в которую загружался файл.
	 * @param string $attribute Название поля, в которое загружался файл.
	 */
	public static function extractFiles($_files, $className, $attribute)
	{
		$files = array();
		if (isset($_files[$className]['name'][$attribute][0]) AND $_files[$className]['name'][$attribute][0]) {
			foreach($_files[$className]['name'][$attribute] as $key=>$fileName) {
				if ( ! $fileName)
					continue;
				$fileInstance = CUploadedFile::getInstanceByName($className.'['.$attribute.']['.$key.']');
				$files[] = array(
					'name' => $fileInstance->getName(),
					'path' => $fileInstance->getTempName(),
					'content' => file_get_contents($fileInstance->getTempName()),
				);
			}
		} else {
			$files = array();
		}
		
		return $files;
	}


	/**
	 * @return array|mixed Извлеченный файл из глобальной переменной $_FILES.
	 * @param array $_files Содержимое глобальной переменной $_FILES.
	 * @param string $className Название класса модели, в которую загружался файл.
	 * @param string $attribute Название поля, в которое загружался файл.
	 */
	public static function extractFile($_files, $className, $attribute)
	{
		$file = array();
		if (isset($_files[$className]['name'][$attribute]) AND $_files[$className]['name'][$attribute]) {
			$fileName = $_files[$className]['name'][$attribute];
			if ( ! $fileName)
				return array();
			$fileInstance = CUploadedFile::getInstanceByName($className.'['.$attribute.']');
			$file = array(
				'name' => $fileInstance->getName(),
				'path' => $fileInstance->getTempName(),
				'content' => file_get_contents($fileInstance->getTempName()),
			);
		}
		
		return $file;
	}


	// Экранирует html-сущности (защита от XSS-атак).
	public function utf_htmlspecialchars($value) {
		return htmlspecialchars($value, ENT_COMPAT, 'UTF-8', false);
	}


	/**
	 * @Override in Model.
	 * Поиск в модели.
	 * @return CActiveDataProvider Искомые значения с заданными параметрами поиска.
	 */
    public function search()
    {
		return new CActiveDataProvider($this, array(
			'criteria' => $this->getCriteria(),
		));
	}


	/**
	 * @Override in Model.
	 * Поиск в базе данных.
	 * @param array $search Параметры поиска.
	 * @param string $order Порядок сортировки штатных единиц.
	 */
	public static function my_search($search, $order=null)
	{
		// Решаю проблему чувствительности символов к регистру в операторе LIKE для Sqlite.
		SearchHelper::sqliteFixCaseSensitiveInLikeOperator(static::db());
		
		// Прочие параметры поиска
		$defaultLimit = Yii::app()->settings->param['pager']['default']['resultsPerPageDefault'];
		$limit  = ( ! empty($search['limit']) AND (int)$search['limit']) ? (int)$search['limit']             : $defaultLimit;
		$offset = ( ! empty($search['page'])  AND (int)$search['page'])  ? ((int)$search['page'] - 1)*$limit : 0;
		//$show_more = isset($search['show_more']) ? $search['show_more'] : false;
		$order = ($order===null) ? static::getOrder($search) : $order;
		
		$query = static::getQuery($search);
		
		$results = static::db()->createCommand()->select('*')->from(static::table())
			->where($query->getWhere(), $query->params)
			->limit($limit, $offset)
			->order($order)
			->queryAll();
		$results = static::handleResults($results);
		
		$count = static::db()->createCommand()->select('count('.static::table().'.id) as count')
			->from(static::table())->where($query->getWhere(), $query->params)->queryAll();
		
		return array(
			'results'=> $results,
			'count'  => $count ? $count[0]['count'] : 0,
		);
	}


	/**
	 * @Override in Model.
	 * @return CDbCommand Параметры запроса.
	 * @param mixed $search Параметры поиска, принятые с формы.
	 */
	protected static function getQuery($search)
	{
		$className = static::className();
		$query = static::db()->createCommand()->select('*')->from(static::table());
		
		// Параметры поиска соискателей
		$_search = isset($search[$className]) ? $search[$className] : array();
		
		// Поиск по частичному совпадению (флажок)
		$partial_match = (isset($search['partial_match'])) ? (bool)$search['partial_match'] : true;
		
		// Критерии поиска
		$model = new $className('search');
		$model->unsetAttributes();
		$model->attributes = $_search;
		$criteria = $model->getCriteria($partial_match);
		$query->where($criteria->condition, $criteria->params);
		
		if ($model->hasErrors()) {
			if (defined('YII_DEBUG') AND YII_DEBUG) {
				$text = '';
				foreach($model->getErrors() as $attr=>$errors)
					foreach($errors as $error)
						$text .= '"'.$model->getAttributeLabel($attr).'" - '.$error.'<br>';
				throw new CHttpException('404', $text);
			} else {
				$query = static::db()->createCommand()->select('*')->from(static::table())->where('0=1');
			}
		}
		
		return $query;
	}


	/**
	 * @Override in Model
	 * @param mixed $search Параметры поиска, принятые с формы.
	 * @return string Сортировка данных при поиске.
	 */
	public static function getOrder($search)
	{
		$order = '';
		
		if (isset($search['order'])) {
			$allowedColumns = static::getAllowedToOrderColumns();
			$appendAllowedColumns = static::appendAllowedToOrderColumns();
			foreach($appendAllowedColumns as $column)
				$allowedColumns[] = $column;
			$order = SearchHelper::getOrder($search['order'], $allowedColumns, array('asc', 'desc'));
		} else {
			$order = static::orderByDefault();
		}
		
		return $order;
	}
	// @Override in Model
	// @return array Список всех колонок, по которым только возможно сортировать вообще.
	public static function getAllowedToOrderColumns()
	{
		$result = array();
		
		$columns = static::db()->schema->getTable(static::table())->getColumnNames();
		$tbl_name = static::table();
		foreach($columns as $col_name)
			$result[] = $tbl_name.'.'.$col_name;
		
		return $result;
	}
	// @Override in Model
	// @return array Список колонок, разрешенных для сортировки, который нужно добавить к тому, что по умолчанию.
	public static function appendAllowedToOrderColumns()
	{
		return array();
	}
	// @Override in Model
	// @return string Сортировка по умолчанию (если параметр сортировки не задан).
	public static function orderByDefault()
	{
		return static::table().'.'.static::getPkColumnName().' desc';
	}
}
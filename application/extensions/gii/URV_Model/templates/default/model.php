<?php
/**
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 */
 ?>
<?php
	$db = $this->connectionId;
	$pk = Yii::app()->$db->schema->getTable($tableName)->primaryKey;
?>
<?php echo "<?php\n"; ?>
/**
 * Модель <?php echo $this->modelClass.".\n"; ?>
 * Пример использования: <pre>($model->setAttr($_POST['<?php echo $this->modelClass; ?>']) AND $model->save())</pre>
 * Более короткая запись: <pre>($model->setAttrAndSave($_POST['<?php echo $this->modelClass; ?>']);</pre>
 * 
 * Объект подключения: Yii->app()-><?php echo $connectionId; ?>. Имеются следующие поля в таблице "<?php echo $tableName; ?>":
<?php // Узнаю максимальную длину каждой колонки
	$maxLengthName = 0; $maxLengthType = 0; $maxLengthComment = 0; $dbType = array(); $minLengthComment = 0;
	$typesHasSize = array('nvarchar', 'varchar');
	foreach($columns as $key=>$column) {
		$dbType[$key] = $column->isPrimaryKey ? 'pk' : $column->dbType;
		$dbType[$key] .= in_array($dbType[$key], $typesHasSize) ? '('.$column->size.')' : '';
		if ($maxLengthName    < strlen($column->name))    $maxLengthName    = strlen($column->name);
		if ($maxLengthType    < strlen($dbType[$key]))    $maxLengthType    = strlen($dbType[$key]);
		if ($maxLengthComment < strlen($column->comment)) $maxLengthComment = strlen($column->comment);
	}
	$maxLengthComment = ($maxLengthComment > $minLengthComment) ? $maxLengthComment : $minLengthComment;
	$maxLength = $maxLengthName + $maxLengthType + 21 + $maxLengthComment; // Общая максимальная длина
?>
<?php foreach($columns as $key=>$column): ?>
<?php
	if ($column->isPrimaryKey) {
		$allowNull = '        ';
	} else if ($column->allowNull) {
		$allowNull = '    NULL';
	} else {
		$allowNull = 'NOT NULL';
	}
	$nameIndent = str_repeat(' ', $maxLengthName - strlen($column->name));
	$typeIndent = str_repeat(' ', $maxLengthType - strlen($dbType[$key]));
	$commentIndent = ($maxLengthComment > mb_strlen($column->comment)) ? str_repeat(' ', $maxLengthComment - mb_strlen($column->comment)) : '';
?>
<?php echo " *     $column->name$nameIndent  $dbType[$key]$typeIndent  $allowNull  $column->comment$commentIndent\n"; ?>
<?php endforeach; ?>
<?php if(!empty($relations)): ?>
 *
 * The followings are the available model relations:
<?php foreach($relations as $name=>$relation): ?>
 * @property <?php
	if (preg_match("~^array\(self::([^,]+), '([^']+)', '([^']+)'\)$~", $relation, $matches))
    {
        $relationType = $matches[1];
        $relationModel = $matches[2];

        switch($relationType){
            case 'HAS_ONE':
                echo $relationModel.' $'.$name."\n";
            break;
            case 'BELONGS_TO':
                echo $relationModel.' $'.$name."\n";
            break;
            case 'HAS_MANY':
                echo $relationModel.'[] $'.$name."\n";
            break;
            case 'MANY_MANY':
                echo $relationModel.'[] $'.$name."\n";
            break;
            default:
                echo 'mixed $'.$name."\n";
        }
	}
    ?>
<?php endforeach; ?>
<?php endif; ?>
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; <?php $now = new DateTime(); echo $now->format('Y')."\n"; ?>
 * @package <?php echo $this->modelPath; ?>.<?php echo $this->modelClass."\n"; ?>
 */
class <?php echo $modelClass; ?> extends Model
{
<?php if ($this->generateOverridableMethods): ?>
	// Длительность кэширования в секундах. Если установить false или '', то кэширование производиться не будет.
	//public static function cacheDuration() { return (property_exists(Yii::app(), 'settings') AND property_exists(Yii::app()->settings->param) AND ! empty(Yii::app()->settings->param['cache']['enabled'])) ? 300 : false; }
<?php endIf; ?>
	// Подключение к базе данных
	public static function db() { return Yii::app()-><?php echo $connectionId ?>; }
	// Имя таблицы
	public static function table() { return '<?php echo $tableName; ?>'; }
	// Имя колонки таблицы с первичным ключом
	public static function getPkColumnName() { return '<?php echo $pk; ?>'; }
    // Класс модели
    public static function model($className = __CLASS__) { return parent::model($className); }
<?php if ($relations): ?>
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
<?php foreach($relations as $name=>$relation): ?>
			<?php echo "'$name' => $relation,\n"; ?>
<?php endforeach; ?>
		);
	}
<?php elseif($this->generateOverridableMethods): ?>
	// Связи таблицы
	public function relations() { return array(); }
<?php endIf; ?>


	/**
	 * @return array Правила валидации.
	 */
	public function rules()
	{
		return array(
			// array('note', 'application.extensions.htmlpurifier.XSSFilter'), // Разрешает только безопасные теги и html-сущности (защита от XSS-атак).
			// array('name', 'filter', 'filter'=>array($this, 'utf_htmlspecialchars')), // Полностью экранирует теги и html-сущности (защита от XSS-атак).
<?php foreach($rules as $rule): ?>
			<?php echo $rule.",\n"; ?>
<?php endforeach; ?>
			array('<?php echo implode(', ', array_keys($columns)); ?>', 'safe', 'on'=>'search'),
		);
	}


	/**
	 * @return array Подписи полей (имя_поля => подпись).
	 */
	public function attributeLabels()
	{
		return array(
<?php
	$maxLengthName = 0;
	foreach($labels as $name=>$label) {
		if ($maxLengthName < strlen($name)) $maxLengthName = strlen($name);
	}
?>
<?php foreach($labels as $name=>$label): ?>
			<?php echo "'$name'".str_repeat(' ', $maxLengthName - strlen($name))." => Yii::t('app', '$label'),\n"; ?>
<?php endforeach; ?>
		);
	}
<?php if ($this->generateOverridableMethods): ?>

	/**
	 * @Override
	 * Обработка данных, полученных с формы перед присвоением аттрибутов.
	 */
	/*public function beforeSetAttr($data, $allowableKeys)
	{
		return $data;
	}*/


	/**
	 * @Override
	 * После присвоения аттрибутов.
	 */
	/*public function afterSetAttr($data, $allowableKeys)
	{
<?php foreach($columns as $column): ?>
<?php if ($column->dbType == 'date')     echo "		DateHelper::formatDateAttribute(\$this, '".$column->name."', Yii::t('app', 'Wrong date specified.'));\n"; ?>
<?php if ($column->dbType == 'datetime') echo "		DateHelper::formatDateAttribute(\$this, '".$column->name."', Yii::t('app', 'Wrong date specified.'));\n"; ?>
<?php endforeach; ?>
	}*/


	/**
	 * Перед удалением.
	 */
	/*public function beforeDelete()
	{
		if ( ! $this->removed) {
			$this->addError('id', Yii::t('app', 'You can not to delete the records that are not marked as removed.'));
			return false;
		}
		return parent::beforeDelete();
	}*/


	/**
	 * @Override in Model.
	 * Обработка полученных результатов (перезаписывает метод).
	 * @param array $data Данные, полученные в результате sql-запроса.
	 * @param array $pkName Имя поля таблицы с первичным ключом.
	 * @return array Обработанный результат.
	 */
	/*public static function handleResults($data, $pkName)
	{
		return parent::handleResults($data, $pkName);
	}*/
<?php endIf; ?>


	/**
	 * @return CDbCriteria Критерии поиска.
	 */
    public function getCriteria($partialMatch=true)
    {
		$criteria=new CDbCriteria;

		// Поиск, если задан список значений ID
		// SearchHelper::addConditionByListId(array('ids'), array('id'), $this, $criteria);

<?php
foreach($columns as $name=>$column)
{
	if($column->type==='string')
	{
		echo "\t\t\$criteria->compare(self::table().'.$name',(string)\$this->$name,true);\n";
	}
	else
	{
		echo "\t\t\$criteria->compare(self::table().'.$name',(string)\$this->$name);\n";
	}
}
?>
		
		return $criteria;
	}
<?php if ($this->generateOverridableMethods): ?>


	/**
	 * @Override
	 * Поиск в базе данных.
	 * @param array $search Параметры поиска.
	 */
	/*public static function my_search($search, $order=null)
	{
		// Решаю проблему чувствительности символов к регистру в операторе LIKE для Sqlite.
		SearchHelper::sqliteFixCaseSensitiveInLikeOperator(self::db());
		
		// Прочие параметры поиска
		$t = self::table();
		$limit  = ( ! empty($search['limit']) AND (int)$search['limit']) ? (int)$search['limit']             : 6;
		$offset = ( ! empty($search['page'])  AND (int)$search['page'])  ? ((int)$search['page'] - 1)*$limit : 0;
		$orderBySearch = ($order===null AND isset($search['order']) AND ! ArrayHelper::hasEmptyValues($search['order']));
		$order = ($order===null) ? static::getOrder($search) : $order;
		
		$query = self::getQuery($search);
		
		$results = self::db()->createCommand()->select('*')->from(self::table())
			->where($query->getWhere(), $query->params)
			->limit($limit, $offset)
			->order($order)
			->queryAll();
		$results = self::handleResults($results);
		
		$count = self::db()->createCommand()->select('count('.self::table().'.<?php echo $pk; ?>) as count')
			->from(self::table())->where($query->getWhere(), $query->params)->queryAll();
		
		return array(
			'results'=> $results,
			'count'  => $count ? $count[0]['count'] : 0,
		);
	}*/
	
	
	/**
	 * @Override in Model.
	 * @return CDbCommand Параметры запроса.
	 * @param mixed $search Параметры поиска, принятые с формы.
	 */
	/*protected static function getQuery($search)
	{
		$query = self::db()->createCommand()->select('*')->from(self::table());
		
		// Параметры поиска соискателей
		$_search = isset($search[self::className()]) ? $search[self::className()] : array();
		
		// Поиск по частичному совпадению (флажок)
		$partial_match = (isset($_search['partial_match'])) ? (bool)$_search['partial_match'] : true;
		
		// Критерии поиска
		$model = new <?php echo $this->modelClass; ?>('search');
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
	}*/
<?php endIf; ?>


	/**
	 * Создает таблицу в базе данных для данной модели (это удобно при использовании инсталлятора).
	 */
	public static function createTable() {
		try {
			self::db()->createCommand()->select('*')->from(self::table())->limit(1)->query()->readAll();
			return false;
		} catch (CDbException $e) {
			
		}
		// Имя таблицы: [<?php echo $tableName; ?>]. Имеются следующие поля:
		self::db()->createCommand()->createTable(self::table(), array(
<?php
	$maxLengthName = 0; $maxLengthType = 0; $dbType = array();
	foreach($columns as $key=>$column) {
		$dbType[$key] = $column->isPrimaryKey ? 'pk' : $column->dbType;
		$dbType[$key] .= in_array($dbType[$key], $typesHasSize) ? '('.$column->size.')' : '';
		if ($maxLengthName < strlen($column->name)) $maxLengthName = strlen($column->name);
		if ($maxLengthType < strlen($dbType[$key])) $maxLengthType = strlen($dbType[$key]);
	}
?>
<?php foreach($columns as $key=>$column): ?>
			<?php
				$name = $column->name;
				$nameIndent = str_repeat(' ', $maxLengthName - strlen($column->name));
				//$type = strtoupper($dbType[$key]) . str_repeat(' ', $maxLengthType - strlen($dbType[$key]));
				$type = $dbType[$key] . str_repeat(' ', $maxLengthType - strlen($dbType[$key]));
				$pkIndent = '';
				
				if ($column->isPrimaryKey) {
					$allowNull = '';
					$pkIndent = str_repeat(' ', $maxLengthType - 2 + 10);
					$type = 'pk';
				} else if ($column->allowNull) {
					$allowNull = '      NULL';
				} else {
					$allowNull = '  NOT NULL';
				}
				
				$comment = $column->comment ? "$pkIndent -- $column->comment',\n" : "$pkIndent, // $column->comment\n";
				
				echo ($type == 'pk') ? "'$name'$nameIndent => '$type$allowNull'$comment" : "'$name'$nameIndent => '$type$allowNull'$comment";
			?>
<?php endforeach; ?>
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
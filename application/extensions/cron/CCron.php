<?php
/**
 * Чтобы создать cron-задачи, необходимо 
 * 
 * Пример использования:
	<pre>
	include_once('CCron.php');
	class Cron extends CCron
	{
		// Изменяю параметры по умолчанию (хочу начать запускать cron с 2014-01-01)
		public function init() {
			$this->start = '2014-01-01';
		}
		
		// Эта задача будет выполняться, т.к. метод начинается со слова "cron".
		// Например, задача для создания резервных копий с периодом 24 часа.
		public function cronBackup($period=1*24*3600)
		{
			BackupHelper::makeBackup();
			
			// Здесь, например, я ограничиваю создание бэкапов в связи с нехваткой свободного места на диске.
			if (BackupHelper::getFreeSpace() < 1000000000) {
				$this->cronData['cronBackup']['count'] = 0; // Обнуляю счетчкик запусков этого метода
				$this->cronData['cronBackup']['limit'] = 3; // Разрешаю только 3 последних запуска этого метода (на большее места не хватит!)
			}
			
			// А здесь я вызываю вспомогательный внутрениий метод.
			$this->additionalMethod();
		}
		
		// Вспомогательный внутренний метод. Сам по себе он не будет запущен, т.к. не начинается со слова "cron".
		public function additionalMethod()
		{
			// .. Вспомогательные операции
		}
	}
	
	Cron::run();
	</pre>
 */

class CCron
{
	public $dataFile = false; // Расположение файла данными о запуске cron. (false - файл cron.json будет создан в том же месте, где находится этот файл)
	
	/* Данные по умолчанию, если не заданы в параметрах методов (начинающихся на cron) 
	   Их можно изменить в любой части этого класса, причем они будут иметь больший приоритет,
	   чем в методах (начинающиеся на cron).
	   Для этого следует задать в $this->cronData, например: $this->cronData[$methodName]['start'] = '2014-01-02';
	   Это можно сделать прямо в методе (начинающийся на cron), 
	   чтобы изменить дату и период вызова этого же метода или любого другого.
	   Чтобы вернуть эти параметры по умолчанию, следует удалить файл cron.json
	*/
	public $start = '1900-01-01'; // Время начала, по которому отсчитывать период
	public $end   = '9999-01-01'; // Время конца действия cron
	public $period = 0;           // Период повторения в секундах (0 - без временного периода). Можно также задать в виде объекта класса DateInterval, указав в нем интервал.
	public $count_period = 0;     // Через сколько вызовов (метода CCron::run()) запускать cron (0 - запускать всегда, т.е. при каждом запуске CCron::run())
	public $limit = 0;            // Сколько раз разрешено выполнять cron (0 - сколько угодно)
	
	// Список данных cron для каждого cron-метода.
	public $cronData;
	
	
	/**
	 * Запускает функции, начинающиеся на cron, например,
	 * public static function cronBackup($start='2014-01-01', $period='3'){}
	 */
	public static function run()
	{
		$className = get_called_class();
		$class = new $className;
		$class->saveData($class->cronData); // Сохраняю данные о последних запущенных кронах
		return $class;
	}
	
	public function __construct()
	{
		$this->cronData = $this->loadData(); // Загружаю данные о последних запущенных кронах
		$this->init();   // Предварительный метод (например, чтобы изменить данные по умолчанию
		$this->_init();
	}
	
	// Метод, который можно переопределить в дочернем классе, чтобы, например, изменить параметры по умолчанию.
	public function init(){}
	
	private function _init()
	{
		$now = new DateTime(); $now = $now->format('d-m-Y H:i:s');
		
		$methods = get_class_methods($this);
		foreach($methods as $methodName) {
			if (strpos($methodName, 'cron') === 0)
				if ($this->itMeetConditions($methodName, $this->getParams($methodName))) {
					call_user_func_array(array($this, $methodName), array());
					$this->cronData[$methodName]['count'] = empty($this->cronData[$methodName]['count']) ? 1 : ++$this->cronData[$methodName]['count'];
					$this->cronData[$methodName]['last']  = $now;
				}
		}
		
		$this->cronData['count'] = empty($this->cronData['count']) ? 1 : ++$this->cronData['count'];
		$this->cronData['last']  = $now;
	}
	
	
	/**
	 * Возвращает аргументы вызваемых методов (начинающихся на cron), чтобы узнать условия их вызова.
	 */
	private function getParams($methodName)
	{
		$results = array();
		$r = new ReflectionMethod(get_called_class(), $methodName);
		$params = $r->getParameters();
		foreach ($params as $param) {
			//$param is an instance of ReflectionParameter
			$results[strtolower($param->getName())] = $param->getDefaultValue();
		}
		
		$cronData = $this->cronData;
		foreach(array('start', 'end', 'period', 'count_period', 'limit') as $key) {
			$results[$key] = isset($results[$key]) ? $results[$key] : $this->$key;
			$results[$key] = isset($cronData[$methodName][$key]) ? $cronData[$methodName][$key] : $results[$key];
		}
		
		return $results;
	}
	
	
	/**
	 * Если параметры, указанные в имени метода, удовлетворяют условиям запуска.
	 */
	private function itMeetConditions($methodName, $params)
	{
		$now         = new DateTime();
		$methodLast  = empty($this->cronData[$methodName]['last']) ? new DateTime() : new DateTime($this->cronData[$methodName]['last']);
		$count       = empty($this->cronData['count']) ? 1 : $this->cronData['count'];
		$methodCount = empty($this->cronData[$methodName]['count']) ? 1 : $this->cronData[$methodName]['count'];
		
		$start  = new DateTime($params['start']);
		$end    = new DateTime($params['end']);
		$period = ((int)($params['period'] == $params['period']) AND $params['period']) 
			? new DateInterval('PT'.$params['period'].'S') : $params['period'];
		$count_period  = $params['count_period'];
		$limit  = $params['limit'];
		
		if ($limit AND ($methodCount > $limit))
			return false;
		if (($now <= $start) OR ($now >= $end))
			return false;
		
		if ($period AND ($now->sub($period) >= $methodLast))
			return true;
		
		if ($count_period AND (($count % $count_period) == 0))
			return true;
		
		// Если никакой период не задан, то выполнять всегда
		if ( ! $count_period AND ! $period)
			return true;
		
		return false;
	}
	
	private function loadData(){return file_exists($this->getDataFile()) ? json_decode(file_get_contents($this->getDataFile()), true) : null;}
	private function saveData($data){file_put_contents($this->getDataFile(), json_encode($data));}
	private function getDataFile(){ return ($this->dataFile === false) ? realpath(dirname(__FILE__)).'/cron.json' : $this->dataFile;}

}
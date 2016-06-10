<?php

/**
 * 
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2016
 * @package application.helpers.components
 */
class DateHelper
{
	// Значения сроков действия если даты не указаны.
	public static function minDate($database='db') {return self::sqlDate($database, '1900-01-01');} // Минимальная дата 
	public static function maxDate($database='db') {return self::sqlDate($database, '9999-01-01');} // Максимальная дата


	/**
	 * Задает минимальную дату (дате начала) и максимальную дату (дате конца), если одна из дат пустая.
	 */
	public static function minMaxDateOnEmpty(&$from_date, &$to_date, $database='db')
	{
		if ($from_date OR $to_date) {
			$from_date = self::strtotime($from_date) ? self::sqlDate($database, $from_date) : self::minDate();
			$to_date   = self::strtotime($to_date)   ? self::sqlDate($database, $to_date)   : self::maxDate();
		}
	}


	/**
	 * Заменяет функцию strtotime(), только диапазон дат расширен вместо 1970-2038 г. на 0000-9999 г.
	 * Ей можно проверять валидность строки даты (не выбрасывает исключение).
	 * @param string $datetimeString Строка даты-времени.
	 * @return DateTime Объект полученного даты-времени (если дата задана неверно, то возвращает false).
	 */
	public static function strtotime($datetimeString)
	{
		if ( ! $datetimeString)
			return false;
		
		try {
			$date = new DateTime($datetimeString);
			// Если невалидно задать год: new DateTime('10000-05-20'), то исключение не выбрасывается, а датой будет 2000-01-01.
			// Таким образом исключаю этот недочет.
			if (($date->format('Y') == '2000') AND (date('Y', strtotime($datetimeString)) != '2000'))
				return false;
			return $date;
		} catch (Exception $e) {
			return false;
		}
	}


	/**
	 * Заменяет функцию date(), только диапазон дат расширен вместо 1970-2038 г. на 0000-9999 г.,
	 * а $dateTime - является объектом класса DateTime() или строкой даты-времени, которая потом преобразуется в объект класса DateTime().
	 * @param string $format Формат даты, в которую нужно преобразовать $dateTime.
	 * @param mixed $dateTime Дата-время, которую нужно преобразовать в нужный формат.
	 * @param DateTimeZone $dateTimeZone Временная зона (часовой пояс).
	 * @return string Преобразованная дата.
	 */
	public static function date($format, $dateTime=null, $dateTimeZone=null)
	{
		if (is_object($dateTime)) {
		
		} else if ($dateTime === null) {
			$dateTime = new DateTime('now', $dateTimeZone);
		} else if ( is_string($dateTime) OR (string)$dateTime == (int)$dateTime) {
			$dateTime = new DateTime($dateTime, $dateTimeZone);
		}
		
		if ($dateTime instanceOf DateTime)
			return $dateTime->format($format);
		else {
			var_dump($dateTime); die();}
	}


	/**
	 * Преобразует дату в нужный формат для заданного sql ($dbName).
	 * а $dateTime - является объектом класса DateTime() или строкой даты-времени, которая потом преобразуется в объект класса DateTime().
	 * @param string $dbName Имя подключения к базе данных.
	 * @param mixed $dateTime Дата-время, которую нужно преобразовать в нужный формат.
	 * @param DateTimeZone $dateTimeZone Временная зона (часовой пояс).
	 * @return string Дата в нужном формате для заданного подключения к базе данных.
	 */
	public static function sqlDate($dbName, $dateTime=null, $dateTimeZone=null)
	{
		return self::date(self::sqlDateFormat($dbName),$dateTime, $dateTimeZone);
	}
	public static function sqlDateTime($dbName, $dateTime=null, $dateTimeZone=null)
	{
		return self::sqlDate($dbName, $dateTime=null, $dateTimeZone).self::date(' H:i:s', $dateTime);
	}


	/**
	 * Возвращает нужный формат даты, т.к. разные sql опознают дату в разных форматах.
	 * @param string $database Имя подключения к базе данных.
	 * @return string формат даты для указанного имени подключения к базе данных.
	 */
	public static function sqlDateFormat($database)
	{
		//$s = trim(Yii::app()->settings->param[$database]['connectionString']);
		try {
			$s = trim(Yii::app()->$database->connectionString);
		} catch (CDbException $e) {
			$s = trim(Yii::app()->settings->param[$database]['connectionString']);
		}
		
		if (preg_match('/^sqlite\:[\s\S]+/', $s)) {
			return 'Y-m-d';
		} else if (preg_match('/^sqlsrv\:[\s\S]+/', $s)) {
			return 'Ymd';
		} else if (preg_match('/^firebird\:[\s\S]+/', $s)) {
			return 'd.m.Y';
		} else {
			return 'Y-m-d';
		}
	}


	/**
	 * Преобразовывает дату (строка) в нужный формат для sql.
	 * @param string $dateString Дата в виде строки.
	 * @param string $database Имя подключения к базе данных.
	 * @return string формат даты для указанного имени подключения к базе данных.
	 */
	public function strToSqlDate($dateString, $database)
	{
		$date = new DateTime($dateString);
		return $date->format(self::sqlDateFormat($database));
	}


	/**
	 * @return integer Разница между двумя датами в секундах.
	 * @param DateTime $from_date Дата-время начала интервала. Тип данных: DateTime или string.
	 * @param DateTime $to_date Дата-время конца интервала. Тип данных: DateTime или string.
	 */
	public static function diff(&$from_date, &$to_date)
	{
		$interval = $from_date->diff($to_date);
		return ($interval->days*86400 + $interval->h*3600 + $interval->i*60 + $interval->s)*($interval->invert ? -1 : 1);
	}


	/**
	 * @return integer Интервал (длительность) пересечения между двумя указанными интервалами в секундах.
	 * @param DateTime &$from_time1 Время начала первого интервала.
	 * @param DateTime &$to_time1 Время конца первого интервала.
	 * @param DateTime &$from_time2 Время начала второго интервала.
	 * @param DateTime &$to_time2 Время конца второго интервала.
	 */
	public static function diff_intervals(&$from_time1, &$to_time1, &$from_time2, &$to_time2)
	{
		if ($from_time1 > $to_time2 OR $from_time2 > $to_time1) {
			return 0;
		} else {
			$from_time = ($from_time1 > $from_time2) ? $from_time1 : $from_time2;
			$to_time   = ($to_time1 < $to_time2)     ? $to_time1   : $to_time2;
			return self::diff($from_time, $to_time);
		}
	}


	/**
	 * Переводит дату, заданную в аттрибуте в формат, подходящий для заданного типа базы данных.
	 * @param CActiveRecord &$model Модель, в которой конвертируется формат даты.
	 * @param string $attribute Название аттрибута, в котором необходимо сконвертировать дату.
	 * @param string $message Текст ошибки, который выводится, если не удастся опознать дату.
	 * @param boolean $allowNull Разрешить пустое значение вместо даты.
	 * @param string $database Название объекта подключения к базе данных.
	 */
	public static function formatDateAttribute(&$model, $attribute, $message, $allowNull=false, $database='db')
	{
		if ($allowNull AND ! $model->$attribute) {
			$model->$attribute = null; // для mssql, чтобы не проставлял 1900-01-01, если пустая строка
			return;
		}
		
		$date = self::strtotime($model->$attribute);
		if ($date) {
			$model->$attribute = self::sqlDate($database, $date).' '.$date->format('H:i:s');
		} else {
			$model->addError($attribute, $message);
		}
	}


	/**
	 * Возвращает то же, что и new DateTime(), только кэшируется строкой дата-время.
	 * Применять этот метод только когда строка даты-времени все время повторяется.
	 * @param string $datetimeString Строка даты-времени.
	 * @return DateTime Объект полученного даты-времени.
	 */
	public static function DateTime_cached($datetimeString)
	{
		$cache_id = 'DateTime_cached'.$datetimeString;
		if (isset(self::$cache[$cache_id]))
			return clone self::$cache[$cache_id];
		
		$result = new DateTime($datetimeString);
		
		self::$cache[$cache_id] = $result;
		return clone $result;
	}
	protected static $cache = array();


	/**
	 * Дата в нормальном виде: "число название_месяца_по-русски год".
	 * @param mixed $date Дата в виде строки или в виде объекта класса DateTime.
	 * @return string Дата в нормальном виде.
	 */
	/*public static function russianDate($date, $format=0)
	{
		if ( ! $date) return '';
		$date = is_string($date) ? new DateTime($date) : $date;
		$month = ($format == 0) ? self::$month2 : self::$month;
		return $date->format('d').' '.$month[$date->format('m')].' '.$date->format('Y').' г.';
	}
	public static $month = array(
		'01' => 'Январь', '02' => 'Февраль', '03' => 'Март', '04' => 'Апрель', '05' => 'Май', '06' => 'Июнь',
		'07' => 'Июль', '08' => 'Август', '09' => 'Сентябрь', '10' => 'Октябрь', '11' => 'Ноябрь', '12' => 'Декабрь',
	);
	public static $month2 = array(
		'01' => 'Января', '02' => 'Февраля', '03' => 'Марта', '04' => 'Апреля', '05' => 'Мая', '06' => 'Июня',
		'07' => 'Июля', '08' => 'Августа', '09' => 'Сентября', '10' => 'Октября', '11' => 'Ноября', '12' => 'Декабря',
	);*/
}
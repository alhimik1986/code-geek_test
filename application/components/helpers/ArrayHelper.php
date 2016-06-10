<?php

/**
 * 
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2016
 * @package application.helpers.components
 */
class ArrayHelper
{
	/**
	 * Извлекает значение из массива по заданному ключу, если оно не найдено, то выводит $defaultValue (полезно для избежания ошибок типа "undefined index").
	 * @param array $array Массив, из которого нужно извлечь значение.
	 * @param string $key Имя первого ключа, для извлечения значения из массива.
	 * @param string $defaultValue Значение, которое выводить в случае отсутствия значения в занных ключах.
	 * @return string Значение массива по заданных ключам.
	 * @example Пример:
	 * <pre>
	 * $array = array(1=>'бетон', 2=>'цемент');
	 * $value = ArrayHelper::getValue($array, 5, 'id='.5);
	 * </pre>
	 */
	public static function getValue1($array, $key, $defaultValue)
	{
		return isset($array[$key]) ? $array[$key] : $defaultValue;
	}


	/**
	 * Извлекает значение из массива по заданным ключам, если оно не найдено, то выводит $defaultValue (полезно для избежания ошибок типа "undefined index").
	 * @param array $array Массив, из которого нужно извлечь значение.
	 * @param string $key1 Имя первого ключа, для извлечения значения из массива.
	 * @param string $key2 Имя второго ключа, для извлечения значения из массива.
	 * @param string $defaultValue Значение, которое выводить в случае отсутствия значения в занных ключах.
	 * @return string Значение массива по заданных ключам.
	 * @example Пример:
	 * <pre>
	 * $array = array(1=>array('name'=>'бетон', 'value'=>'400'), 2=>array('name'=>'цемент', 'value'=>'600'));
	 * $value = ArrayHelper::getValue($array, 4, 'name', 'index='.'name');
	 * </pre>
	 */
	public static function getValue2($array, $key1, $key2, $defaultValue)
	{
		return isset($array[$key1][$key2]) ? $array[$key1][$key2] : $defaultValue;
	}


	/**
	 * Возрващает список значений (как правило, список ID) массива в заданном ключе.
	 * @param array $results Массив, в котором нужно искать список значений.
	 * @param string $key Ключ, в котором искать список значений.
	 * @return array Список значений в виде целых чисел.
	 * Пример: <pre>$data = array(array('id'=>3, 'name'=>'nik'), array('id'=>5, 'name'=>'vlad'));
	 * $ids = ArrayHelper::getListIdInKey($data, 'id');
	 * print_r($data);</pre>
	 * Результат: <pre> Array(
	 *     [3] => 3,
	 *     [5] => 5,
	 * ) </pre>
	 */
	public static function getListIdInKey($results, $key)
	{
		$ids = array();
		foreach($results as $result) {
			$id = $result[$key];
			//$id = (int)$id;
			$id = preg_replace('/[^0-9]/', '', (string)$id);
			$ids[$id] = $id;
		}
		return $ids;
	}
	public static function getListIdInKeyGUID($results, $key)
	{
		$ids = array();
		foreach($results as $result) {
			$id = $result[$key];
			if ( ! self::isGUID($id))
				continue;
			$ids[$id] = $id;
		}
		return $ids;
	}


	/**
	 * Проверяет корректность массива и убирает из него повторяющиеся значения.
	 * Бросает исключение, если данные не корректны (не массив).
	 * @return array Обработанный массив из неповторяющихся целых чисел (список ID).
	 * @param array $array Данные (список ID), которые необходимо проверить на массив и убрать 
	 * повторяющиеся значения.
	 * @param boolean $throwException Выбрасывать исключение, если данные не корректны.
	 * @param string $errorMessage Сообщение об ошибке, которое необходимо вывести, если данные не 
	 * являются массивом.
	 */
	public static function validateIds($array, $errorMessage, $throwException=true)
	{
		if ( ! is_array($array))
			if ($throwException)
				throw new CHttpException('404', $errorMessage);
			else
				return array();

		$result = array();
		foreach($array as $id) {
			//$id = (int)$id;
			$id = preg_replace('/[^0-9]/', '', (string)$id);
			$result[$id] = $id;
		}

		return $result;
	}


	/**
	 * @return boolean Является ли значение в формате GUID (глобального уникального ID)
	 * @param string $value Проверяемое значение.
	 */
	public static function isGUID($value)
	{
		return is_string($value) AND preg_match('/[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}/ui', $value);
	}


	/**
	 * Вставляет вместо ключа массива значение в указанном ключе (как правило, это ID в ключе массива для быстрого поиска).
	 * @param array $results Данные, которые необходимо "проиндексировать".
	 * @param string $key Ключ, по которому необходимо индексировать данные.
	 * @return array "Проиндексированный" массив.
	 */
	public static function indexResults($results, $key)
	{
		$indexed = array();
		foreach($results as $result) $indexed[$result[$key]] = $result;
		return $indexed;
	}


	/**
	 * Присваивает значение по умолчанию ($value) если нет ключа в массиве.
	 * @param array &$array Массив, в котором присваивать значение по умолчанию.
	 * @param string $key Ключ этого массива.
	 * @param mixed $default Значение по умолчанию, если нет ключа $key в массиве.
	 * @param string $type Преобразовывает тип значения в массиве (int, double, string, array и т.д.). Если false - то не преобразовывает тип.
	 */
	public static function setIfNotIsset(&$array, $key, $default, $type=false)
	{
		if ($type !== false) {
			if ( ! isset($array[$key]) OR ! setType($array[$key], $type))
				$array[$key] = $default;
			else
				setType($array[$key], $type);
		} else {
			if ( ! isset($array[$key]))
				$array[$key] = $default;
		}
	}


	/**
	 * Присваивает значение по умолчанию ($value) если ключ в массиве пустой.
	 * @param array &$array Массив, в котором присваивать значение по умолчанию.
	 * @param string $key Ключ этого массива.
	 * @param mixed $default Значение по умолчанию, если значение в массиве пустое.
	 * @param string $type Преобразовывает тип значения в массиве (int, double, string, array и т.д.). Если false - то не преобразовывает тип.
	 */
	public static function setIfEmpty(&$array, $key, $default, $type=false)
	{
		if ($type !== false) {
			if (empty($array[$key]) OR ! setType($array[$key], $type))
				$array[$key] = $default;
			else
				setType($array[$key], $type);
		} else {
			if (empty($array[$key]))
				$array[$key] = $default;
		}
		
	}


	/**
	 * Проверяет наличие указанных ключей в массиве и бросает исключение, если указанный ключ отсутствует.
	 * @param array $keys Список ключей, наличие которых требуется проверить.
	 * @param array $array Массив, в котором требуется проверить наличие заданных ключей.
	 * @param boolean $throwException Выбрасывать исключение, если данные не корректны.
	 * @return array Список ключей, которые не найдены в указанном массиве.
	 */
	public static function checkRequiredKeys($keys, $array, $throwException=true)
	{
		$errors = array();
		foreach($keys as $key) {
			if ( ! isset($array[$key])) {
				$errors[$key] = 'Не указан параметр '.$key;
				if ($throwException)
					throw new CHttpException('403', implode($errors[$key], '<br>'));
			}
		}
		return $errors;
	}


	/**
	 * @param array $data Массив данных, которые нужно сгруппировать по $maxCount штук.
	 * @param array $maxCount Сколько данных в группе.
	 * @return array Сгруппированный результат, 
	 * в каждой группе которого находится не больше указанного количества значений.
	 */
	public static function groupByMaxCount($data, $maxCount)
	{
		$count = 0;
		$group = array();
		$result = array();
		foreach($data as $key=>$value) {
			$count++;
			$group[$key] = $value;
			if ($count >= $maxCount) {
				$result[] = $group;
				$group = array();
				$count = 0;
			}
		}
		if ($group)
			$result[] = $group;
		
		return $result;
	}


	/**
	 * @param array $array1
	 * @param array $array2
	 * @return array Результат слияния двух массивов без сброса ключей.
	 */
	public static function merge($array1, $array2)
	{
		$result = $array1;
		foreach($array2 as $key=>$value)
			$result[$key] = $value;
		return $result;
	}


	/**
	 * @param array $array Данные, которые могут содержать пустые значения.
	 * @return boolean Являются ли все значения пустыми ( != false).
	 */
	public static function hasEmptyValues($array)
	{
		if ( ! is_array($array))
			return true;
		
		foreach($array as $value) {
			if (is_array($value)) {
				if (self::hasEmptyValues($value))
					return false;
			} else if ($value) {
				return false;
			}
		}
		
		return true;
	}


	/**
	 * Выводит переменную в развернутом виде и завершает приложение. Используется для отладки.
	 * @param mixed $array Переменная, которую нужно вывест на экран.
	 * @param boolean Завершить приложение после вызова этого метода.
	 */
	public static function d($array, $die=true)
	{
		if (is_array($array)) foreach($array as $key=>$value) if (is_array($value)) unset($array[$key]['settings_timesheet']);
		if ( ! $array)
			echo var_export($array, true);
		else
			echo '<pre>'.print_r($array,true).'</pre>';
		if ($die)
			die();
	}
}
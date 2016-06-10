<?php

/**
 * 
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2016
 * @package application.helpers.components
 */
class SearchHelper
{
	/**
	 * @param array $conditionArray Поиск по массиву, где ключ - имя поля, значение - искомое значение поля.
	 * @param string $divder Разделитель между условиями поиска (AND или OR).
	 * @param boolean $operator Оператор (=, !=, >, < и т.д.).
	 * @return array Условия поиска в виде: array('condition'=>'строка условий поиска', 'args'=>'аргументы поиска').
	 */
	public static function arrayCondition($conditionArray, $divider='AND', $operator='=')
	{
		$condition = ''; $args = array();
		
		// Условия при заданном параметре $conditionArray
		if (is_array($conditionArray)) {
			foreach($conditionArray as $key=>$value) {
				$key = (string)$key; $value = (string)$value;
				if ($condition) $condition .= ' '.$divider.' ';
				$condition .= $key.$operator.':'.$key;
				$args[':'.$key] = $value;
			}
		} else {
			$condition = '0=1';
		}
		
		return array('condition'=>$condition, 'args'=>$args);
	}
	public static function arrayCondition2($conditionArray, $divider='AND', $operator='=')
	{
		$condition = ''; $args = array();
		
		// Условия при заданном параметре $conditionArray
		if (is_array($conditionArray)) {
			foreach($conditionArray as $key=>$value) {
				$condition .= $condition ? ' '.$divider.' ' : '';
				if (is_array($value)) {
					$condition .= self::inCondition($value, $columnName=$key, $_operator='IN');
				} else {
					$condition .= $key.$operator.':'.$key;
					$args[':'.$key] = $value;
				}
			}
			if ($condition === '')
				$condition = '0=1';
		} else {
			$condition = '0=1';
		}
		
		return array('condition'=>$condition, 'args'=>$args);
	}


	/**
	 * @param array $array Массив, в котором нужно искать значения.
	 * @param string $columnName Имя колонки в которой искать массив значений.
	 * @param string $operator Оператор поиска (IN или NOT IN)
	 * @return строку для sql-условия IN (...). Если $array пустой, то возвращает заведомо ложное условие: 0=1.
	 */
	public static function listCondition($array, $columnName, $operator='IN')
	{
		if ( is_array($array) AND ! $array) return '0=1';
		
		$list = '';
		foreach($array as $value) {
			$int = preg_replace('/[^0-9]/', '', (string)$value);
			if ($value == $int AND $int !== '') {
				if ($list !== '') $list .= ',';
				$list .= $int;
			}
		}
		if ($list === '') return '1=0';
		return $columnName.' '.$operator.' ('.$list.')';
	}
	public static function listConditionGUID($array, $columnName, $operator='IN')
	{
		if ( is_array($array) AND ! $array) return '0=1';
		
		$list = '';
		foreach($array as $value) {
			$int = (string)$value;
			if (self::isGUID($int)) {
				if ($list !== '') $list .= ',';
				$list .= "'".$int."'";
			}
		}
		if ($list === '') return '1=0';
		return $columnName.' '.$operator.' ('.$list.')';
	}
	public static function inCondition($array, $columnName, $operator='IN')
	{
		return self::listCondition($array, $columnName, $operator);
	}
	public static function inConditionGUID($array, $columnName, $operator='IN')
	{
		return self::listConditionGUID($array, $columnName, $operator);
	}


	public static function stringToInt($string)
	{
		return preg_replace('/[^0-9]/', '', (string)$string);
	}


	/**
	 * Вспомогательный метод при поиске, если задан список значений ID.
	 */
	public static function addConditionByListId($array_name_ids, $array_name_id, $model, &$criteria)
	{
		foreach($array_name_ids as $key=>$attribute) {
			if (is_array($model->$attribute) AND $model->$attribute)
				$criteria->addCondition(SearchHelper::inCondition($model->$attribute, $model::table().'.'.$array_name_id[$key]));
			$attribute = 'not_'.$attribute;
			if (property_exists($model, $attribute) AND is_array($model->$attribute) AND $model->$attribute)
				$criteria->addCondition(SearchHelper::inCondition($model->$attribute, $model::table().'.'.$array_name_id[$key], $operator='NOT IN'));
		}
	}


	/**
	 * Решает проблему чувствительности символов к регистру в операторе LIKE для Sqlite.
	 * Источник: http://blog.amartynov.ru/archives/php-sqlite-case-insensitive-like-utf8/
	 * Эту функцию следует вызывать в местах, где используется оператор LIKE в запросах и база данных Sqlite.
	 * Замедляет время запроса как минимум в 2 раза.
	 * @param CDbConnection $db Объект подключения к базе данных.
	 */
	public static function sqliteFixCaseSensitiveInLikeOperator($db)
	{
		if (preg_match('/^sqlite\:.*/ui', $db->connectionString)) {
			function lexa_ci_utf8_like($mask, $value) {
				$mask = str_replace(
					array("%", "_"),
					array(".*?", "."),
					preg_quote($mask, "/")
				);
				$mask = "/^$mask$/ui";
				return preg_match($mask, str_replace("\n", ' ', $value));
			}
			
			$db->getPdoInstance()->sqliteCreateFunction('like', 'lexa_ci_utf8_like', 2);
		}
	}


//=================================================================================================
//=================================== ПЕЙДЖЕР =====================================================	
//=================================================================================================
	/**
	 * @param array $search Критерии поиска в формате: ИмяПоляТаблицы=>ЗначениеПоляТаблицы, где $search = array(
	 *     'limit' => '', // число результатов на страницу
	 *     'page'  => '', // текущая страница в пейджере
	 *     'count' => '', // общее число результатов
	 * )
	 * 
	 * @return array Вся информация о пейджере (левый, средний, правый пейджер и число результатов).
	 */
	public static function getPagerInfo($search, $count)
	{
		$resultsPerPage = (isset($search['limit']) AND (int)$search['limit']) ? (int)$search['limit'] : Yii::app()->settings->param['pager']['default']['resultsPerPageDefault'];
		$resultsPerPage = (($resultsPerPage > 0) AND ($resultsPerPage <= Yii::app()->settings->param['pager']['default']['resultsPerPageMax'])) ? $resultsPerPage : Yii::app()->settings->param['pager']['default']['resultsPerPageMax'];
		
		$page = (isset($search['page']) AND (int)$search['page']) ? (int)$search['page'] : 1; // Текущая страница в пейджере
		$pager = self::getPagesList($count, $page, $resultsPerPage);
		$pager['count'] = $count;
		return $pager;
	}


	/**
	 * @param int $count Число результатов поиска
	 * @param int $page Текущая страница
	 * @param int $resultsPerPage Результатов на страницу
	 * @return array Список страниц в результатах поиска (левый, средний и правый pager)
	 */
	public static function getPagesList($count, $page, $resultsPerPage)
	{
		// Настройки
		$pagerLeftCount      = 1; // Число страниц в левом пейджере
		$pagerRightCount     = 1; // Число страниц в правом пейджере
		$maxPages = Yii::app()->settings->param['pager']['default']['maxPages']; // Максимальное число страниц в пейджере
		
		// Список страниц в среднем пейджере при бесконечном числе результатов.
		$pagesCount = ceil($count / $resultsPerPage); // Число страниц в пейджере в результатах поиска
		$startPage  = $page - floor($maxPages/2);
		$startPage  = ($startPage > $pagesCount - $maxPages + 1) ? $pagesCount - $maxPages + 1: $startPage;
		$startPage  = ($startPage > 0) ? $startPage : 1;
		$stopPage   = $startPage + $maxPages - 1;
		$middle     = array(); // средний пейджер
		for($i=$startPage; $i<=$stopPage; $i++) {
			$middle[$i] = $i;
		}
		// Убираю из этого списка страницы, которые не входят в текущий диапазон.
		foreach($middle as $key=>$p) {
			if ($p > $pagesCount) unset($middle[$key]);
		}
		
		// Убираю из списка посередине крайние первые и посление страницы, добавляя значения к левым и правым пейджерам
		$left = array(); // Левый пейджер
		for($i=1; $i<=$pagerLeftCount; $i++) {
			if ( ! isset($middle[$i])) {
				$left[$i] = $i;
				//unset($middle[reset($middle)]);
			}
		}
		$right = array(); // Правый пейджер
		for($i=$pagesCount-$pagerRightCount+1; $i<=$pagesCount; $i++) {
			if ( ! isset($middle[$i])) {
				$right[$i] = $i;
				//unset($middle[end($middle)]);
			}
		}
		
		return array(
			'left'  =>$left, 'middle'=>$middle, 'right'=>$right, // Число страниц в левом, правом и среднем пейджере
			'start' =>(($page - 1)*$resultsPerPage + 1 < 1) ? 0 : ($page - 1)*$resultsPerPage + 1, // Какая по счету показана первая строка результата
			'stop'  =>($page*$resultsPerPage > $count) ? $count : $page*$resultsPerPage,           // Какая по счету показана последняя строка результата
			'count' => $count, // Число результатов поиска
			'limit' => $resultsPerPage, // Число результатов на страницу
			'page'  => $page, // Текущая страница в пейджере
			'nextPage' => $page + 1 > $pagesCount ? 0 : $page + 1, // Следующая страница
			'prevPage' => $page - 1 < 1 ? 0 : $page - 1, // Предыдущая страница
			'pagesCount' => $pagesCount, // Число страниц в пейджере
		);
	}
//=================================================================================================
//================================ КОНЕЦ ПЕЙДЖЕР ==================================================	
//=================================================================================================

//=================================================================================================
//=================================== СОРТИРОВКА ==================================================
//=================================================================================================
	/**
	 * Возвращает безопасный порядок сортировки из принятых данных.
	 * @param array $data Входные данные вида: array('имя_колонки'=>'порядок_сортировки', 'name'=>'asc', 'address'=>'desc').
	 * @param array $allowableKeys Список разрешенных имен полей, по которым можно сортировать вида: array('имя_колонки', 'name', 'address').
	 * @param array $allowableValues Список разрешенных порядков сортировки вида: array('asc', 'desc').
	 * @return string Строка для sql-запроса.
	 */
	public static function getOrder($data, $allowableKeys=array(), $allowableValues=array())
	{
		$keys = array(); $values = array();
		foreach($allowableKeys as $key) $keys[$key] = $key;
		foreach($allowableValues as $value) $values[$value] = $value;
		$allowableKeys = $keys; $allowableValues = $values;
		
		$result = '';
		if (is_array($data)) foreach($data as $column=>$order) {
			if (isset($allowableKeys[$column]) AND isset($allowableValues[$order])) {
				if ($result != '') $result .= ', ';
				$result .= $column.' '.$order;
			}
		}
		
		return $result;
	}
//=================================================================================================
//================================ КОНЕЦ СОРТИРОВКА ===============================================
//=================================================================================================	
}
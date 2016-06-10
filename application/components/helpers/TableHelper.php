<?php

/**
 * Вспомогательный класс для генерации таблиц.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2016
 * @package application.helpers.components
 */
class TableHelper
{
	/**
	 * Вспомогательный метод для вьюшкек.
	 * @return string Строки таблицы в формате html.
	 * @param array Строки таблицы в определенном формате.
	 */
	public static function arrayToHtmlTable($table)
	{
		$html = '';
		foreach($table as $row) {
			$attributes = '';
			foreach($row['attributes'] as $attrName=>$attrValue) {
				$attributes .= ' '.$attrName.'="'.$attrValue.'"';
			}
			$html .= '<tr'.$attributes.'>';
			foreach($row['td'] as $td) {
				$attributes = '';
				if (isset($td['attributes'])) foreach($td['attributes'] as $attrName=>$attrValue) {
					$attributes .= ' '.$attrName.'="'.$attrValue.'"';
				}
				$text = $td['content'] ? $td['content'] : '&nbsp;';
				$html .= '<td'.$attributes.'>'.$text.'</td>';
			}
			$html .= '</tr>'."\n";
		}
		return $html;
	}


	/**
	 * Вспомогательный метод для вьюшкек. Подходит только для обновления строки, для обновления всей таблицы
	 * вряд ли будет работать - для этого нужно править fnRowCallback в файле ajax-form.js, да и производительность
	 * при этом будет заметно страдать.
	 * @return string Строки таблицы в формате JSON.
	 * @param array Строки таблицы в определенном формате.
	 */
	public static function arrayToJsonTable($table)
	{
		$_row = array();
		self::pullAttributes($table, $_row);
		foreach($table as $row) {
			self::pullAttributes($row, $_row);
			$_td = array();
			foreach($row['td'] as $td) {
				$td = $td ? $td : '&nbsp;';
				$_td[] = $td;
			}
			$_row[] = $_td;
		}
		return json_encode($_row);
	}


	/**
	 * Вспомгательный метод для методов rowsToHtmlTable() и rowsToJsonTable().
	 * Копирует ключ 'attributes' из $data в $result и удаляет этот ключ из $data.
	 */
	protected static function pullAttributes(&$data, &$result)
	{
		if (isset($data['attributes'])) {
			$result['attributes'] = $data['attributes'];
			unset($data['attributes']);
		}
	}


	/**
	 * Вспомгательный метод, добавляющий &nbsp; (пробелы) в пустые ячейки таблицы, 
	 * чтобы они корректно отображалиь в ie7.
	 */
	public static function addSpacesToEmptyCells($table)
	{
		foreach($table as $key=>$row)
			foreach($row as $k=>$v)
				if (( $k != 'attributes') AND ($v == ''))
					$result[$key][$k] = '&nbsp;';
		return $table;
	}
}
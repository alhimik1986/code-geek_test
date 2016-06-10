<?php

/**
 * Вспомогательный класс для работы со строками.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2016
 * @package application.helpers.components
 */
class StringHelper
{
	/**
	 * Очищает поле для примечания, если оно не содержит букв.
	 * @param string $string Строка, принятая от html-редактора.
	 * @return string Очищенное значение.
	 */
	public static function attrNullIfEmpty($string)
	{
		return trim(str_replace(array('<br>', '&nbsp;'), '', $string)) ? $string : null;
	}


	/**
	 * @param string $full_name Фамилия, имя и отчество (полное имя)
	 * @return string Фамилия и инициалы.
	 */
	public static function getShortName($full_name)
	{
		$full_name = preg_replace('/\s+/u', ' ', trim($full_name));
		$array = explode(' ', $full_name);
		$last_name = isset($array[0]) ? $array[0] : '';
		$first_name = isset($array[1]) ? $array[1] : '';
		$middle_name = isset($array[2]) ? $array[2] : '';
		$first_name = iconv_substr($first_name, 0, 1, 'utf-8');
		$middle_name = iconv_substr($middle_name, 0, 1, 'utf-8');
		$first_name  .= $first_name ? '. ' : '';
		$initials = $first_name.$middle_name;
		
		return $last_name.($initials ? ' '.$initials : '');
	}
}
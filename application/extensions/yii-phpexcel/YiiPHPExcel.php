<?php
/**
 * Это расширение помогает прикрутить PHPExcel к Yii-framework.
 * Чтобы PHPExcel заработал в Yii-framework - выполните код:
 * <pre>include_once('YiiPHPExcel.php');</pre>
 */

spl_autoload_unregister(array('YiiBase', 'autoload'));
require('/../../../vendors/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
spl_autoload_register(array('YiiBase', 'autoload'));

if ( ! defined('EOL')) define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
//$objReader->setIncludeCharts(true);

/**
 * Сюда можно вкладывать вспомогательные методы для работы с excel.
 */
class YiiPHPExcel
{
	/**
	 * @param string $file Место нахождения файла excel-таблицы, из которого нужно считать данные.
	 * @return array Текущий лист таблицы Excel.
	 * Метод позаимствован из http://www.yiiframework.com/extension/yexcel/
	 */
	public static function readActiveSheet( $file )
	{
		include_once ('/../../../vendors/vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php');
		$objPHPExcel = PHPExcel_IOFactory::load($file);
		$sheetData = $objPHPExcel->getActiveSheet()
			->toArray($nullValue = null, $calculateFormulas = true, $formatData = true, $returnColumnRef = true);

		return $sheetData;
	}
}
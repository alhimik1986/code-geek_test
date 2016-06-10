<?php
/**
 * ��� ���������� �������� ���������� PHPExcel � Yii-framework.
 * ����� PHPExcel ��������� � Yii-framework - ��������� ���:
 * <pre>include_once('YiiPHPExcel.php');</pre>
 */

spl_autoload_unregister(array('YiiBase', 'autoload'));
require('/../../../vendors/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
spl_autoload_register(array('YiiBase', 'autoload'));

if ( ! defined('EOL')) define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
//$objReader->setIncludeCharts(true);

/**
 * ���� ����� ���������� ��������������� ������ ��� ������ � excel.
 */
class YiiPHPExcel
{
	/**
	 * @param string $file ����� ���������� ����� excel-�������, �� �������� ����� ������� ������.
	 * @return array ������� ���� ������� Excel.
	 * ����� ������������� �� http://www.yiiframework.com/extension/yexcel/
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
<?php

/**
 * Класс для установки приложения (создания и удаления таблиц).
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.Install.components
 */
class Installer
{
	/**
	 * Создает все таблицы для моделей.
	 * @return boolean Успешность удаления всех таблиц.
	 */
	public static function createTables()
	{
		return self::call_method_in_models('createTable');
	}


	/**
	 * Удаляет все таблицы, вызывая соответствующие методы в моделях (если эти методы существуют).
	 * @warnig Удаление таблицы приводит к потере данных :)
	 * @return boolean Успешность удаления всех таблиц.
	 */
	public static function dropTables()
	{
		return self::call_method_in_models('dropTable');
	}


	/**
	 * Вызыает метод во всех моделях приложения (создать или удалить таблицы)
	 * @return boolean Успешность вызова всех методов.
	 */
	public static function call_method_in_models($methodName)
	{
		$models = self::getModels();
		$error = false;
		foreach($models as $model) {
			if ( ! method_exists($model, $methodName)) {
				continue;
			}
			if ( ! call_user_func(array($model, $methodName))) {
				echo $model.'->'.$methodName.'<br>';
				$error = true;
			}
		}
		return ! $error;
	}


	/**
	 * @return array список всех моделей в веб-приложении.
	 */
	public static function getModels()
	{
		$modules = Yii::app()->modules;
		unset($modules['debug'], $modules['gii']);
		$models = array(); $paths = array();
		
		// Получаю список папок с моделями
		foreach($modules as $key=>$module) {
			$path = Yii::import('application.modules.'.$key.'.models.*');
			if ( ! is_dir($path)) continue;
			$paths[] = $path;
		}
		$path = Yii::import('application.models.*');
		if (is_dir($path)) {
			$paths[] = $path;
		}
		
		// Получаю список всех названий моделей
		foreach($paths as $path) {
			$files = CFileHelper::findFiles($path, array('fileTypes'=>array('php'), 'level'=>0));
			foreach($files as $file) {
				$models[] = basename($file, '.php');
			}
		}
		
		return $models;
	}

	public static function ok(){echo 'ok'; }
}
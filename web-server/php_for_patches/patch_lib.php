<?php
/**
 * Вспомогательный класс для низкоуровневых функций.
 */
class patchHelper
{
	/**
	 * Этот кусок достает список всех файлов в заданной папке
	 */
	public static $files;
	public static function listFolderFiles($dir){
		$ffs = scandir($dir);
		foreach($ffs as $ff){
			if($ff != '.' && $ff != '..'){
				if(is_dir($dir . DIRECTORY_SEPARATOR . $ff)) {
					self::listFolderFiles($dir . DIRECTORY_SEPARATOR . $ff);
				} else {
					self::$files[$dir.'/'.$ff] = $ff;
				}
			}
		}
	}
	public static function files($dir)
	{
		self::$files = array();
		self::listFolderFiles($dir);
		return self::$files;
	}
	
	
	/**
	 * Возвращает путь на один уровень вверх, только слева, а не справа.
	 */
	public static function one_level_up($path)
	{
		$path = explode(DIRECTORY_SEPARATOR, $path);
		unset($path[0]);
		return implode(DIRECTORY_SEPARATOR, $path);
	}
}



class patch extends patchHelper
{
	/**
	 * Считывает все файлы шаблонов
	 */
	public static function read_templates($main_dir)
	{
		$files = array();
		foreach(self::files('templates') as $path=>$file_name)
			$files[$path] = file_get_contents($main_dir.'/'.$path);
		
		return $files;
	}
	
	
	/**
	 * Считывает все значения параметров шаблонов.
	 */
	public static function read_params($templates, $main_dir)
	{
		$params = array();
		foreach($templates as $path=>$file_name) {
			$params_path = $main_dir.'/params/'.self::one_level_up($path).'.php';
			$params[$path] = is_file($params_path) ? include_once($params_path) : '';
		}
		return $params;
	}
	
	
	/**
	 * Записывает все файлы шаблонов
	 */
	public static function write_templates($templates, $main_dir)
	{
		foreach($templates as $path=>$content) {
			$path = $main_dir.'/../../vendor/'.self::one_level_up($path);
			
			// Создаю папку, если ее нет.
			$path_info = pathinfo($path);
			if ( ! is_dir($path_info['dirname'])) {
				mkdir($path_info['dirname'], 0777, true);
			}
			
			// Убираю из $path папку "templates" и вставляю файл в получившийся относительный путь.
			file_put_contents($path, $content);
		}
	}
	
	
	/**
	 * Производит замену шаблонных переменных на заданные параметры.
	 */
	public static function replace_templates($templates, $params)
	{
		foreach($templates as $key=>$content) {
			if ( ! isset($params[$key]) OR ! is_array($params[$key])) continue;
			$templates[$key] = strtr($content, $params[$key]);
		}
		return $templates;
	}
	
	
	/**
	 * Главная функция, что-то вроде main();
	 */
	public static function run($main_dir)
	{
		$templates = self::read_templates($main_dir);
		$params = self::read_params($templates, $main_dir);
		$r_templates = self::replace_templates($templates, $params);
		self::write_templates($r_templates, $main_dir);
	}
}

// Погнали!!!
//patch::run(__DIR__);

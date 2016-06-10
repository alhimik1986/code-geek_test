<?php

/**
 * Помогает сжимать файлы стилей (.css) и java-скриптов (.js).
 * Его можно как наследовать, так использовать отдельно.
 * Пример использования:
 *     - Добавляем файлы, вызывая методы: JsCssFiles::js('script.js'); JsCssFiles::css('style.css');
 *     - Добавляем скрипты в виде строк, если нужно: JsCssFiles::js('alert("ok");'); JsCssFiles::css('body{font-family:Arial;}');
 *     - После того, как все файлы добавлены, вызвать JsCssFiles::compress();
 * 
 * Добавленные файлы и скрипты можно очистить: JsCssFiles::clear();
 * Чтобы очистить и  заново сгенерировать сжатые файлы (например, какой-нибудть .js- или .css-файл претерпел изменения)
 * вызовите метод JsCssFiles::flush();
 * Чтобы изменить папку со сжатыми файлами, то впишите ее в JsCssFiles::$path со слешем в конце
 * Чтобы изменить корневую публичную папку, то впишите ее в JsCssFiles::$webRoot со слешем в конце
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.components
 * @depends Yii framework
 * @depends yii-EClientScript (Yii extension)
 */
class JsCssFiles
{
	protected static $_mergeFiles    = false;
	protected static $_compressFiles = false;
	/**
	 * @var string Папка, в которой будут храниться сжатые файлы
	 */
	public static $path = false;
	/**
	 * @return string Путь к папке со сжатыми файлами (не установлен, то папка в js/compressed/ в директории, где был запущен index.php)
	 */
	protected static function getPath() {
		return self::$path===false ? Yii::getPathOfAlias('webroot.compressed').DIRECTORY_SEPARATOR : self::$path;
	}
	public static $webRoot = false;
	protected static function getWebRoot() {
		return self::$webRoot===false ? Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR : self::$webRoot;
	}
	/**
	 * @var string url-путь к сжатому файлу.
	 */
	public static $publicPath = false;
	protected static function getPublicPath() {
		return self::$publicPath===false ? Yii::app()->baseUrl.'/compressed/' : self::$publicPath;
	}
	
	// Строки и массивы, содержащие скрипты и перечисленные файлы, их можно очистить по отдельности,
	// присвоив им, соответственно, '' или array().
	// А можно вызвать JsCssFiles::clear(); чтобы очистить их всех.
	public static $js = '';
	public static $css = '';
	public static $jsFiles = array();
	public static $cssFiles = array();
	
	// Функции для добавления скриптов и файлов для сжатия.
	public static function js($string)        { self::$js .= $string;          }
	public static function css($string)       { self::$css .= $string;         }
	public static function jsFile($fileName)  { self::$jsFiles[] = $fileName;  }
	public static function cssFile($fileName) { self::$cssFiles[] = $fileName; }
	
	/**
	 * Очищает все добавленные строки и файлы.
	 */
	public static function clear()
	{
		self::$js = '';
		self::$css = '';
		self::$jsFiles = array();
		self::$cssFiles = array();
	}
	
	
	/**
	 * Удалить все сжатые файлы, чтобы компрессор сгенерировал новые.
	 * Код взят из: http://stackoverflow.com/questions/4594180/deleting-all-files-from-a-folder-using-php
	 * Он удаляет все js- и css-файлы (с длиной имени 35-36 символов) в папке, указанной в  JsCssFiles::$path;
	 */
	public static function flush()
	{
		$files = glob(self::getPath().'*'); // get all file names
		foreach($files as $file){ // iterate files
			if(is_file($file))
				if ( (substr($file, -4) == '.css') OR (substr($file, -3) == '.js'))
					if ( (strlen(basename($file)) == 36) OR (strlen(basename($file)) == 35) )
						unlink($file); // delete file
		}
	}
	
	
	/**
	 * Проводит слияение, сжатие и загрузку добавленных файлов и возвращает имя сжатого файла.
	 * @param array Список добавленных файлов
	 * @param string $fileExtension Расширение добавленных файлов, чтобы подобрать нужный компрессор для сжатия.
	 * @return string Имя сжатого файла
	 */
	protected static function mergeAndCompressFilesWithExtension($files, $fileExtension)
	{
		$code = '';
		$compressedFileName = md5(serialize($files)) . $fileExtension;
		$webFileName        = self::getPublicPath() . $compressedFileName;
		$compressedFileName = self::getPath() . $compressedFileName;
		
		if ( ! file_exists($compressedFileName)) {
			foreach($files as $file) {
				$code .= file_get_contents(self::getWebRoot().$file);
			}
			// Сжимаю js-файл
			if ($fileExtension == '.js') {
				Yii::import('ext.yii-EClientScript.JSMinPlus');
				if (self::$_compressFiles) $code = JSMinPlus::minify($code);
			}
			// Сжимаю css-файл
			if ($fileExtension == '.css') {
				Yii::import('ext.yii-EClientScript.CssMin');
				if (self::$_compressFiles) $code = CssMin::minify($code, array(), array('CompressUnitValues' => true));
			}
			
			//if ( ! file_exists($compressedFileName)) mkdir($compressedFileName, 0777, true);
			file_put_contents($compressedFileName, $code);
		}
		return $webFileName;
	}
	
	
	/**
	 * Собирает все js- и css-файлы в один js- и css-файл и загружает его.
	 * Этот метод нужно вызвать, после того, как будут записаны все скрипты и добавлены все файлы.
	 */
	public static function mergeAndCompress($mergeFiles=null, $compressFiles=null)
	{
		if ($mergeFiles    !== null) self::$_mergeFiles    = $mergeFiles;
		if ($compressFiles !== null) self::$_compressFiles = $compressFiles;
		
		$cs = Yii::app()->clientScript;
		
		if (self::$js  !== '') $cs->registerScript('JsCssCompressor_js', self::$js, CClientScript::POS_BEGIN);
		if (self::$css !== '') $cs->registerCss('JsCssCompressor_css', self::$css);
		if (self::$jsFiles ) {
			if (self::$_mergeFiles) {
				$compressedFileName = self::mergeAndCompressFilesWithExtension(self::$jsFiles, '.js');
				$cs->registerScriptFile($compressedFileName);
			} else {
				foreach(self::$jsFiles as $jsFile) $cs->registerScriptFile($jsFile);
			}
		}
		if (self::$cssFiles) {
			if (self::$_mergeFiles) {
				$compressedFileName = self::mergeAndCompressFilesWithExtension(self::$cssFiles, '.css');
				$cs->registerCssFile($compressedFileName);
			} else {
				foreach(self::$cssFiles as $cssFile) $cs->registerCssFile($cssFile);
			}
		}
	}
}
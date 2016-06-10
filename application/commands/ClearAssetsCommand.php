<?php
/**
 * Clear assets Command
 *
 * Deletes all published assets.
 * 
 * Это класс для очистки папки assets. Отредактировал под свои нужды.
 * 
 * Usage:
 * yiic clearassets
 *
 * @author Alexander Makarov
 */
class ClearAssetsCommand extends CConsoleCommand{
    /**
     * Executes the command.
     * @param array command line parameters for this command.
     */
    public function run($args)
    {
        $path =  Yii::app()->getAssetManager()->getBasePath();
        $di = new DirectoryIterator($path);
        foreach($di as $d)
        {
            if(!$d->isDot())
            {
                $this->removeDirRecursive($d->getPathname());
            }
        }
		
		$this->clearDebug(); // Очистка папки debug
		$this->clearLog();   // Очистка файлов журнала (runtime/application.log)
    }

    function removeDirRecursive($dir)
    {
        $files = glob($dir.'*', GLOB_MARK);
        foreach ($files as $file)
        {
            if (is_dir($file)){
                $this->removeDirRecursive($file);
            }
            else if (basename($file) != '.gitignore'){
                unlink($file);
            }
        }

        if (is_dir($dir)) rmdir($dir);
    }
	
	/**
	 * Очистка папки debug
	 */
	public function clearDebug()
	{
		if (class_exists('Yii2Debug', $autoload=false)) {
			array_map('unlink', glob(Yii::app()->debug->logPath.'/*'));
		}
	}
	
	/**
	 * Очистка логов.
	 */
	public function clearLog()
	{
		foreach(Yii::app()->log->routes as $route) {
			if($route instanceof CFileLogRoute) {
				array_map('unlink', glob($route->logPath.'/application*.log'));
			}
		}
		
	}
}

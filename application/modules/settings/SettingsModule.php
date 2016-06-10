<?php
/**
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.settings
 */
class SettingsModule extends CWebModule
{
	public $defaultController = 'Setting';
	public $settings_json_file;
	
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'settings.models.*',
			'settings.components.*',
		));
		
		if ($this->settings_json_file)
			SettingsModel::set_custom_settings_json_file_path($this->settings_json_file);
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}

<?php

/**
 * Настройки системы учета времени.
 * 
 * Извлечь настройки можно командой: Yii::app()->settings->param['имя_параметра_настройки'].
 * Изменять параметры настройки можно только на странице настроек системы учета времени.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.settings.components
 */
class Settings extends CApplicationComponent
{
	/**
	 * Параметры настройки.
	 */
	public $param;


	public function __construct()
	{
		$this->param = $this->getAllSettings();
	}

	protected function getAllSettings()
	{
		$settings = SettingsModel::getSettings();
		if (is_array($settings)) foreach($settings as $key=>$value)
			$settings[$key] = $value['value'];
		
		return $settings;
	}

	public function get($name)
	{
		return SettingsModel::getSetting($name);
	}

	public function set($name, $value)
	{
		$result = SettingsModel::setSetting($name, $value);
		$this->param = $this->getAllSettings();
		return $result;
	}

	public function getAll()
	{
		return $this->getAllSettings();
	}
}
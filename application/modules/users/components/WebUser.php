<?php
/**
 * Это вспомогательный класс для контроля доступа по ролям.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.users.components
 */
class WebUser extends CWebUser
{
	// Параметры пользователя (те же, что и в таблице users)
	public $param;

	// Роль пользователя
	public $role;

	// В каких группах состоит пользователь
	public $inGroup = array();

	// Чтобы различать пользователей тест- и продакшн-режима.
	public function afterLogin($fromCookie)
	{
		$this->setState('script_name', $_SERVER['SCRIPT_NAME']);
		$this->setState('ip', Controller::getIp());
	}
}
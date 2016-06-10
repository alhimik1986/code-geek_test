<?php
/**
 * AccessControl фильтр, обеспечивающий контроль доступа по ролям.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.controllers.filters
 */
// 8ms Denwer
class AccessControl extends CAccessControlFilter
{
    public function preFilter($filterChain)
    {
		// Загружаю параметры пользователя
		$user = UserIdentity::loadUser();
		
		// Проверяю, не заблокирован ли сайт
		$this->checkTheSiteIsNotBlocked($user);
		
		// Присваиваю роли в компоненте пользователя
		$this->_setUserRoles($user);
		
		// Проверяю: авторизован ли пользователь с нужного index.php или index-test.php файла
		$loginUrl = Yii::app()->getController()->createUrl('/'.reset(Yii::app()->user->loginUrl));
		if ( ! $this->checkScriptName() ) {
			if ( ! Yii::app()->user->isGuest ) Yii::app()->user->logout();
			Yii::app()->getRequest()->redirect($loginUrl);
		}
		
		// Проверяю доступ по разрешенным ролям.	
        if ( $this->checkControllerAndAction(Yii::app()->user->role) ) {
			return true;
        } else if (Yii::app()->user->isGuest) {
			Yii::app()->getRequest()->redirect($loginUrl);
		} else {
			$this->accessDenied(Yii::app()->user, Yii::t('app', 'Access is denied!'));
		}
    }


	/**
	 * Присваивает роли в компоненте пользователя.
	 * @param array $user Данные пользователя.
	 */
	public function _setUserRoles($user)
	{
		Yii::app()->user->role = $user ? $user['role'] : Users::GUEST;
		Yii::app()->user->param = $user;
		
		// Определяю, в каких группах состоит пользователь
		foreach(Users::groupList() as $group)
			Yii::app()->user->inGroup[$group] = in_array(Yii::app()->user->role, Users::$$group);
	}


	/**
	 * Проверка доступа текущего контроллера и действия.
	 */
	public function checkControllerAndAction($role)
	{
		$accessByRoles = Yii::app()->getController()->accessByRoles();
		if ( ! isset($accessByRoles[Yii::app()->controller->action->id])) {
			return false;
		}
		return (array_search($role, $accessByRoles[Yii::app()->controller->action->id]) !== false);
	}


	/**
	 * Проверяет совпадают ли файл, запускающий фреймворк и файл, из которого авторизовался пользователь.
	 * Чтобы авторизованные пользователи тестового и рабочего режимов не пересекались (запускаемых, соответственно, из index.php и index-test.php)
	 * Чтобы админ тестового режима не мог войти в админку продакшн-режима.
	 */
	public function checkScriptName()
	{
		if ( ! Yii::app()->user->isGuest ) {
			return (Yii::app()->user->getState('script_name') == $_SERVER['SCRIPT_NAME']);
		} else {
			return true;
		}
	}


	/**
	 * Проверяет, не заблокирован ли сайт.
	 */
	protected function checkTheSiteIsNotBlocked($user)
	{
		$site = Yii::app()->settings->param['Site'];
		if ($site['blocked'] AND ( ! $user OR ! in_array($user['role'], Users::$ADMINS))) {
			Yii::app()->getController()->renderPartial('application.views.site.blocked.index', array('blockedReason'=>$site['blockedReason']));
			Yii::app()->end();
		}
	}
}
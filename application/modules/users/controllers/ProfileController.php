<?php

/**
 * Управление профилем пользователей.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.users.controllers
 */

class ProfileController extends Controller
{
	/**
	 * Фильтр: контроль доступа по ролям.
	 */
    public function filters()
    {
        return array(
            array('application.Controllers.filters.AccessControl'),
        );
    }
	/**
	 * Передаю фильтру данные о доступе в формате: действие => разрешенные_роли.
	 */
	public function accessByRoles()
	{
		return array(
			'index' => Users::$AUTHENTICATED,
		);
	}


	/**
	 * Страница профиля пользователя.
	 */
	public function actionIndex()
	{
		$user = UserIdentity::loadUser();
		$usersModel = (Yii::app()->user->role != Users::SUPER_ADMIN) ? Users::getModel($user['id']) : null;
		$profile = new ProfileForm;
		// Устанавливаю по умолчанию аттрибуты профиля пользователя ранними значениями.
		foreach($profile->attributes as $key=>$value) $profile->$key = $user[$key];
		
		if (isset($_POST['ProfileForm'])) {
			$profile->attributes = $_POST['ProfileForm'];
			$profile->saveProfile($usersModel, $_POST['ProfileForm']);
			// Проверить ошибоки валидации и вывести в формате JSON: если успех - удачно записанные данные, если ошибка - ошибки.
			$this->checkErrorsAndDisplayResult($profile);
		} else {
			$this->render('index', array(
				'model'  => $profile,
				'user'   => $user,
			));
		}
	}
}
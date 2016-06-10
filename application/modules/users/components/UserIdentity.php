<?php
/**
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.users.components
 */
class UserIdentity extends CUserIdentity
{
	// Константы ошибок
	const ERROR_NONE             = 0; // Нет ошибок авторизации
	const ERROR_USERNAME_INVALID = 1; // Неправильное имя пользователя
	const ERROR_PASSWORD_INVALID = 2; // Неправильный пароль
	const ERROR_USER_REMOVED     = 3; // Пользователь удален
	const ERROR_USER_BLOCKED     = 4; // Пользователь заблокирован
	const ERROR_IP_INVALID       = 5; // IP-адрес пользователя не совпадает с разрешенным


	/**
	 * Достает параметры пользователя из БД по его username. Если его нет в базе, то берет параметры супер администратора и также проверяет имя пользователя.
	 * Использутся при входе в систему (LoginForm.php) и при определении прав (AccessControl.php)
	 * @param string $username - имя пользователя, если не указано, то берется текущий пользователь
	 * @return array параметры текущего пользователя
	 */
	public static function loadUser($username='')
	{
		// Имя пользователя: если пустое, то текущий пользователь (если пользователь не вошел в систему, то возвращаю пустой результат)
		if ( ! $username) {
			if (Yii::app()->user->isGuest) {
				return array();
			} else {
				$username = Yii::app()->user->name;
			}
		}
		
		// Вход в систему идет как через "Имя пользователя" так и через "E-mail"
		
		// Поиск пользователя (системного супер администратора)
		$user = Yii::app()->settings->param['superadmin'];
		if ( ($user['username'] !== $username) AND ($user['email'] !== $username) ) {
			$user = array();
		} else {
			// Обрабатываю данные
			$registration_date = DateHelper::sqlDate('db', $user['registration_date']).DateHelper::date(' H:i:s', $user['registration_date']);
			$user['password'] = MyPasswordHelper::hashPassword($registration_date, $user['password']);
			if ($user) {
				$user['FIO'] = $user['last_name'].' '.$user['first_name'].' '.$user['middle_name'];
				$user['fio'] = $user['last_name'].' '.iconv_substr($user['first_name'], 0, 1, 'utf-8').'.'.iconv_substr($user['middle_name'], 0, 1, 'utf-8').'.';
			}
		}
		
		// Если пользователь - не суперадмин, то ищу его в базе данных.
		try {
			if ( ! $user) {
				$user = Users::getArrayByArray(array('email'=>$username));
				if ( ! $user)
					$user = Users::getArrayByArray(array('username'=>$username));
				$user = reset($user);
				
				// Если в базе данных зарегистриовали суперадмина, то запрещаю доступ
				if ($user AND ($user['role'] == Users::SUPER_ADMIN)) {
					Yii::app()->user->logout();
					throw new CHttpException('403', Yii::t('users.app', 'Access is denied!'));
				}
			}
		} catch (CDbException $e) { // Если база пользователей не создана, то вывожу предупреждающее сообщение.
			$user = array();
			throw new CHttpException('403', Yii::t('users.app', 'Table with users not found in database. May be the application not installed. Contact your site administrator.'));
		}
		
		if ($user) {
			if ($user['removed'] OR $user['blocked']) {
				$user['role'] = Users::GUEST;
			}
		}
		
		if ($user AND $user['role'] != Users::SUPER_ADMIN) {
			$registration_date = DateHelper::sqlDate('db', $user['registration_date']).DateHelper::date(' H:i:s', $user['registration_date']);
			$user['email_password'] = MyPasswordHelper::decrypt_string($user['email_password'], MyPasswordHelper::combination($registration_date, ''));
			
		}
		
		return $user;
	}


	/**
	 * Проверка авторизации пользователя
	 */
	public function authenticate()
	{
		// Достаю пользователя из БД по его username. Если его нет в базе, то вставляю параметры супер администратора.
		$user = self::loadUser($this->username);
		$registration_date = $user ? DateHelper::sqlDate('db', $user['registration_date']).DateHelper::date(' H:i:s', $user['registration_date']) : '';
		$this->errorCode = self::ERROR_NONE;
		
		// Проверяю авторизацию пользователя по заданным параметрам
		if ( ! $user OR ! $this->username) {
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		} else if ( ($user['username'] !== $this->username) AND ($user['email'] !== $this->username)) {
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		} else if ( ! MyPasswordHelper::validatePassword($registration_date, $this->password, $user['password'])) {
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		} else if ($user['removed']) {
			$this->errorCode=self::ERROR_USER_REMOVED;
		} else if ($user['blocked']) {
			$this->errorCode=self::ERROR_USER_BLOCKED;
		} else if ($user['ip'] AND $user['check_ip'] AND ($user['ip'] != Controller::getIp()) ) {
			$this->errorCode=self::ERROR_IP_INVALID;
		} else {
			$this->errorCode = self::ERROR_NONE;
			$this->username = $user['username'];
		}
		
		return !$this->errorCode;
	}
}
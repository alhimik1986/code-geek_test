<?php

/**
 * Форма для восстановления учетной записи и входа в систему по ссылке восстановления.
 * 
 * @depends application.modules.users.models.Users.php
 * @depends application.components.Controller::getIp()
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.users.models
 */
class RecoveryForm extends CFormModel
{
	public $username;   // Имя пользователя или почта пользователя
	public $hash;       // Хеш-код для генерации ссылки восстановления
	public $user;       // Информация о пользователе
	public $verifyCode; // Каптча

	public static function className() { return __CLASS__; }

	/**
	 * Максимальное число заявок на восстановление за день.
	 */
	public static function maxRecoveryCount()    { return 30;    }
	/**
	 * Время (в секундах), через которое заявка на восстановление считается не действительной (по умолчанию 1 час).
	 */
	public static function maxRecoveryDuration() { return 60*60; }
	/**
	 * Соль для шифрования хеш-кода
	 */
	protected static $salt = 'r&-]e*|.q%3o7_';


	/**
	 * @return array Правила валидации.
	 */
	public function rules()
	{
		return array(
			array('username', 'required'),
			array('username, hash', 'filter', 'filter'=>'trim'),
			array('verifyCode', 'captcha', 'on'=>'request',
				'allowEmpty'=>!CCaptcha::checkRequirements() OR ! (Yii::app()->settings->param['captcha']['enabled'] AND Yii::app()->settings->param['captcha']['recovery']),
				'captchaAction' => '//'.Yii::app()->controller->module->id.'/user/captcha',
			),
			array('username', 'loadUser', 'message'=>''), // Проверка существования пользователя
		);
	}


	/**
	 * @return array Подписи полей (имя_поля => подпись)
	 */
	public function attributeLabels()
	{
		return array(
			'username'   => Yii::t('app', 'Username or E-mail'),
			'hash'       => Yii::t('app', 'Recovery code'),
			'verifyCode' => Yii::t('app', 'Text in the image'),
		);
	}


	/**
	 * Проверка существования пользователя.
	 */
	public function loadUser($attribute, $params)
	{
		$user = UserIdentity::loadUser($this->$attribute);
		
		if ( ! $user OR $user['removed'] OR $user['blocked'] OR ! $user['email']) {
			$this->addError($attribute, Yii::t('users.app', 'A user with the "E-mail" or "username" does not exist or is blocked.'));
			return false;
		}
		
		return $this->user = $user;
	}


	/**
	 * Записывает информацию для восстановления пользователя. Возвращает хеш-код для восстановления пароля.
	 */
	public function recordRecoveryInfo()
	{
		if ($this->user['role'] == Users::SUPER_ADMIN) {
			throw new CHttpException('403', Yii::t('users.app', 'You can not restore the system user.'));
		}
		
		// Расшифровываю информацию о восстановлении
		if ($this->user['recovery_info']) {
			$info = explode('_', $this->user['recovery_info']);
			$date = $info[0];  // Дата-время заявки на восстановление
			$count = $info[1]; // Число заявок на восстановление
		} else {
			$date = false;
			$count = 0;
		}
		
		// Если ранее восстанавливался пароль
		if ($date) {
			// если пароль восстанавливался в тот же день, то увеличиваю счетчик восстановлений
			if (date('d') == DateHelper::date('d', $date)) {
				$count++;
			} else {
				$count = 0;
			}
			
			if ($count > self::maxRecoveryCount()) throw new CHttpException('403', Yii::t('users.app', 'Reached maximum limit of attempts to recover passwords per day. Please, refer to the administrator of this site to recover your password or try to recover the password tomorrow.'));
		}
		
		// Генерирую новую информацию о восстановлении
		$date = date('Y-m-d H:i:s');
		$info = $date.'_'.$count.'_'.Controller::getIp();
		
		// Записываю информацию о восстановлении в базу данных.
		Users::db()->createCommand()->update(Users::table(), array('recovery_info'=>$info), 'id=:id', array(':id'=>$this->user['id']));
		$this->user['recovery_info'] = $info;
		
		return $this->hash = self::getRecoveryHash($this->user);
	}


	/**
	 * Проверяет хеш-код восстановления и не просрочена ли заявка на восстановление пароля.
	 */
	public function login()
	{
		if ( ! $info = $this->user['recovery_info']) {
			return false; // Отказ, если нет информации о восстановлении (пользователь ни разу не пытался восстановить учетную запись)
		}
		$info = explode('_', $info);
		if ( ! $requestDate = DateHelper::strtotime($info[0])) {
			return false; // Отказ, если не опозналась дата заявки на восстановление
		}
		
		$now = new DateTime();
		$requestDate->modify('+ '.(int)self::maxRecoveryDuration().' second');
		
		if (($requestDate > $now) AND (self::getRecoveryHash($this->user) === $this->hash) ) {
			if ( ! Yii::app()->user->isGuest) Yii::app()->user->logout();
			Yii::app()->user->login(new UserIdentity($this->user['username'],$this->hash), Yii::app()->settings->param['loginDuration']['short']);
			return true;
		} else {
			return false; // Отказ, если заявка просрочена или неправильный hash-код
		}
	}


	/**
	 * Возвращает хеш-код для восстановления пароля.
	 */
	public static function getRecoveryHash($user)
	{
		if ( ! $user['recovery_info']) return false;
		return md5($user['username'].$user['password'].$user['email'].$user['recovery_info'].self::$salt);
	}
}
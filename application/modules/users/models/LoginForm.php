<?php

/**
 * Класс-модель для входа в систему.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.users.models
 */
class LoginForm extends CFormModel
{
	public $username;   // Имя пользователя
	public $password;   // Пароль
	public $rememberMe; // Частный компьютер (запомнить пароль на 1 месяц)

	private $_identity;

	public static function className() { return __CLASS__; }

	/**
	 * @return array Правила валидации.
	 */
	public function rules()
	{
		return array(
			array('username, password', 'required'), // Обязательные поля
			array('username, password', 'filter', 'filter'=>'trim'), // Очищаю пробелы в начале и в конце
			array('rememberMe', 'boolean'), // Должно быть булевым типом
		);
	}


	/**
	 * @return array Подписи полей (имя_поля => подпись)
	 */
	public function attributeLabels()
	{
		return array(
			'username'   => Yii::t('app', 'Username'),
			'password'   => Yii::t('app', 'Password'),
			'rememberMe' => Yii::t('app', 'Remember me'),
		);
	}


	/**
	 * Вход в систему под введенными "Имя пользователя" и "Паролем"
	 * @return boolean Успешно ли вошел пользователь.
	 */
	public function login()
	{
		$this->_identity = new UserIdentity($this->username,$this->password);
		$this->_identity->authenticate();

		if($this->_identity->errorCode===UserIdentity::ERROR_NONE) {
			$duration=$this->rememberMe ? Yii::app()->settings->param['loginDuration']['long'] : Yii::app()->settings->param['loginDuration']['short']; // 30 дней иначе 2 часа
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		} else {
			//$this->addError('password', 'Неправильное имя пользователя или пароль.'. $this->_identity->errorCode);
			$this->addError('password', Yii::t('app', 'Incorrect username or password.'));
			return false;
		}
	}
}
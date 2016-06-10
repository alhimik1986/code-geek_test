<?php

/**
 * Класс-модель профиля пользователя.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.users.models
 */
class ProfileForm extends CFormModel
{
	public $username=null;   // Имя пользователя
	public $password=null;   // Пароль
	public $email=null;      // Электронная почта
	public $phone=null;      // Телефон
	public $ip=null;         // IP-адрес

	public static function className() { return __CLASS__; }

	/**
	 * @return array Правила валидации.
	 */
	public function rules()
	{
		return array(
			array('username,', 'required'), // Обязательные поля
			array('username,password,email,phone,ip', 'filter', 'filter'=>'trim'), // Очищаю пробелы в начале и в конце
			array('username,password,email,phone,ip', 'length', 'max'=>255), // Не должны превышать 255 символов
		);
	}


	/**
	 * @return array Подписи полей (имя_поля => подпись)
	 */
	public function attributeLabels()
	{
		return array(
			'username' => Yii::t('app', 'Username'),
			'password' => Yii::t('app', 'Password'),
			'email'    => Yii::t('app', 'E-mail'),
			'phone'    => Yii::t('app', 'Contact phone'),
			'ip'       => Yii::t('app', 'IP-address'),
		);
	}


	/**
	 * Сохранение данных. Если системный супер администратор, то сохраняет в файл settings.json, иначе в модель Users.
	 * Возвращает модель пользователя.
	 * 
	 * @param mixed $model Модель пользователя
	 * @return mixed Модель пользователя с наличием или отсутствием ошибок после валидации и сохранения.
	 */
	public function saveProfile($model, $data)
	{
		// Валидация
		$this->validate();
		if ($this->hasErrors())
			return false;
		
		// Если пользователь есть в базе, то сохраняю его в базу данных.
		if (Yii::app()->user->role != Users::SUPER_ADMIN) {
			$model->setAttrAndSave($this->attributes);
			
			// Копирую ошибки валидации из модели пользователя.
			$errors = $model->getErrors();
			foreach($errors as $key=>$error)
				$this->addError($key, $error);
		} else { // Если же это - системный супер администратор, то сохраняю его в файл settings.json
			$settings = SettingsModel::getModel('superadmin');
			$superadmin = $settings->value;
			
			// Присваиваю все аттрибуты модели профиля к модели настроек.
			foreach($this->attributes as $key=>$value) {
				// Если пароль пустой, то не изменяю его.
				if (($key == 'password') AND ! $value AND isset($data['password']))
					continue;
				$settings->value[$key] = $value;
			}
			
			// Проверяю уникальность имени пользователя и email в базе данных пользователей, если они изменены
			if ($superadmin['username'] != $this->username) {
				$result = Users::getArrayByArray(array('username'=>$this->username));
				if ( ! $result AND $this->email)
					$result = Users::getArrayByArray(array('username'=>$this->email));
				if ($result)
					$this->addError('username', Yii::t('app', 'This username already used.'));
			}
			if ($superadmin['email'] != $this->email) {
				$result = Users::getArrayByArray(array('email'=>$this->email));
				if ( ! $result AND $this->username)
					$result = Users::getArrayByArray(array('email'=>$this->username));
				if ($result)
					$this->addError('email', Yii::t('app', 'This E-mail already used.'));
			}
			
			if ( ! $this->hasErrors() AND $settings->validate())
				$settings->save();
			
			// Копирую ошибки валидации в модель пользователя.
			foreach($settings->getErrors() as $key=>$error)
				$this->addError($key, $error);
		}
		
		// Переавторизовываюсь, если пользователь изменил ник.
		if ( ! $this->hasErrors() AND ($this->username != Yii::app()->user->name)) {
			Yii::app()->user->logout();
			Yii::app()->user->login(new UserIdentity($this->username, $this->password), Yii::app()->settings->param['loginDuration']['short']);
		}
		
		return $this->hasErrors();
	}
}
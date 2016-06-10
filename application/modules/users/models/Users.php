<?php
/**
 * Управляет пользователями.
 * Для присвоения аттрибутов всегда пользуйтесь функцией setAttr(); в ней обрабатываются пароль и отделы, а также действуют нужные правила валидации.
 * Для присвоения аттрибутов, валидации и сохранения результатов одновременно (для экономии строк) пользуйтесь функцией setAttrAndSave().
 * 
 * Объект подключения: Yii::app()->db. Имеются следующие поля в таблице "users":
 *     id                  pk                        ID пользователя
 *     last_name           nvarchar(255)  NOT NULL   Фамилия
 *     first_name          nvarchar(255)  NOT NULL   Имя
 *     middle_name         nvarchar(255)  NOT NULL   Отчество
 *     role                integer        NOT NULL   Роль (по умолчанию - упраляющий)
 *     blocked             boolean        NOT NULL   Заблокирован ли пользователь
 *     removed             boolean        NOT NULL   Удален ли пользователь
 *     subscribed          boolean        NOT NULL   Подписан ли пользователь на рассылку нарушений (по умолчанию - подписан)
 *     username            nvarchar(255)  NOT NULL   Имя пользователя
 *     password            nvarchar(255)  NOT NULL   Пароль
 *     email               nvarchar(255)      NULL   Электронная почта
 *     phone               nvarchar(255)      NULL   Контактный телефон пользователя
 *     comment             text               NULL   Комментарий к пользователю
 *     registration_date   datetime           NULL   Дата регистрации
 *     recovery_info       nvarchar(255)      NULL   Информация о восстановлении пользователя (дата-время заявки на восстановление + число заявок на восстановление за день; дата-время и число заявок разделены символом "_")
 *     confirmed           boolean        NOT NULL   Подтвержден ли пользователь (это пометка не блокирует пользователя, она показывает, что пользователь зарегистрировался и ожидает подтверждения (разблокировки). При регистрации поле blocked=1 (заблокирован) стоит по умолчанию.
 *     email_host          nvarchar(255)      NULL   Хост для отправки сообщения
 *     email_port          integer            NULL   Порт для отправки сообщения
 *     email_smtp_secure   integer            NULL   Протокол (ssl, tls) для отправки сообщения
 *     email_address       nvarchar(255)      NULL   Адрес отправителя для отправки сообщения
 *     email_from_name     nvarchar(255)      NULL   Имя отправителя для отправки сообщения
 *     email_username      nvarchar(255)      NULL   Имя пользователя для отправки сообщения
 *     email_password      nvarchar(255)      NULL   Пароль для отправки сообщения
 *     email_class         integer            NULL   Класс (тип) почтового менеджера
 *     fio                 nvarchar(255)      NULL   ФИО (для быстрого поиска)
 *     all_text            text               NULL   Смесь всех колонок для поиска по всем параметрам
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.users.models
 */

Yii::import('application.modules.users.models.UsersRolesHelper'); // Для yii-doc (документатора)
class Users extends UsersRolesHelper
{
	// Кэширование
	public static function cacheDuration() { return Yii::app()->settings->param['cache']['enabled'] ? Yii::app()->settings->param['cache']['Users'] : ''; }

	// Значения по умолчанию
	public $role       = self::ADMIN; // Роль (по умолчанию - упраляющий)
	public $blocked    = 1;
	public $removed    = 0;
	public $subscribed = 1;
	public $confirmed  = 1;
	public $verifyCode;  // Captcha

	// Подключение к базе данных
	public static function db() { return Yii::app()->db; }
	// Имя таблицы
	public static function table() { return 'users'; }
    // Класс модели
    public static function model($className = __CLASS__) { return parent::model($className); }

	/**
	 * @return array Правила валидации.
	 */
	public function rules() {
		$roleList = $this->roleList(); unset($roleList[self::SUPER_ADMIN]); $roleList = array_keys($roleList);
		return array(
			array('comment', 'application.extensions.htmlpurifier.XSSFilter'), // Экранирую html-сущности (защита от XSS-атак).
			array('last_name,first_name,middle_name,role,username,password,email,phone', 'filter', 'filter'=>array($this, 'utf_htmlspecialchars')), // Экранирую html-сущности (защита от XSS-атак).
			array('last_name,first_name,middle_name,role,username,password,email,phone,comment,email_host,email_port,email_smtp_secure,email_address,email_from_name,email_username,email_password, email_class', 'filter', 'filter'=>'trim'),// Убираю пробелы в начале и в конце во всех полях, кроме массивов.
			array('last_name,first_name,middle_name,role,username,email,phone', 'match', 'pattern' => '/^[a-zA-Zа-яА-Я0-9_\s\.\-\@]+$/u', 'message' => Yii::t('app', 'This field must contain only letters and numbers.')),// Эти поля не должны содержать спец. символы
			array('last_name,first_name,role, username,password', 'warning'), // Вывожу предупраждение, если после очистки пробелов стали пустыми обязательные поля
			array('last_name,first_name,role, username,password', 'required'), // Эти поля не должны быть пустыми (обязательные поля).
			array('last_name,first_name,middle_name,username,password,email,phone,recovery_info', 'length', 'max'=>255), // Не должны быть больше 255 символов.
			array('role', 'in', 'range'=>$roleList), // Роль должна быть одной из списка доступных для создания пользователей.
			array('role', 'checkRole'), // Проверяю, чтобы нельзя было создавать или редактировать Администратора или Супер администратора.
			array('blocked,removed,subscribed,confirmed', 'in', 'range'=>array(0,1), 'allowEmpty'=>false, 'message'=>Yii::t('app', '{attribute} must be 0 or 1.')), // Должны иметь значени 0 или 1.
			array('email', 'email'), // Это поле должно соответствовать шаблону email-адреса.
			array('username,email', 'compare1'), // Имя пользователя и email не должны быть равны данным супер администратора.
			array('comment', 'length', 'max'=>40000), // Не должен превышать 4000 символов
			array('fio', 'length', 'max'=>255), // Не должен превышать 255 символов
			array('all_text', 'length', 'max'=>400000),
			array('username,email', 'unique', 'attributeName'=>'username'), // "Имя пользователя" и электронная почта (для восстановления пароля и входа в систему) должны быть уникальными. Уникальными между собой.
			array('username,email', 'unique', 'attributeName'=>'email'),    // "Имя пользователя" и электронная почта (для восстановления пароля и входа в систему) должны быть уникальными. Уникальными между собой.
			//array('registration_date', 'type', 'type'=>'datetime', 'datetimeFormat'=>'yyyymmdd hh:mm:ss.???', 'on'=>'create'), // Должен быть иметь формат даты-время (вопросики означают - любой символ)
			array('email_class', 'in', 'range'=>array_keys(UsersMailHelper::email_class_list())),
			array('verifyCode', 'captcha', 'on'=>'registration',
				'allowEmpty'=>!CCaptcha::checkRequirements() OR ! (Yii::app()->settings->param['captcha']['enabled'] AND Yii::app()->settings->param['captcha']['registration']),
				'captchaAction' => '//users/user/captcha1',
			), // Каптча при регистрации пользователя
		);
	}
	// Предупреждающее сообщение, если после очистки от пробелов стало пустым обязательное поле
	public function warning($attribute, $params) {
		if (($this->$attribute === '') AND isset($this->data[$attribute]) AND ($this->data[$attribute] !== '')) {
			Yii::app()->user->setFlash('info', Yii::t('app', 'The field "{field}" should not contain just spaces.', array('{field}'=>$this->getAttributeLabel($attribute))));
		}
	}
	// Проверяю, чтобы "Имя пользователя" или E-mail не были равны данным супер администратора
	public function compare1($attribute, $params) {
		$user = Yii::app()->settings->param['superadmin'];
		if ($this->$attribute == $user['username'] OR $this->$attribute == $user['email']) {
			$this->addError($attribute, Yii::t('app', '{attr_label} "{attr_value}" already used.', array('{attr_label}'=>$this->getAttributeLabel($attribute), '{attr_value}' => $this->$attribute)));
		}
	}
	/**
	 * Проверяю, чтобы нельзя было создавать или редактировать Администратора или Супер администратора.
	 * Супер администратор может делать все.
	 */
	public function checkRole()
	{
		if (Yii::app()->user->role == self::SUPER_ADMIN)
			return true;
		
		if ($this->role != $this->oldAttributes['role']) {
			if ($this->role <= self::ADMIN OR $this->oldAttributes['role'] <= self::ADMIN) {
				$this->addError('role', Yii::t('app', 'Not allowed to edit roles greater or equal to yours.'));
				return false;
			}
		}
		return true;
	}


	/**
	 * @return array Подписи полей (имя_поля => подпись)
	 */
	public function attributeLabels() 
	{
		return array(
			'id'                 => Yii::t('app', 'ID'),
			'last_name'          => Yii::t('app', 'Surname'),
			'first_name'         => Yii::t('app', 'Name'),
			'middle_name'        => Yii::t('app', 'Patronymic'),
			'role'               => Yii::t('app', 'Role'),
			'blocked'            => Yii::t('app', 'Blocked'),
			'removed'            => Yii::t('app', 'Removed'),
			'subscribed'         => Yii::t('app', 'Subscribe to newsletters'),
			'username'           => Yii::t('app', 'Username'),
			'password'           => Yii::t('app', 'Password'),
			'email'              => Yii::t('app', 'E-mail'),
			'phone'              => Yii::t('app', 'Contact phone'),
			'comment'            => Yii::t('app', 'Comment'),
			'registration_date'  => Yii::t('app', 'Registration date'),
			'recovery_info'      => Yii::t('app', 'Information of user recovery'),
			'confirmed'          => Yii::t('app', 'Confirmed'),
			'verifyCode'         => Yii::t('app', 'Verification code'),
			'email_host'         => Yii::t('app', 'Host (SMTP)'),
			'email_port'         => Yii::t('app', 'Port'),
			'email_smtp_secure'  => Yii::t('app', 'Protocol'),
			'email_address'      => Yii::t('app', 'Sender (E-mail)'),
			'email_from_name'    => Yii::t('app', 'Sender name'),
			'email_username'     => Yii::t('app', 'Username'),
			'email_password'     => Yii::t('app', 'Password'),
			'email_class'        => Yii::t('app', 'Type of mail'),
		);
	}


	/**
	 * @Override
	 * Обработка данных, полученных с формы перед присвоением аттрибутов.
	 */
	public function beforeSetAttr($data, $allowableKeys)
	{
		// Запрещаю изменять Фамилию, Имя, Отчество, Имя пользователя; шифрую пароль при его создании или изменении
		if ( ! $this->isNewRecord) {
			if (Yii::app()->user->role != Users::SUPER_ADMIN) { // Только Супер администратор может менять ФИО
				unset($data['last_name']);
				unset($data['first_name']);
				unset($data['middle_name']);
			}
			unset($data['registration_date']);
			unset($data['recovery_info']);
			// Если поле "Пароль" пустое, то оставляю аттрибут "Пароль" без изменений.
			if (isset($data['password']) AND ($data['password'] === '') ) {
				unset($data['password']);
			}
			if (isset($data['email_password']) AND ($data['email_password'] === '') ) {
				unset($data['email_password']);
			}
		}
		
		return $data;
	}


	/**
	 * @Override
	 * Дополнительные правила валидации после присвоения аттрибутов.
	 */
	public function afterSetAttr($data, $allowableKeys)
	{
		// Если новая запись, то пишу дату регистрации
		$this->registration_date = $this->isNewRecord ? DateHelper::date('d-m-Y H:i:s') : $this->registration_date;
		DateHelper::formatDateAttribute($this, 'registration_date', Yii::t('app', 'Incorrect registration date.'));
		
		// Шифрую пароль, если новая запись или аттрибут "Пароль" изменился и он не пустой.
		if ($this->isNewRecord) {
			$this->password = MyPasswordHelper::hashPassword($this->registration_date, $this->password);
		} else if ($this->password !== $this->oldAttributes['password']) {
			if ( $this->password !== '') {
				$this->password = MyPasswordHelper::hashPassword($this->registration_date, $this->password);
			}
		}
		// Шифрую пароль для почты
		if ($this->email_password !== $this->oldAttributes['email_password']) {
			if ( $this->email_password ) {
				$this->email_password = MyPasswordHelper::encrypt_string($this->email_password, MyPasswordHelper::combination($this->registration_date, ''));
			}
		}
		// Обновление поля для поиска по всем параметрам
		$this->all_text = $this->getAllText();
		// ФИО для поиска
		$this->fio = trim($this->last_name).' '.trim($this->first_name).' '.trim($this->middle_name);
		$this->fio = iconv_substr($this->fio, 0, 255, 'utf-8');
		
		$this->comment = trim(str_replace(array('<br>', '&nbsp;'), '', $this->comment)) ? $this->comment : null; // Очищаю примечание, если оно не содержит букв
	}
	public function getAllText()
	{
 		$all = array('id', 'last_name', 'first_name', 'middle_name', 'phone', 'email', 'username', 'comment', 
			'registration_date', 'email_host', 'email_port', 'email_smtp_secure', 'email_address', 'email_username',
			'email_from_name');
		$text = '';
		foreach($all as $value) {
			$text .= ($text AND $this->$value) ? ' ' : '';
			$text .= trim($this->$value);
		}
		
		$text .= ArrayHelper::getValue1(self::roleList(), $this->role, 'role='.$this->role);
		
		return $text;
	}


	/**
	 * @Override
	 * Обработка полученных результатов.
	 * @param array $data Результаты, полученные из базы данных.
	 * @return array Обработанные результаты.
	 */
	public static function handleResults($data, $pkName=null)
	{
		$results = array();
		$pk_name = self::getPkColumnName();
		foreach($data as $value) {
			$value['FIO'] = $value['last_name'].' '.$value['first_name'].' '.$value['middle_name'];
			$value['fio'] = $value['last_name'].' '.iconv_substr($value['first_name'], 0, 1, 'utf-8').'.'.iconv_substr($value['middle_name'], 0, 1, 'utf-8').'.';
			$results[$value[$pk_name]] = $value;
		}
		return $results;
	}


	/**
	 * @return CDbCriteria Критерии поиска.
	 */
    public function getCriteria()
    {
        $criteria=new CDbCriteria;
		
        $criteria->compare(self::table().'.id', SearchHelper::stringToInt($this->id));
        $criteria->compare(self::table().'.last_name',$this->last_name,true);
        $criteria->compare(self::table().'.first_name',$this->first_name,true);
        $criteria->compare(self::table().'.middle_name',$this->middle_name,true);
        $criteria->compare(self::table().'.role',SearchHelper::stringToInt($this->role));
        $criteria->compare(self::table().'.blocked',SearchHelper::stringToInt($this->blocked));
        $criteria->compare(self::table().'.removed',SearchHelper::stringToInt($this->removed));
        $criteria->compare(self::table().'.subscribed',SearchHelper::stringToInt($this->subscribed));
        $criteria->compare(self::table().'.username',$this->username,true);
        //$criteria->compare(self::table().'.password',$this->password,true);
        $criteria->compare(self::table().'.email',$this->email,true);
        $criteria->compare(self::table().'.phone',$this->phone,true);
		$criteria->compare(self::table().'.comment',$this->comment,true);
        //$criteria->compare(self::table().'.registration_date',$this->registration_date,true);
		//$criteria->compare(self::table().'.recovery_info',$this->recovery_info,true);
		$criteria->compare(self::table().'.confirmed',$this->confirmed);
		$criteria->compare(self::table().'.email_host',$this->email_host,true);
		$criteria->compare(self::table().'.email_port',$this->email_port,true);
		$criteria->compare(self::table().'.email_smtp_secure',$this->email_smtp_secure,true);
		$criteria->compare(self::table().'.email_address',$this->email_address,true);
		$criteria->compare(self::table().'.email_from_name',$this->email_from_name,true);
		$criteria->compare(self::table().'.email_username',$this->email_username,true);
		//$criteria->compare(self::table().'.email_password',$this->email_password,true);
		//$criteria->compare(self::table().'.email_class',$this->email_class);
		$criteria->compare(self::table().'.fio',$this->fio,true);
		$criteria->compare(self::table().'.all_text',$this->all_text,true);
		
        return $criteria;
    }


	/**
	 * @return array Пользователь по указанному ID, если ID=0, то возвращает суперадмина.
	 * @param integer $id ID пользователя.
	 */
	public static function getUserById($id)
	{
		$result = ($id == 0) ? Yii::app()->settings->param['superadmin'] : self::getArrayById($id);
		$result['FIO'] = $result['last_name'].' '.$result['first_name'].' '.$result['middle_name'];
		$result['fio'] = $result['last_name'].' '.iconv_substr($result['first_name'], 0, 1, 'utf-8').'.'.iconv_substr($result['middle_name'], 0, 1, 'utf-8').'.';
		return $result;
	}


	/**
	 * Создает таблицу в базе данных для данной модели (это удобно при использовании инсталлятора).
	 */
	public static function createTable() {
		try {
			self::db()->createCommand()->select('*')->from(self::table())->limit(1)->query()->readAll();
			return false;
		} catch (CDbException $e) {
			
		}
		// Имя таблицы: [users]. Имеются следующие поля:
		self::db()->createCommand()->createTable(self::table(), array(
			'id'                 => 'pk',                       // ID пользователя
			'last_name'          => 'nvarchar(255)  NOT NULL',  // Имя
			'first_name'         => 'nvarchar(255)  NOT NULL',  // Фамилия
			'middle_name'        => 'nvarchar(255)      NULL',  // Отчество
			'role'               => 'integer        NOT NULL',  // Роль
			'blocked'            => 'boolean        NOT NULL',  // Заблокирован ли пользователь
			'removed'            => 'boolean        NOT NULL',  // Удален ли пользователь
			'subscribed'         => 'boolean        NOT NULL',  // Подписан ли пользователь на рассылку нарушений
			'username'           => 'nvarchar(255)  NOT NULL',  // Имя пользователя
			'password'           => 'nvarchar(255)  NOT NULL',  // Пароль
			'email'              => 'nvarchar(255)      NULL',  // Электронная почта
			'phone'              => 'nvarchar(255)      NULL',  // Контактный телефон пользователя
			'comment'            => 'text               NULL',  // Комментарий к пользователю
			'registration_date'  => 'datetime           NULL',  // Дата регистрации
			'recovery_info'      => 'nvarchar(255)      NULL',  // Информация о восстановлении пользователя
			'confirmed'          => 'boolean        NOT NULL',  // Подтвержденный пользователь
			'email_host'         => 'nvarchar(255)      NULL',  // Хост для отправки сообщения
			'email_port'         => 'integer            NULL',  // Порт для отправки сообщения
			'email_smtp_secure'  => 'nvarchar(255)      NULL',  // Протокол (ssl, tls) для отправки сообщения
			'email_address'      => 'nvarchar(255)      NULL',  // Адрес отправителя для отправки сообщения
			'email_from_name'    => 'nvarchar(255)      NULL',  // Имя отправителя для отправки сообщения
			'email_username'     => 'nvarchar(255)      NULL',  // Имя пользователя для отправки сообщения
			'email_password'     => 'nvarchar(255)      NULL',  // Пароль для отправки сообщения
			'email_class'        => 'nvarchar(255)      NULL',  // Класс (тип) почтового менеджера
			'fio'                => 'nvarchar(255)      NULL',  // ФИО (для быстрого поиска)
			'all_text'           => 'text               NULL',  // Смесь всех колонок для поиска по всем параметрам
		));
		return true;
	}
	/**
	 * Удаляет таблицу в базе данных для этой модели (тоже необходимо для инсталлятора)
	 */
	public static function dropTable() {
		try {
			self::db()->createCommand()->select('*')->from(self::table())->limit(1)->query()->readAll();
			self::db()->createCommand()->dropTable(self::table());
			return true;
		} catch (CException $e) {
			return false;
		}
	}
}
<?php
/**
 * Вспомогательный класс для отправки email-сообщений.
 * 
 * Если нужно прикрепить файл без формы отправки сообщений, то отправку сообщения нужно делать в следующем виде:
 * <pre>
 * $data = array('subject'=>'...', 'text'=>'...', 'emails'=>array('Петров <petrov@mail.ru>', 'ivanov@ivanov.gmail.ru', 'Вася [vasya@vasya.ru]') и т.д.);
 * $mail = new MailForm;
 * $mail->setAttr($data);
 * $mail->files = array(
 *     array('name'=>'Имя файла', 'path'=>'местоположение файла с указанием имени файла'),
 *     array('name'=>'Имя файла', 'path'=>'местоположение файла с указанием имени файла'),
 * );
 * 
 * // При желании можно изменить кое-какие аттрибуты (т.к. они не присваиваются в $mail->setAttr($data)):
 * // $mail->setFromEmail($email);
 * // $mail->setFromName($from_name);
 * 
 * ($mail->validate() AND $mail->send(MailLog::MESSAGE_TYPE_NEWSLETTER)); // MailLog::MESSAGE_TYPE_NEWSLETTER - указываем тип сообщения - рассылка
 * </pre>
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.mail.models
 * @depends application.modules.mail.models.MailLog
 * @depends application.modules.mail.models.MailFilesLog
 * @depends application.models.Users
 */

class MailForm extends CFormModel {
	public $subject;           // Тема сообщения
	public $text;              // Текст сообщения
	public $emails;            // Список адресатов
	public $cc;                // Копия
	public $files = array();   // Файлы, прикрепленные к сообщению

	// Эти поля не должны вводиться извне, но должны проходить валидацию (поэтому сделал их публичными, но они перезаписываются в методе setAttr($data))
	public $from_email;        // Отправитель (почта)
	public $from_name;         // Отправитель (имя)
	
	protected $oldAttributes;  // Старые значения атррибутов (до присвоения входных данных)
	protected $data;           // Данные, принятые с формы
	
	protected $_from_email = null; // Отправитель (почта) - присваивается посредством функции setFromEmail($from_email);
	protected $_from_name  = null; // Отправитель (имя)   - присваивается посредством функции setFromName($from_name);
	public function setFromEmail($from_email) { $this->_from_email = $from_email; }
	public function setFromName($from_name)   { $this->_from_name  = $from_name;  }
	public function getFromName()  { return $this->_from_name;  }
	public function getFromEmail() { return $this->_from_email; }

    // Класс модели
    public static function model($className = __CLASS__) { return parent::model($className); }
	// Имя класса
	public static function className() { return __CLASS__; }


	/**
	 * @return mixed Длительность кэширования в секундах. Если установить '', то кэширование производиться не будет.
	 */
	public static function cacheDuration() { return Yii::app()->settings->param['cache']['enabled'] ? Yii::app()->settings->param['cache']['Users'] : ''; }


	/**
	 * Правила валидации
	 */
	public function rules()
	{
		return array(
			array('subject, from_name, text', 'application.extensions.htmlpurifier.XSSFilter'), // Очищаю содержимое от XSS-атак
			array('subject,text,emails', 'required'), // Обязательные поля
			array('subject,text,from_email,from_name', 'filter', 'filter'=>'trim'), // Очищаю от пробелов в начале и конце
			array('subject,text,from_email,from_name', 'filter', 'filter'=>array($this, 'html_purifier')), // Экранирую html-сущности (защита от XSS-атак).
			array('subject,from_email,from_name', 'length', 'max'=>255), // Не должны быть больше 255 символов.
			array('text', 'type', 'type'=>'string'), // Должны быть строкой
			array('emails, cc, files', 'safe'), // Помечаю безопасной, т.к. обрабатывать буду вручную
		);
	}


	/**
	 * @return array Подписи полей (имя_поля => подпись)
	 */
	public function attributeLabels()
	{
		return array(
			'subject'    => Yii::t('mail.app', 'Subject'),
			'text'       => Yii::t('mail.app', 'Text'),
			'emails'     => Yii::t('mail.app', 'Addresses'),
			'cc'         => Yii::t('mail.app', 'Carbon copy'),
			'files'      => Yii::t('mail.app', 'Attach file'),
			'from_email' => Yii::t('mail.app', 'Sender (E-mail)'),
			'from_name'  => Yii::t('mail.app', 'Sender (name)'),
		);
	}


	/**
	 * Присваивает аттрибуты через эту функцию, чтобы дейстовали нужные правила валидации.
	 */
	public function setAttr($data, $allowableKeys=null, $extractFiles=true)
	{
		$this->oldAttributes = $this->attributes;
		unset($data['from_email'], $data['from_name']);
		$this->data = $data;
		// Фильтрую данные по разрешенным ключам
		 $data = Model::filterData($data, $allowableKeys);
		
		$this->attributes = $data;
		
		// Извлекаю файлы, если они есть и это разрешено
		$this->files = array();
		if ($extractFiles) {
			$files = Model::extractFiles($_FILES, self::className(), 'files');
			foreach($files as $file)
				$this->files[] = array('name'=>$file['name'], 'content'=>$file['content']);
		}
		
		// Парсю email-адреса
		$this->emails = (isset($data['emails']) AND $data['emails']) ? self::handle_emails($data['emails']) : array();
		$this->cc = (isset($data['cc']) AND $data['cc'] AND is_array($data['cc'])) ? self::handle_emails($data['cc']) : array();
	}


	/**
	 * Отправляет сообщение.
	 * @param int $type Тип сообщения.
	 * @return bool Успешность отправки почты и записи в журнал.
	 */
	public function send($type=-1)
	{
		if ($this->hasErrors())
			return false;
		if ( ! $this->emails) {
			$this->addError('emails', Yii::t('mail.app', 'No addresses selected.'));
			return false;
		}
		
		Yii::import('application.modules.mail.includes.ExchangeMailHelper');
		Yii::import('application.modules.mail.includes.PHPMailerHelper');
		$class_list = UsersMailHelper::email_class_list();
		/*$class_key = Yii::app()->user->isGuest
			? Yii::app()->settings->param['superadmin']['email_class']
			: Yii::app()->user->param['email_class'];*/
		$class_key = Yii::app()->settings->param['superadmin']['email_class'];
		$mailerClass = isset($class_list[$class_key]) ? $class_list[$class_key] : $class_list[Users::PHPMailerHelper];
		$mailer = new $mailerClass;
		
		// Отправляю сообщение, журналирую в случае успеха, возвращаю успешность действий.
		if (defined('PHPUNIT_TEST_MODE') OR $sended=$mailer::send($this, $type)) {
			return true;
		} else {
			$this->addError('emails', Yii::t('mail.app', 'Unable to send a message.'));
			return false;
		}
	}


	/**
	 * @return array Возвращает массив id-пользователей по массиву $email-адресов.
	 */
	protected static function getUserIdsByEmails($emails)
	{
		Yii::import('application.modules.users.models.Users');
		$users = new Users;
		$results = $users->getDbConnection()->createCommand()->select('id')->from($users->tableName())->where(array('in', 'email', $emails))->queryAll();
		foreach($results as $key=>$result)
			$results[$key] = $result['id'];
		return $results;
	}


	/**
	 * Парсит email-адреса (адреса, разделенные запятыми и переводом строки, переводятся в массив)
	 * @param  string &$email    Список email-адресов, разделенных запятыми или переводом строки
	 * @param  array  $delimiter Массив разделителей email-адресов
	 * @return array Массив распознанных email-адресов
	 */
	/*public static function parseEmails($emails, $delimiter=array("\n", ',')){
		$reg = implode('|', $delimiter);
		$emails = preg_split( '/('.$reg.')/', $emails); // аналог explode, только расщепляет строку по нескольким разделителям
		foreach($emails as $key=>$email) {
			preg_match('/\<\w+\>/', $email, $email_array); // Извлекаю email из: < >
			$email = isset($email_array[0]) ? substr($email_array[0], 1, -1) : $email; // Убираю символы: < >, если они есть
			$emails[$key] = trim($email);
			if ( ! $emails[$key]) unset($emails[$key]);
		}
		return $emails;
	}*/


	/**
	 * @param string $string Строка с неизвестной кодировкой.
	 * @return string Возвращает строку с кодировкой utf-8.
	 */
	public static function toUTF8($string)
	{
		if (mb_check_encoding($string, 'windows-1251')) {
			return iconv('windows-1251', 'utf-8', $string);
		} else if ($encoding = mb_detect_encoding($string)) {
			return iconv($encoding, 'utf-8', $string);
		} else {
			return $string;
		}
	}


	// Экранирую html-теги (защита от XSS-атак).
	public function html_purifier($value) {
		$p = new CHtmlPurifier(array(
			'HTML.Allowed'=>'p,ul,li,b,i,a[href],pre',
		));
		return $p->purify($value);
	}


	/**
	 * @return array список адресов всех пользователей.
	 */
	public static function getEmailList()
	{
		if (self::cacheDuration() !== '') { $cacheId = __CLASS__.__FUNCTION__.$_SERVER['PHP_SELF']; $cache = Yii::app()->cache[$cacheId]; if ($cache !== false) return $cache; }
		
		Yii::import('application.modules.users.models.Users');
		$users = new Users;
		$results = $users->dbConnection->createCommand()->select('last_name,first_name,middle_name,email')->from($users->tableName())->query()->readAll();
		$users = array();
		foreach($results as $result) {
			if ( ! $result['email']) continue;
			$fio = $result['last_name'].' '.iconv_substr($result['first_name'], 0, 1, 'utf-8').'.'.iconv_substr($result['middle_name'], 0, 1, 'utf-8').'.';
			$users[] = $fio.' <'.$result['email'].'>';
		}
		
		if (self::cacheDuration() !== '') { Yii::app()->cache->set($cacheId, $users, self::cacheDuration()); }
		
		return $users;
	}


	/**
	 * @return возвращает пропарсенные email-адреса.
	 */
	public static function handle_emails($emails)
	{
		$validator=new CEmailValidator;
		$result = array();
		foreach($emails as $key=>$email) {
			// Парсю email-адреса по скобкам < > или [ ]
			if ( ! preg_match('/\<(.*?)\>/', $email, $email_array)) { // Извлекаю email из: < >
				preg_match('/\[(.*?)\]/', $email, $email_array); // Извлекаю email из: [ ]
			}
			
			$email = isset($email_array[0]) ? substr($email_array[0], 1, -1) : $email; // Убираю символы: < > или [ ], если они есть
			$email = trim($email);
			
			if ($email AND $validator->validateValue($email)) {
				$result[] = $email;
			}
		}
		return $result;
	}


	/**
	 * Удалить сообщение и все вложенные файлы.
	 * @param integer $id id-сообщения.
	 * @return MailForm(CFormModel) Модуль с наличием или отсутствием ошибок.
	 */
	public static function deleteMessage($id)
	{
		$model = MailLog::model()->findByPk($id);
		if($model===null) throw new CHttpException(404, Yii::t('mail.app', 'Message not found.'));
		$filesModel = MailFilesLog::model();
		
		// Пробую удалить отправленное письмо в журнале сообщений.
		$transaction = $model->dbConnection->beginTransaction();
		try {
			$model->deleteByPk($id);
			$filesModel->deleteAllByAttributes(array('mail_log_id'=>$id));
			$transaction->commit();
		} catch (CException $e) {
			$transaction->rollBack();
			$model->addError('file', Yii::t('mail.app', 'Failed to delete a file in the message log.'));
			return $model;
		}
		
		return $model;
	}
}
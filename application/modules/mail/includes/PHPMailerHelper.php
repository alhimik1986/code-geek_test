<?php
/**
 * Вспомогательный класс для отправки сообщения на обычные smtp сервера (GMAIL, mail.ru и т.д.).
 * При такой отправке НЕ ЗАПОМИНАЮТСЯ исходящие сообщения.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2015
 * @package application.modules.mail.includes
 */
class PHPMailerHelper
{
	/**
	 * Отпправляет сообщение.
	 * @param MailForm $model Модель для отправки формы почтового сообщения.
	 * @param integer $type Тип сообщения.
	 * @return boolean Успешность отправки сообщения.
	 */
	public static function send($model, $type=-1)
	{
		/*$user = Yii::app()->user->isGuest
			? Yii::app()->settings->param['superadmin']
			: Yii::app()->user->param; // Параметры пользователя*/
		$user = Yii::app()->settings->param['superadmin'];
		
		Yii::import('application.modules.mail.extensions.PHPMailer.phpmailer', true);
		
		$mailer = new PHPMailer();
		$mailer->CharSet = 'UTF-8';
		//$mailer->Mailer = 'smtp';
		$mailer->IsSMTP();  // telling the class to use SMTP
		$mailer->SMTPDebug  = 0;                 // enables SMTP debug information (for testing)
                                                   // 1 = errors and messages
                                                   // 2 = messages only
		$mailer->SMTPAuth   = true;                  // enable SMTP authentication
		if ($user['email_smtp_secure']) {
			$mailer->SMTPSecure = $user['email_smtp_secure']; // set secure protocol (ssl, tls, etc)
		}
		$mailer->Host       = $user['email_host'];      // sets the SMTP server
		$mailer->Port       = (int)$user['email_port'];      // set the SMTP port for the GMAIL server
		$mailer->Username   = $user['email_username'];  // SMTP account username
		$mailer->Password   = $user['email_password'];  // SMTP account password
		$mailer->From       =  ( ! is_null($model->getFromEmail())) ? $model->getFromEmail() : $user['email_address'];   // sender address
		$mailer->FromName   =  ( ! is_null($model->getFromName()) ) ? $model->getFromName()  : $user['email_from_name']; // sender name
		//$mailer->Sender     =  ( ! is_null($model->_from_email)) ? $model->_from_email : $user['email_address'];
		//$mailer->Priority = 1;
		$mailer->AddReplyTo($user['email_address'], "Info");
		
		$mailer->Subject = $model->subject;
		$mailer->MsgHTML($model->text);
		$mailer->AltBody = "";
		
		// Присвавиваю emails
		foreach($model->emails as $email)
			$mailer->AddAddress($email);
		
		// Присвавиваю копии
		foreach($model->cc as $cc)
			$mailer->AddCC($cc);
		
		// Прикрепляю файлы
		foreach($model->files as $file) {
			$mailer->AddStringAttachment($file['content'], $file['name']);
		}
		
		
function exception_error_handler($errno, $errstr, $errfile, $errline ) {throw new ErrorException(iconv('cp1251', 'utf-8', $errstr), $errno, 0, $errfile, $errline);} set_error_handler("exception_error_handler");
		
		return $mailer->Send();
	}
}
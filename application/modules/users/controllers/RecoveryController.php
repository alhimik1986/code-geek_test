<?php

/**
 * Восстановление пользователей. Т.к. строчек кода немного, то вспомогательные функции я разместил прямо здесь.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.users.controllers
 */

class RecoveryController extends Controller
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
			'request'        => Users::$ALL,
			'requestSuccess' => Users::$ALL,
			'login'          => Users::$ALL,
		);
	}


	/**
	 * Заявка на восстановление пароля
	 */
	public function actionRequest()
	{
		if( ! isset($_POST['RecoveryForm']) ) {
			throw new CHttpException(400, Yii::t('app', 'Bad request.'));
		}
		
		$model = new RecoveryForm('request');
		$model->attributes = $_POST['RecoveryForm'];
		if ( $model->validate() AND $model->recordRecoveryInfo() ) {
			$model->username = $this->createUrl('recovery/requestSuccess');
			$this->sendRecoveryLink($model->user, $this->createAbsoluteUrl('recovery/login', array('username'=>$model->user['email'], 'hash'=>$model->hash))); // Посылаю ссылку восстановления на почту пользователя.
		}
		$this->checkErrorsAndDisplayResult($model);
	}


	/**
	 * Сообщение, что ссылка успешно отправлена на почту.
	 */
	public function actionRequestSuccess()
	{
		$this->render('requestSuccess');
	}


	/**
	 * Вход в систему по ссылке восстановления.
	 */
	public function actionLogin($username, $hash)
	{
		$model = new RecoveryForm;
		$model->attributes = $_GET;
		
		if ($model->validate() AND $model->login()) {
			$this->redirect((Yii::getVersion() == '1.1.16') ? $this->createUrl('profile/index') : array($this->createUrl('profile/index')));
		} else {
			throw new CHttpException('403', Yii::t('users.app', 'The reference to recovery is incorrect or expired.'));
		}
	}


	/**
	 * Отправка ссылки для восстановления пароля на почту пользователя.
	 */
	public function sendRecoveryLink($user, $url)
	{
		if ( ! defined('PHPUNIT_TEST_MODE')) {
			Yii::import('application.modules.mail.models.MailForm');
			Yii::import('application.modules.mail.models.MailLog');
			
			// Подставляю учетные данные суперадминистратора для отправки почты
			$oldUser = Yii::app()->user->param;
			Yii::app()->user->param = Yii::app()->settings->param['superadmin'];
			
			$mail = new MailForm;
			$mail->emails = array($user['email']);
			$mail->subject = Yii::t('users.app', 'Time tracking: password recovery');
			$mail->setFromName(Yii::t('users.app', 'Time tracking'));
			$mail->text = $this->renderPartial('sendRecoveryLink', array('user'=>$user, 'url'=>$url), true);
			
			($mail->validate() AND $mail->send(MailLog::MESSAGE_TYPE_RECOVERY)); // MailLog::MESSAGE_TYPE_RECOVERY - указываем тип сообщения - "восстановление пароля"
			
			// Возвращаю прежнего пользователя
			Yii::app()->user->param = $oldUser;
		}
	}
}
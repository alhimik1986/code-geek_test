<?php

/**
 * Форма отправки сообщений.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.mail.controllers
 */
class FormController extends Controller
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
		$both = array_merge(Users::$ADMINS, array(Users::SUPERVISOR));
		return array(
			'index' => Yii::app()->request->isPostRequest ? Users::$ADMINS : $both,
		);
	}


	/**
	 * Форма для отправки сообщения.
	 */
	public function actionIndex()
	{
		if (isset($_POST['MailForm'])) {
			$model = new MailForm;
			$model->setAttr($_POST['MailForm']);
			
			if (isset($_POST['MailForm']['validate']) AND $_POST['MailForm']['validate']) {
				$model->validate();
			} else {
				( ! $model->hasErrors() AND $model->validate() AND $model->send(MailLog::MESSAGE_TYPE_PERSONAL));
			}
			
			// Проверить ошибоки валидации и вывести в формате JSON: если успех - удачно записанные данные, если ошибка - ошибки.
			$this->checkErrorsAndDisplayResult($model);
		} else {
			$model = new MailForm;
			$this->renderJson('_form', array(
				'model'     => $model,
				'formTitle' => isset($_GET['formTitle']) ? $_GET['formTitle'] : Yii::t('mail.app', 'Message'),
			));
		}
	}
}
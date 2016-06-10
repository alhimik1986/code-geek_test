<?php

/**
 * Восстановление пользователей. Т.к. строчек кода немного, то вспомогательные функции я разместил прямо здесь.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.users.controllers
 */

class RegistrationController extends Controller
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
			'form'              => Users::$ALL,
			'success'           => Users::$ALL,
			'confirmation'      => Users::$ADMINS,
			'confirmationForm'  => Users::$ADMINS,
			'search'            => Users::$ADMINS,
		);
	}


	/**
	 * Форма регистрации (заявка на регистрацию)
	 */
	public function actionForm()
	{
		if (isset($_POST['Users'])) {
			$model = new Users;
			// При $model->validate() и $model->save() captcha генерируется и проверяется дважды, что приводит к ошибке валидации.
			// Валидация происходит при заданном мной сценарии: registration,
			// поэтому после валидации перед сохранением меняю сценарий на старый, чтобы не было двойной валидации каптчи.
			$scenario = $model->scenario;
			$model->scenario = 'registration';
			
			$model->setAttr($_POST['Users']);
			$model->blocked = 1; $model->confirmed = 0;
			if ($model->validate()) {
				$model->scenario = $scenario;
				$model->save();
			}
			// Проверить ошибоки валидации и вывести в формате JSON: если успех - удачно записанные данные, если ошибка - ошибки.
			$this->checkErrorsAndDisplayResult($model);
		} else {
			$this->renderJson('_registration_form', array(
				'model'     => new Users,
				'formTitle' => Yii::t('users.app', 'Registration form'),
			));
		}
	}


	/**
	 * Страница при успешной регистрации пользователя.
	 */
	public function actionSuccess()
	{
		$this->render('success');
	}


	/**
	 * Список неподтвержденных пользователей.
	 */
	public function actionConfirmation()
	{
		$this->render('confirmation', array(
			'model'    => new Users,
			'roleList' => Users::roleList(),
		));
	}


	/**
	 * Форма подтверждения пользователя.
	 */
	public function actionConfirmationForm($id)
	{
		if (isset($_POST['Users'])) {
			$model = ($id) ? Users::getModel($id) : new Users;
			$model->setAttr($_POST['Users']);
			$model->confirmed = ($model->removed) ? 0 : 1;
			$model->blocked   = ($model->removed) ? 1 : 0;
			if ($model->validate()) {
				$model->save();
			}
			// Проверить ошибоки валидации и вывести в формате JSON: если успех - удачно записанные данные, если ошибка - ошибки.
			$this->checkErrorsAndDisplayResult($model);
		} else {
			$model = Users::getModel($id);
			$model->blocked = 0;
			$this->renderJson('_confirmation_form', array(
				'model'           => $model,
				'formTitle'       => $model->last_name.' '.$model->first_name.' '.$model->middle_name,
				'foreign_dept_groupList' => ForeignDeptGroup::dropDownList(),
			));
		}
	}


	/**
	 * Поиск в таблице.
	 */
	public function actionSearch()
	{
		$my_search = Users::my_search($_POST);
		
		$this->renderJson('application.modules.users.views.user._index_rows', array(
			'data'        => $my_search['results'],
			'pagerInfo'   => SearchHelper::getPagerInfo($_POST, $my_search['count']),
			'search'      => $_POST,
		));
	}
}
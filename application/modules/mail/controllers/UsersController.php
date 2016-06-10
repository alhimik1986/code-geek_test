<?php

/**
 * Управление данными пользователей для отправки почты.
 * Внимание! Редактирующий может изменить и другие параметры пользователя при наличии соответствующих полей!
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.mail.controllers
 */
class UsersController extends Controller
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
			'index'  => $both,
			'form'   => Yii::app()->request->isPostRequest ? Users::$ADMINS : $both,
			'search' => $both,
		);
	}


	/**
	 * Список пользователей для заполнения их учетной записи для отправки почты.
	 */
	public function actionIndex()
	{
		$this->render('index', array('model'=>new Users, 'roleList'=>Users::roleList()));
	}


	/**
	 * Форма создания и редактирования пользователя.
	 */
	public function actionForm($id=false)
	{
		$model = ($id) ? Users::getModel($id) : new Users;
		if (isset($_POST['Users'])) {
			$model->setAttrAndSave($_POST['Users']);
			// Проверить ошибоки валидации и вывести в формате JSON: если успех - удачно записанные данные, если ошибка - ошибки.
			$this->checkErrorsAndDisplayResult($model);
		} else {
			$this->renderJson('_form', array(
				'model'     => $model,
				'formTitle' =>$model->last_name.' '.$model->first_name.' '.$model->middle_name,
			));
		}
	}


	/**
	 * Поиск в таблице.
	 */
	public function actionSearch()
	{
		$my_search = Users::my_search($_POST);
		
		$this->renderJson('_index_rows', array(
			'data'        => $my_search['results'],
			'pagerInfo'   => SearchHelper::getPagerInfo($_POST, $my_search['count']),
			'search'      => $_POST,
		));
	}
}
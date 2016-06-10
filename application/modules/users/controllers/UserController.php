<?php
/**
 * Управление пользователями
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.users.controllers
 */

class UserController extends Controller
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
			'index'   => $both,
			'search'  => $both,
			'form'    => Yii::app()->request->isPostRequest ? Users::$ADMINS : $both,
			'delete'  => array(Users::SUPER_ADMIN),
			'login'   => Users::$ALL,
			'logout'  => Users::$ALL,
			'captcha' => Users::$ALL,
			'captcha1'=> Users::$ALL,
		);
	}


	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=> 0x91989a,
				//'foreColor'=>0xFFFFFF,
				'width'=>'120',
				'height'=>'50',
				'testLimit'=>'1', // Число попыток.
			),
			'captcha1'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=> 0xE4E7FA,
				//'foreColor'=>0xFFFFFF,
				'width'=>'120',
				'height'=>'50',
				'testLimit'=>'1', // Число попыток.
			),
		);
	}


	/**
	 * Список всех пользователей.
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
				'formTitle' => $model->isNewRecord ? Yii::t('users.app', 'New user') : $model->last_name.' '.$model->first_name.' '.$model->middle_name,
			));
		}
	}


	/**
	 * Удалить запись.
	 */
	public function actionDelete()
	{
		if (isset($_POST['Users']) AND isset($_POST['Users']['id'])) {
			$model = Users::getModel($_POST['Users']['id']);
			$model->delete();
			$this->checkErrorsAndDisplayResult($model);
		}
	}


	/**
	 * Страница входа
	 */
	public function actionLogin()
	{
		$form=new LoginForm;
		$recovery = new RecoveryForm;
		
		if(isset($_POST['LoginForm'])) {
			$form->attributes=$_POST['LoginForm'];
			
			if ( $form->validate() AND $form->login() ) {
				$form->username = '5';
				$form->password = Users::getUrlForRedirectingUser();
			}
			
			$this->checkErrorsAndDisplayResult($form);
		} else {
			$this->render('login',array('model'=>$form, 'recovery'=>$recovery));
		}
	}


	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect('login');
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
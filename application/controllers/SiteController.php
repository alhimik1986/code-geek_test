<?php
/**
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.controllers
 */
class SiteController extends Controller
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
			'page'     => Users::$ALL,
			'error'    => Users::$ALL,
			'index'    => Users::$ALL,
		);
	}
	
	
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}
	
	
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest) {
				if ( ! in_array($error['code'], array(404, 403)))
					echo '<div class="format"><h2>Внутренняя ошибка сервера!</h2></div>'.$error['message'];
				else
					echo '<div class="format"></div>'.$error['message'];
				if (defined('YII_DEBUG') AND YII_DEBUG) {
					echo '<br>'.'<br>'.$error['file']. '('.$error['line'].')'.'<br><br>';
					echo '<pre>'.$error['trace'].'</pre>';
				}
			} else {
				$view = Yii::getPathOfAlias('application.views.site.errors').DIRECTORY_SEPARATOR.'error'.$error['code'].'.php';
				if (file_exists($view) AND ! YII_DEBUG)
				{
					$this->render('errors/error'.$error['code'], array('data'=>array(
						'error'   => $error,
						'message' => $error['message'],
						'code'    => $error['code'],
						'time'    => new DateTime(),
						'version' => '',//'Yii '.Yii::getVersion(),
						'admin'   => '',//Yii::app()->params['adminEmail'],
					)));
				}
				else
				{
					$this->render('errors/error', $error);
				}
			}
			
			if (property_exists(Yii::app()->user, 'param') AND isset(Yii::app()->user->param['id'])) {
				$user = Yii::app()->user->param;
				$roles = Users::roleList(); $role = isset($roles[$user['role']]) ? $roles[$user['role']] : 'role='.$user['role'];
				$message = 'User: #'.$user['id'].' '.$user['FIO'].' ('.$role.')';
				Yii::log($message, $level=CLogger::LEVEL_ERROR);
			}
		}
	}
	
	
	/**
	 * Перенаправление с главной страницы.
	 */
	public function actionIndex()
	{
		$this->redirect(Users::getUrlForRedirectingUser());
	}
	
}
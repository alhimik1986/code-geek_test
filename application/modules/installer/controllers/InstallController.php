<?php
/**
 * Управление установкой и удалением приложения приложения.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.Install.controllers
 */

class InstallController extends Controller
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
			'index'       => array(Users::SUPER_ADMIN),
			'uninstall'   => array(Users::SUPER_ADMIN),
			'reinstall'   => array(Users::SUPER_ADMIN),
			'install'     => array(Users::SUPER_ADMIN),
			'import'      => array(Users::SUPER_ADMIN),
		);
	}
	public function beforeAction($action)
	{
		if ( ! Yii::app()->settings->param['installer']['allow']) {
			throw new CHttpException('401', Yii::t('installer.app', 'Installing the application is prohibited! Please set the parameter "[installer] [allow] = 1" in the system settings.'));
		}
		return parent::beforeAction($action);
	}

	public function actionIndex()
	{
		if (Yii::app()->user->role != Users::SUPER_ADMIN) throw new CHttpException(403, Yii::t('app', 'Access is denied!'));
		echo Installer::createTables() ? 'installed' : 'error';
		$this->import();
	}

	public function actionInstall()
	{
		if (Yii::app()->user->role != Users::SUPER_ADMIN) throw new CHttpException(403, Yii::t('app', 'Access is denied!'));
		echo Installer::createTables() ? 'installed' : 'error';
	}

	public function actionUninstall()
	{
		if (Yii::app()->user->role != Users::SUPER_ADMIN) throw new CHttpException(403, Yii::t('app', 'Access is denied!'));
		echo Installer::dropTables() ? 'uninstalled' : 'error';
	}


	public function actionReinstall()
	{
		if (Yii::app()->user->role != Users::SUPER_ADMIN) throw new CHttpException(403, Yii::t('app', 'Access is denied!'));
		echo Installer::dropTables() ? 'uninstalled' : 'error'; echo '<br>';
		echo Installer::createTables() ? 'installed' : 'error'; echo '<br>';
		$this->import();
	}

	public function actionImport()
	{
		if (Yii::app()->user->role != Users::SUPER_ADMIN) throw new CHttpException(403, Yii::t('app', 'Access is denied!'));
		$this->import();
		echo 'imported';
	}

	protected function import()
	{
		if ( Yii::app()->settings->param['installer']['import']) {
			Importer::import();
		}
	}
}
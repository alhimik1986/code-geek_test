<?php
/**
 * Контроллер Setting.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.modules.settings.controllers
 */
class SettingController extends Controller
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
			'index'      => array(Users::SUPER_ADMIN),
			'form'       => array(Users::SUPER_ADMIN),
			'rows'       => array(Users::SUPER_ADMIN),
			'flushAll'   => array(Users::SUPER_ADMIN),
			'flushCache' => array(Users::SUPER_ADMIN),
			'blockSite'  => array(Users::SUPER_ADMIN),
			'siteBlocked'=> array(Users::SUPER_ADMIN),
		);
	}


	/**
	 * Список всех настроек.
	 */
	public function actionIndex()
	{
		$this->render('index', array(
			'data'=>SettingsModel::getSettings(),
			'model' => new SettingsModel,
		));
	}


	/**
	 * Форма создания и редактирования.
	 */
	public function actionForm($id)
	{
		if (isset($_POST['SettingsModel'])) {
			$model = SettingsModel::getModel($id);
			$model->setAttrAndSave($_POST);
			// Проверить ошибоки валидации и вывести в формате JSON: если успех - удачно записанные данные, если ошибка - ошибки.
			$this->checkErrorsAndDisplayResult($model);
		} else {
			$model = SettingsModel::getModel($id);
			$this->renderJson('_form', array(
				'model'     => $model,
				'formTitle' => $model->name,
			));
		}
	}


	/**
	 * Список всех строк для обновления таблицы.
	 */
	public function actionRows()
	{
		$this->renderJson('_rows', array(
			'data' => SettingsModel::getSettings(),
		));
	}


	/**
	 * Очистить кэш и удалить сжатые css- и js-файлы, чтобы создать их заново;
	 * Очистка папки assets, debug и фалов журнала (application.log)
	 */
	public function actionFlushAll()
	{
		Yii::app()->cache->flush();
		JsCssFiles::flush();
		
		// Очистка папки assets, debug и фалов журнала (application.log)
		Yii::import('application.commands.ClearAssetsCommand');
		$c = new ClearAssetsCommand('', new CConsoleCommandRunner);
		$c->run(array());
		
		$this->render('flush');
	}


	/**
	 * Очистить кэш.
	 */
	public function actionFlushCache()
	{
		Yii::app()->cache->flush();
		$this->render('flush');
	}


	/**
	 * Блокировка сайта.
	 */
	public function actionBlockSite()
	{
		$model = SettingsModel::getModel('Site');
		if (isset($_POST['Site'])) {
			$post = array('value'=>$model->value);
			
			if (isset($_POST['Site']['blocked']))
				$post['value']['blocked'] = (int)(bool)$_POST['Site']['blocked'];
			if (isset($_POST['Site']['blockedReason']))
				$post['value']['blockedReason'] = (string)$_POST['Site']['blockedReason'];
			$className = SettingsModel::className();
			$post = array($className => $post);
			$model->setAttrAndSave($post);
			
			// Проверить ошибоки валидации и вывести в формате JSON: если успех - удачно записанные данные, если ошибка - ошибки.
			$this->checkErrorsAndDisplayResult($model);
		} else {
			$this->render('blockSite', array('data'=>$model->value));
		}
	}


	/**
	 * Обраазец страницы заблокированного сайта.
	 */
	public function actionSiteBlocked()
	{
		$setting = Yii::app()->settings->param['Site'];
		$this->renderPartial('application.views.site.blocked.index', $setting);
	}
}
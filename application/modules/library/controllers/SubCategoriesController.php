<?php

class SubCategoriesController extends Controller
{
	/**
	 * Фильтр: контроль доступа по ролям.
	 */
    public function filters()
    {
        return array(
            array('application.controllers.filters.AccessControl'),
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
		);
	}


	/**
	 * Список всех записей.
	 */
	public function actionIndex()
	{
		$this->render('index', array(
			'model'=>new SubCategories,
		));
	}


	/**
	 * Форма создания и редактирования.
	 */
	public function actionForm($id=false)
	{
		$model = $id ? SubCategories::getModel($id) : new SubCategories;
		if (isset($_POST[$model::className()])) {
			$model->setAttrAndSave($_POST[$model::className()]);
			// Проверить ошибоки валидации и вывести в формате JSON: если успех - удачно записанные данные, если ошибка - ошибки.
			$this->checkErrorsAndDisplayResult($model);
		} else {
			$this->renderJson('_form', array('model' => $model, 'formTitle'=>$id ? Yii::t('app', 'Edit record') : Yii::t('app', 'New record')));
		}
	}


	/**
	 * Поиск в таблице.
	 */
	public function actionSearch()
	{
		$my_search = SubCategories::my_search($_POST);
		
		$this->renderJson('_index_rows', array(
			'data'        => $my_search['results'],
			'pagerInfo'   => SearchHelper::getPagerInfo($_POST, $my_search['count']),
			'search'      => $_POST,
		));
	}


	/**
	 * Удалить запись.
	 */
	public function actionDelete()
	{
		if (isset($_POST['SubCategories']['id'])) {
			$model = SubCategories::getModel($_POST['SubCategories']['id']);
			$model->delete();
			$this->checkErrorsAndDisplayResult($model);
		}
	}
}
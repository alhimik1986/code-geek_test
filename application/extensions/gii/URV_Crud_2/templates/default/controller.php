<?php
/**
 * This is the template for generating a controller class file for CRUD feature.
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>

class <?php echo $this->controllerClass; ?> extends <?php echo $this->baseControllerClass."\n"; ?>
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
			'form'    => Yii::app()->request->isPostRequest ? Users::$ADMINS : $both,
			'row'    => Users::$ADMINS,
			'table'  => Users::$ADMINS,
			'delete'  => array(Users::SUPER_ADMIN),
		);
	}


	/**
	 * Список всех записей.
	 */
	public function actionIndex()
	{
		$this->render('index', array(
			'model'=>new <?php echo $this->modelClass; ?>,
		));
	}


	/**
	 * Форма создания и редактирования.
	 */
	public function actionForm($id=false)
	{
		$model = $id ? <?php echo $this->modelClass; ?>::getModel($id) : new <?php echo $this->modelClass; ?><?php echo empty($this->scenario) ? '' : "('{$this->scenario}')"; ?>;
		if (isset($_POST[$model::className()])) {
			$model->setAttrAndSave($_POST[$model::className()]);
			// Проверить ошибоки валидации и вывести в формате JSON: если успех - удачно записанные данные, если ошибка - ошибки.
			$this->checkErrorsAndDisplayResult($model);
		} else {
			$this->renderJson('_form', array('model' => $model, 'formTitle'=>$id ? Yii::t('app', 'Edit record') : Yii::t('app', 'New record')));
		}
	}


	/**
	 * Строка таблицы, которую нужно обновить после редактирования.
	 */
	public function actionRow($id, $count)
	{
		$this->renderJson('_index_rows', array(
			'data' => <?php echo $this->modelClass; ?>::getArrayByIds(array($id)),
			'count' => $count,
		));
	}


	/**
	 * Вся таблица записей, чтобы обновить список записей после добавления или удаления.
	 */
	public function actionTable()
	{
		$this->renderJson('_index_rows', array('data' => <?php echo $this->modelClass; ?>::getArrayByArray(array())));
	}


	/**
	 * Удалить запись.
	 */
	public function actionDelete()
	{
		if (isset($_POST['<?php echo $this->modelClass; ?>']['<?php echo $this->tableSchema->primaryKey; ?>'])) {
			$model = <?php echo $this->modelClass; ?>::getModel($_POST['<?php echo $this->modelClass; ?>']['<?php echo $this->tableSchema->primaryKey; ?>']);
			$model->delete();
			$this->checkErrorsAndDisplayResult($model);
		}
	}
}

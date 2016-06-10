<?php

class BooksController extends Controller
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
			'file'    => Users::$ALL,
			'viewFile'=> Users::$ALL,
			'viewPhoto'=> Users::$ALL,
			'removeFile'=> Users::$ADMINS,
			'replaceToCategory' => Users::$ADMINS,
		);
	}


	/**
	 * Список всех записей.
	 */
	public function actionIndex()
	{
		$this->render('index', array(
			'model'=>new Books,
		));
	}


	/**
	 * Форма создания и редактирования.
	 */
	public function actionForm($id=false)
	{
		$model = $id ? Books::getModel($id) : new Books;
		if (isset($_POST[$model::className()])) {
			if ( ! empty($_POST[$model::className()]['validate'])) {
				$model->setAttr($_POST[$model::className()]);
				$model->validate();
			} else {
				$model->setAttrAndSave($_POST[$model::className()]);
			}
			if ($_FILES) {
				$className = lcfirst($model::className());
				echo "
				<script type=\"text/javascript\">
					window.top.window.remove_form_and_search();
				</script>
				";
			} else {
				// Проверить ошибоки валидации и вывести в формате JSON: если успех - удачно записанные данные, если ошибка - ошибки.
				$this->checkErrorsAndDisplayResult($model);
			}
		} else {
			$this->renderJson('_form', array(
				'model' => $model,
				'formTitle'=>$id ? Yii::t('app', 'Edit record') : Yii::t('app', 'New record'),
				'files' => BooksFiles::getArrayByArray(array('book_id'=>$model->id)),
			));
		}
	}


	/**
	 * Поиск в таблице.
	 */
	public function actionSearch()
	{
		$my_search = Books::my_search($_POST);
		
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
		if (isset($_POST['Books']['id'])) {
			$model = Books::getModel($_POST['Books']['id']);
			$model->delete();
			$this->checkErrorsAndDisplayResult($model);
		}
	}


	/**
	 * Скачать прикрепленный файл.
	 */
	public function actionFile($id)
	{
		$file = BooksFiles::getFile((int)$id);
		$book = DownloadMailNotifier::getBook($id);
		if ($file AND $book) {
			$model = DownloadMailNotifier::notify($file, $book);
			if ($model->hasErrors())
				$this->checkErrorsAndDisplayResult($model);
			
			// http://habrahabr.ru/post/151795/
			if (ob_get_level()) { // если этого не сделать файл будет читаться в память полностью!
				ob_end_clean();
			}
			//header('Content-Type: text/html; charset=windows-1251');
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $file['name'].'"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . strlen($file['file']));
			echo $file['file'];
			Yii::app()->end();
		}
	}


	/**
	 * Просмотреть прикрепленный файл.
	 */
	public function actionViewFile($id)
	{
		$file = BooksFiles::getFile((int)$id);
		if ($file) {
			// http://habrahabr.ru/post/151795/
			if (ob_get_level()) { // если этого не сделать файл будет читаться в память полностью!
				ob_end_clean();
			}
			header('Content-Type: image/jpeg');
			echo $file['file'];
			Yii::app()->end();
		}
	}


	/**
	 * Просмотреть файл обложки.
	 */
	public function actionViewPhoto($id)
	{
		$fileContent = Books::getFile((int)$id);
		if ($fileContent) {
			// http://habrahabr.ru/post/151795/
			if (ob_get_level()) { // если этого не сделать файл будет читаться в память полностью!
				ob_end_clean();
			}
			header('Content-Type: image/jpeg');
			echo $fileContent;
			Yii::app()->end();
		}
	}


	/**
	 * Удалить прикрепленный файл.
	 */
	public function actionRemoveFile()
	{
		$className = BooksFiles::className();
		if (isset($_POST[$className]['id']) AND isset($_POST[$className]['book_id'])) {
			$model = $className::getModel($_POST[$className]['id']);
			if ( ! $model)
				throw new CHttpException('404', 'Удаляемый файл не найден.');
			$model->delete();
			if ($model->hasErrors()) {
				$this->checkErrorsAndDisplayResult($model);
			} else {
				$this->renderJson('_index_files_rows', array(
					'files' => $className::getArrayByArray(array('book_id'=>$_POST[$className]['book_id'])),
				));
			}
		}
	}


	/**
	 * Переместить назначенные книги в заданную категорию.
	 */
	public function actionReplaceToCategory()
	{
		$sub_category_id = isset($_POST['sub_category_id']) ? (int)$_POST['sub_category_id'] : 0;
		$ids = isset($_POST['ids']) ? $_POST['ids'] : array();
		ArrayHelper::validateIds($ids, $errorMessage='Неправильно задан список книг.', $throwException=false);
		if ( ! $sub_category_id OR ! $ids)
			throw new CHttpException('404', 'Не заданы книги или категория.');
		$subCategory = SubCategories::getArrayById($sub_category_id);
		$category_id = $subCategory ? $subCategory['category_id'] : 0;
		
		Books::db()->createCommand()
			->update(Books::table(), array('sub_category_id'=>$sub_category_id, 'category_id'=>$category_id), SearchHelper::inCondition($ids, 'id'));
		
		$this->echoJson('ok');
	}
}
<?php

class LibraryController extends Controller
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
		return array(
			'index'   => Users::$ALL,
			'search'  => Users::$ALL,
			'view'    => Users::$ALL,
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
	 * Просмотр книги.
	 */
	public function actionView($id)
	{
		$book = Books::getArrayById($id);
		if ( ! $book)
			throw new CHttpException('404', 'Книга не найдена!');
		
		$_files = BooksFiles::getFileNames(array($book['id']));
		$files = isset($_files[$book['id']]) ? $_files[$book['id']] : array();
		
		$category = Categories::getArrayById($book['category_id']);
		$category = $category ? $category['name'] : '';
		$subCategory = SubCategories::getArrayById($book['sub_category_id']);
		$subCategory = $subCategory ? $subCategory['name'] : '';
		
		$this->renderJson('_view', array('book'=>$book, 'files'=>$files, 'category'=>$category, 'subCategory'=>$subCategory));
	}
}
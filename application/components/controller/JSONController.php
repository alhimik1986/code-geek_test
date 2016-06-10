<?php
/**
 * Имеет методы для работы с ajaxForm.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.components
 */
class JSONController extends CController
{
	/**
	 *  Проверить ошибоки валидации и вывести в формате JSON: если успех - удачно записанные данные, если ошибка - ошибки.
	 */
	public function checkErrorsAndDisplayResult($model)
	{
		if(Yii::app()->request->isAjaxRequest) {
			$messages = array();
			foreach(Yii::app()->user->getFlashes() as $type=>$message) $messages[][$type] = $message;
			if ($model->hasErrors()) {
				echo CJSON::encode(array(
					'status'   => 'error',
					'content'  => array($model::className() => $model->getErrors()),
					'messages' => $messages,
				));
			} else {
				echo CJSON::encode(array(
					'status'   => 'success',
					'content'  => array($model::className() => $model->attributes),
					'messages' => $messages,
				));
			}
		} else {
			if ($model->hasErrors()) {
				foreach($model->getErrors() as $key=>$errors) {
					foreach($errors as $error) {
						Yii::app()->user->setFlash('error', $model->getAttributeLabel($key).': '.$error);
					}
				}
			}
			$this->redirect(Yii::app()->request->urlReferrer);
		}
	}
	
	
	/**
	 * Вывести данные в формате JSON
	 */
	public function renderJson($view, $params=array())
	{
		if(Yii::app()->request->isAjaxRequest) {
			$messages = array();
			$content = $this->renderPartial($view, $params, true);
			foreach(Yii::app()->user->getFlashes() as $type=>$message) $messages[][$type] = $message;
			echo json_encode(array(
				'status'   => 'success',
				'content'  => $content,
				'messages' => $messages,
			));
		} else {
			$this->renderPartial($view, $params);
		}
	}
	
	
	/**
	 * Вывести ошибку в формате JSON
	 */
	public function renderJsonError($view, $params=array())
	{
		if(Yii::app()->request->isAjaxRequest) {
			$messages = array();
			$content = array(array(array($this->renderPartial($view, $params, true))));
			foreach(Yii::app()->user->getFlashes() as $type=>$message) $messages[][$type] = $message;
			echo CJSON::encode(array(
				'status'   => 'error',
				'content'  => $content,
				'messages' => $messages,
			));
		} else {
			$this->renderPartial($view, $params);
		}
	}
	
	
	
	
	/**
	 * Вывести текст в формате JSON
	 */
	public function echoJson($text)
	{
		if(Yii::app()->request->isAjaxRequest) {
			$messages = array();
			foreach(Yii::app()->user->getFlashes() as $type=>$message) $messages[][$type] = $message;
			echo CJSON::encode(array(
				'status'   => 'success',
				'content'  => $text,
				'messages' => $messages,
			));
		} else {
			echo $text;
		}
	}
	
	
	/**
	 * Вывести ошибку в формате JSON
	 * @param string $text Текст ошибки.
	 * @param boolean $throwException Бросать исключение (завершать приложение) при выводе сообщения.
	 * @param integer $errorCode Код ошибки (если не ajax-сообщение).
	 */
	public function echoJsonError($text='', $throwException=false, $errorCode='')
	{
		if(Yii::app()->request->isAjaxRequest) {
			$text = ($text != '') ? array(array(array($text))) : '';
			$messages = array();
			foreach(Yii::app()->user->getFlashes() as $type=>$message) $messages[][$type] = $message;
			echo CJSON::encode(array(
				'status'   => 'error',
				'content'  => $text,
				'messages' => $messages,
			));

			if ($throwException)
				Yii::app()->end();
		} else {
			if ($throwException)
				throw new CHttpException($errorCode, $text);
			else
				echo $text;
		}
	}
}
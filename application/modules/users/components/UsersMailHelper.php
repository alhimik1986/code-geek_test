<?php
/**
 * Вспомогательный класс для обработки почтовых данных пользователей.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2016
 * @package application.modules.users.models
 */

class UsersMailHelper
{
	const PHPMailerHelper    = 0;
	const ExchangeMailHelper = 1;


	/**
	 * @return array Список классов почтового менеджера.
	 */
	public static function email_class_list()
	{
		return array(
			self::PHPMailerHelper    => 'PHPMailerHelper',
			self::ExchangeMailHelper => 'ExchangeMailHelper',
		);
	}


	/**
	 * @return array Список классов почтового менеджера.
	 */
	public static function email_class_label()
	{
		return array(
			self::PHPMailerHelper    => Yii::t('users.app', 'Default'),
			self::ExchangeMailHelper => Yii::t('users.app', 'Exchange'),
		);
	}
}
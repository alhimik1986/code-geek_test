<?php
/**
 * Вспомогательный класс для работы с ролями. Наследуется классом Users.
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2016
 * @package application.modules.users.models
 */

class UsersRolesHelper extends Model
{
	// Роли для пользователей или группы пользователей
	const SUPER_ADMIN            = 1;   // Супер Администратор
	const ADMIN                  = 2;   // Администратор
	const SUPERVISOR             = 10;  // Наблюдатель (может просматривать все страницы, но не может ничего редактировать)
	const GUEST                  = 100; // Гость (неавторизованный пользователь)

	// Группа ролей: все пользователи
	public static $ALL = array(
		self::SUPER_ADMIN,
		self::ADMIN,
		self::SUPERVISOR,
		self::GUEST,
	);
	// Группа ролей: авторизованные пользователи
	public static $AUTHENTICATED = array(
		self::SUPER_ADMIN,
		self::SUPERVISOR,
		self::ADMIN,
	);
	// Группа ролей: администраторы
	public static $ADMINS = array(
		self::SUPER_ADMIN,
		self::ADMIN,
	);


	/**
	 * @return array Список всех групп ролей.
	 */
	public static function groupList()
	{
		return array(
			'ALL'           => 'ALL',
			'AUTHENTICATED' => 'AUTHENTICATED',
			'ADMINS'        => 'ADMINS',
		);
	}


	/**
	 * @return Список всех ролей авторизованных пользователей.
	 */
	public static function roleList()
	{
		return array(
			self::SUPER_ADMIN => Yii::t('users.app', 'Super administrator'),
			self::ADMIN       => Yii::t('users.app', 'Administrator'),
			self::SUPERVISOR  => Yii::t('users.app', 'Supervisor'),
		);
	}


	// Список ролей для формы создания и редактирования пользователей (чтобы можно было создавать те роли, которые видишь)
	public static function roleListForForm()
	{
		return array(
			self::ADMIN => Yii::t('users.app', 'Administrator'),
		);
	}


	/**
	 * @return адрес, на который перенаправлять авторизованного пользователя.
	 */
	public static function getUrlForRedirectingUser()
	{
		$user = UserIdentity::loadUser();
		// Устанавливаю роли в компоненте пользователя (Yii::app()->user), точнее, уже авторизованного пользователя
		$ac = new AccessControl();
		$ac->_setUserRoles($user);
		
		$inGroup = Yii::app()->user->inGroup;
		
		if ($inGroup['ADMINS']) {
			return Yii::app()->getController()->createUrl('//settings/setting/index');
		} else {
			return Yii::app()->getController()->createUrl('//library/library/index');
		}
	}


	/**
	 * @return array Список всех пользователей по указанным ID в данных.
	 * @param array $user_ids Список ID сотрудников.
	 */
	public static function getArrayByIdsWithSuperAdmin($user_ids)
	{
		$users = static::getArrayByIds($user_ids);
		$users[0] = Yii::app()->settings->param['superadmin'];
		
		foreach($users as $id=>$value) {
			$value['FIO'] = $value['last_name'].' '.$value['first_name'].' '.$value['middle_name'];
			$value['fio'] = $value['last_name'].' '.iconv_substr($value['first_name'], 0, 1, 'utf-8').'.'.iconv_substr($value['middle_name'], 0, 1, 'utf-8').'.';
			$users[$id] = $value;
		}
		return $users;
	}
}
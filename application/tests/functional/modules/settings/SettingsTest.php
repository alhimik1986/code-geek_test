<?php
require_once('/../users/helpers/LoginHelper.php');
require_once('/../users/helpers/UserHelper.php');

/**
 * Тест работоспособности системных настроек.
 */
class SettingsTest extends WebTestCase
{
	const INDEX_URL = '/index-test.php/settings/setting/index';
	const FLUSH_CACHE_URL = '/index-test.php/settings/setting/flushCache';
	const FLUSH_ALL_URL = '/index-test.php/settings/setting/flushAll';
	public static $urls = array(
		'/index-test.php/settings/setting/index',
		'/index-test.php/settings/setting/form',
		'/index-test.php/settings/setting/table',
		'/index-test.php/settings/setting/flushCache',
		'/index-test.php/settings/setting/flushAll',
	);

	// Проверяю доступ по ролям.
	public function testAccess()
	{
		$s = $this;
		$users = UserHelper::createUsers($s);
		LoginHelper::logout($s);
		foreach(UserHelper::$allRoles as $role) {
			LoginHelper::login($s, $users[$role]['username'], $users[$role]['password']);
			foreach(self::$urls as $url) {
				$s->open($url);
				if ( ! in_array($role, array(Users::SUPER_ADMIN))) {
					$s->assertTextPresent('Доступ запрещен');
				} else {
					$s->assertTextNotPresent('Доступ запрещен');
				}
			}
		}

		// Доступ анонимным пользователям
		LoginHelper::logout($s);
		foreach(self::$urls as $url) {
			$s->open($url);
			$s->assertElementPresent('id=LoginForm_username');
		}

		// Доступ системного супер админа
		LoginHelper::login($s, LoginHelper::$username, LoginHelper::$password);
		foreach(self::$urls as $url) {
			$s->open($url);
			$s->assertTextNotPresent('Доступ запрещен');
		}
	}
	

	// Пробую сохранить (отредактировать) кэш в настройках.
	public function testSettings()
	{
		$s = $this;
		LoginHelper::logout($s);

		$origValue = Yii::app()->settings->param['cache']['enabled'];
		LoginHelper::login($s, LoginHelper::$username, LoginHelper::$password);
		$s->open(self::INDEX_URL);
		$s->doubleClick('//table[@id="urv-table"]/tbody/tr[contains("cache", "")]');
		$s->waitForElementPresent('id=urv-form');
		if ($s->getBrowser('*iexplore')) sleep(1);
		$s->type('id=SettingsModel_value_enabled', $value=time());
		$s->click('id=urv-form-button-submit');
		$s->waitForElementNotPresent('id=urv-form');
		$s->assertTextPresent($value);

		// Возращаю в исходное положение
		$s->doubleClick('css=#urv-table tbody tr:contains("cache")');
		$s->waitForElementPresent('id=urv-form');
		$s->type('id=SettingsModel_value_enabled', $origValue);
		$s->click('id=urv-form-button-submit');
		$s->waitForElementNotPresent('id=urv-form');
		$s->assertElementPresent('//table[@id="urv-table"]/tbody/tr[contains("cache", "")]/td[contains("'.$origValue.'", "")]');

		// Проверяю еще работоспособность, но без сохранения.
		$s->doubleClick('css=#urv-table tbody tr:contains("dbEmployees")');
		$s->waitForElementPresent('id=urv-form');
		$s->assertElementPresent('//*[@id="urv-form"]/*/*/*[4]/input[@type="text"]');
		$s->click('id=urv-form-button-cancel');
		$s->waitForElementNotPresent('id=urv-form');
	}
	
	
	//Тестирую очистку кэша.
	public function testFlush()
	{
		$s = $this;
		LoginHelper::login($s, LoginHelper::$username, LoginHelper::$password);
		$s->open(self::FLUSH_CACHE_URL);
		$s->assertTextPresent('Кэш очищен');
		$s->open(self::FLUSH_ALL_URL);
		$s->assertTextPresent('Кэш очищен');
	}
}
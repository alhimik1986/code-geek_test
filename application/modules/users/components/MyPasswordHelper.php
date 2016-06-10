<?php
/**
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2016
 * @package application.modules.users.components
 */
class MyPasswordHelper
{
	protected static $salt='u*-$v№5/';    // Соль для шифрования пароля


	/**
	 *
	 */
	public static function combination($registration_date, $password)
	{
		return $registration_date.self::$salt.$password;
	}


	/**
	 * Шифрую пароль.
	 */
	public static function hashPassword($registration_date, $password)
	{
		//return md5(self::combination($registration_date, $password)); // 0 ms
		return CPasswordHelper::hashPassword(self::combination($registration_date, $password), 4); // 25 ms
	}


	/**
	 * Проверяю пароль.
	 */
	public static function validatePassword($registration_date, $password, $hash)
	{
		//return (self::hashPassword($registration_date, $password) === $hash);
		return CPasswordHelper::verifyPassword(self::combination($registration_date, $password), $hash);
	}


	/**
	 * @return string Зашифрованную строку.
	 * @param string $pure_string Строка, которую нужно зашифровать.
	 * @param string $secret_key Ключ шифрования.
	 * http://stackoverflow.com/a/16606352
	 */
	public static function encrypt_string($string, $secret_key) {
		$iv = substr(hash('sha256', self::$salt), 0, 16);
		$key = hash('sha256', $secret_key);
		$output = openssl_encrypt($string, 'AES-256-CBC', $key, 0, $iv);
        $output = base64_encode($output);
		
		return $output;
	}


	/**
	 * @return string Расшифрованную строку.
	 * @param string $encrypted_string Зашифрованная строка.
	 * @param string $secret_key Ключ шифрования.
	 */
	public static function decrypt_string($string, $secret_key) {
		$iv = substr(hash('sha256', self::$salt), 0, 16);
		$key = hash('sha256', $secret_key);
		$result = openssl_decrypt(base64_decode($string), 'AES-256-CBC', $key, 0, $iv);
		
		return $result;
	}
}
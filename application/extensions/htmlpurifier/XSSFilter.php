<?php
/**
 * Чистка html от XSS-атак.
 * Использование: вставить в валидацию модели следующие строчки:
 * array('name, comment', 'application.extensions.htmlpurifier.XSSFilter'), // Очищаю содержимое от XSS-атак
 */
class XSSFilter extends CValidator
{
	public $allowEmpty = true;
	
	protected static $purifier;
	
	protected static function createPurifier()
	{
		if ( ! self::$purifier) {
			self::$purifier = new CHtmlPurifier();
			self::$purifier->options = array(
				'CSS.AllowedProperties'        => array('background-color'),
				//'HTML.Allowed'                 => 'div[align],br,p,b,i,u,ol,ul,li,sub,sup,strike,blockquote',
				'HTML.SafeObject'              => true,
				'HTML.TidyLevel'               => 'medium',
				'URI.DisableExternalResources' => true,
			);
		}
	}


	protected function validateAttribute($model,$attribute)
	{
		$model->$attribute = self::prePurify($model->$attribute);
	}


	public function filterValue($value)
	{
		return self::prePurify($dirty_html);
	}
	
	
	public static function prePurify($string)
	{
		if (preg_match('/^\<div\>.*/iu', $string)) {
			$string = $string ? preg_replace('/^\<div\>/iu', '', $string) : $string;
			$string = $string ? preg_replace('/\<\/div\>$/iu', '', $string) : $string;
		}
		
		$dirty_html = $string 
			? strip_tags($string, '<br><p><b><i><u><ol><ul><li><div><sub><sup><strike><blockquote>')
			: $string;
		if ($dirty_html) {
			self::createPurifier();
			$dirty_html = self::$purifier->purify($dirty_html);
			$dirty_html = preg_replace('/^(<br \/>)+/', '', $dirty_html);
			$dirty_html = preg_replace('/(<br \/>)+$/', '', $dirty_html);
		}
		return $dirty_html;
	}
}
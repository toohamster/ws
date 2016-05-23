<?php namespace Ws;

abstract class Env
{

	private static $options = [];

	public static function is($key)
	{
		return isset(self::$options[$key]) ? self::$options[$key] : false;
	}

	public static function checking()
	{
		self::$options['cli']	= PHP_SAPI === 'cli';
		self::$options['win']	= strtolower(substr(php_uname("s"), 0, 3));
	}

	public static function strings($string)
	{
		if ( is_string($string) && self::$options['cli'] && self::$options['win'] )
		{
			return iconv("UTF-8", "GBK", $string);
		}
		return false;
	}

	/**
	 * 对字符串或数组进行格式化，返回格式化后的数组
	 *
	 * @param array|string $input 要格式化的字符串或数组
	 * @param string $delimiter 按照什么字符进行分割
	 *
	 * @return array 格式化结果
	 */
	public static function arr($input, $delimiter = ',')
	{
	    if (!is_array($input))
	    {
	        $input = explode($delimiter, $input);
	    }
	    $input = array_map('trim', $input);
	    return array_filter($input, 'strlen');
	}

}

Env::checking();
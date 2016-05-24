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
		self::$options['cliwin'] = self::$options['cli'] && self::$options['win'];
	}

	public static function strings($string)
	{
		if (is_string($string) && self::$options['cliwin'])
		{
			return iconv("UTF-8", "GBK", $string);
		}
		return false;
	}
	   
    function val($arr, $name, $default = null)
    {
        return isset($arr[$name]) ? $arr[$name] : $default;
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

	public static function dump($vars, $label = '', $return = false)
	{
		if ( self::$options['cli'] )
		{
			$content = print_r($vars,true);
			if ( $label != '' )
			{
				$content = "[$label]: " . $content;
			}
			$content = self::strings($content);

		    if ($return) { return $content; }
		    fwrite(STDOUT, $content . PHP_EOL);
		}
		else
		{
			$content = "<pre>\n";
		    if ($label != '') {
		        $content .= "<strong>{$label} :</strong>\n";
		    }
		    $content .= htmlspecialchars(print_r($vars, true),ENT_COMPAT | ENT_IGNORE);
		    $content .= "\n</pre>\n";

		    if ($return) { return $content; }
		    echo $content;
		}
	}

}

Env::checking();
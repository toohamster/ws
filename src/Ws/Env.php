<?php namespace Ws;

abstract class Env
{

	private static $options = [];

	public static function is($key)
	{
		return array_key_exists($key, self::$options) ? self::$options[$key] : false;
	}

	public static function detect()
	{
		self::$options['time']	= time();
		self::$options['microtime']	= microtime(true);
		self::$options['cli']	= PHP_SAPI === 'cli';
		self::$options['win']	= strtolower(substr(php_uname("s"), 0, 3));
		self::$options['cliwin'] = self::$options['cli'] && self::$options['win'];

		if (self::$options['cli'])
		{
			for ($i = 1; $i < $_SERVER['argc']; $i++)
			{
				$arg = explode('=', $_SERVER['argv'][$i]);
				if (count($arg) > 1 || strncmp($arg[0], '-', 1) === 0)
				{
					$_GET[ltrim($arg[0], '-')] = isset($arg[1]) ? $arg[1] : true;
				}
				$_REQUEST = array_merge($_REQUEST,$_GET);
			} 
		}
	}

	public static function cliString($string)
	{
		if (is_string($string) && self::$options['cliwin'])
		{
			return iconv("UTF-8", "GBK", $string);
		}
		return false;
	}
	   
    public static function val($arr, $name, $default = null)
    {
        return isset($arr[$name]) ? $arr[$name] : $default;
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
			$content = self::cliString($content);

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

Env::detect();
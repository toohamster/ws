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
		static $init = false;
		if ($init) return;

		self::$options['time']	= time();
		self::$options['microtime']	= microtime(true);
		self::$options['cli']	= PHP_SAPI === 'cli';
		self::$options['win']	= 'win' === strtolower(substr(php_uname("s"), 0, 3));
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

		$init = true;
	}

	/**
	 * 返回类加载器对象
	 * 
	 * @return \Composer\Autoload\ClassLoader
	 */
	public static function classLoader()
	{
		static $loader = null;
		if ( is_null($loader) )
		{
			$loader = new \Composer\Autoload\ClassLoader();
			$loader->register();
		}

		return $loader;
	}

	/**
	 * 返回终端字符串
	 * 
	 * @param  string $string
	 * @return string
	 */
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

    /**
	 * 输出一个变量的内容
	 *
	 * @param mixed $vars 要输出的变量
	 * @param string $label 输出变量时显示的标签
	 * @param boolean $return 是否返回输出内容
	 *
	 * @return string
	 */
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

	/**
	 * 判断变量是否
	 * 
	 * @param  \Closure  $closure 匿名函数
	 * 
	 * @return boolean
	 */
	public static function isClosure($closure)
	{
		return !empty($closure) && ($closure instanceof \Closure);
	}

	/**
	 * 获取回调函数对象
	 * 
	 * @param  mixed $closure 回调函数对象
	 * 
	 * @return array
	 */
	public static function getClosure($closure)
	{
		if (empty($closure)) return null;
		
		$closureType = null;

		if ( self::isClosure($closure) )
        {
            $closureType = 'closure';
        }
        else if (is_callable($closure))
        {
            $closureType = 'callable';
        }
        else if (is_string($closure))
        {
            $closure = \Ws\Helper\Arrays::normalize($closure, '@');
            // class, method
            $class = array_shift($closure);

            if (class_exists($class))
            {
                $method = array_shift($closure);
                if ( empty($method) ) $method = 'execute';

                if ( is_callable([$class, $method]) )
                {
                    $closureType = 'method';
                    $closure = [$class, $method];
                }
            }                       
        }

        if ( empty($closureType) )
        {
        	return null;
        }

        return ['type'=> $closureType, 'closure'=> $closure];
	}

	public static function getServerName()
    {
        $server_info = explode(' ', php_uname());
        $server_name = $server_info[0] == 'Windows' ? $server_info[2] : $server_info[1];
        if (empty($server_name)) {
            $server_name = '--';
        }

        $server_name = str_replace(' ', '-', $server_name);
        return strtolower(trim($server_name));
    }

	public static function fast_uuid($suffix_len = 3)
	{
	    //! 计算种子数的开始时间
	    static $being_timestamp = 1421833799;

	    $time = explode(' ', microtime());
	    $id = ($time[1] - $being_timestamp) . sprintf('%06u', substr($time[0], 2, 6));
	    if ($suffix_len > 0) {
	        $id .= substr(sprintf('%010u', mt_rand()), 0, $suffix_len);
	    }
	    return $id;
	}

	public static function identify($x)
	{
	    static $mask = '0123456789abcdefghijklmnopqrstuvwxyz';
	    $x = sprintf("%u", crc32($x));

	    $m = '';
	    while ($x > 0) {
	        $s = $x % 36;
	        $m .= $mask[$s];
	        $x = floor($x / 36);
	    }
	    return $m;
	}

}

Env::detect();
<?php namespace Ws\Mvc;

use Ws\Env;

class Container
{

	/**
	 * 应用设置对象
	 * 
	 * @var Config
	 */
	public static $config;

	public static function init($options=[])
	{
		static $noinit = true;
		if ($noinit)
		{			
			// 载入 框架默认值
			$config = new Config( require(__DIR__ . '/__defaults.php') );
			$config->import($options);

			$timezone = $config->get('defaults.timezone', 'Asia/Chongqing');
        	date_default_timezone_set($timezone);

        	if ( !Env::is('cli') )
        	{
        		$session = $config->get('defaults.session_autostart', true);
        		if ( $session )
        		{
        			session_start();
        		}

        		header("Content-Type: text/html;charset=utf-8");
        	}

        	self::$config = $config;
        	$noinit = false;
		}
	}

	/**
	 * 加载指定应用
	 * 
	 * @param  string $appId 应用标识
	 * 
	 * @return \Ws\Mvc\App
	 */
	public static function load($appId)
	{

	}

}
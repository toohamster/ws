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

	/**
	 * 加载指定应用
	 * 
	 * @param  string $appId 应用标识
	 * 
	 * @return \Ws\Mvc\App
	 */
	public static function dispatch(Request $request=null)
	{
		if (null == $request)
		{
			$request = new Request( Request::get_request_pathinfo() );
		}
		
		$app = self::parseMointpoints($request);
		if (!empty($app))
		{
			return $app->run();
		}

		// paochu异常
	}

	/**
	 * 解析请求并挂载到指定应用
	 * 
	 * @param  Request $request
	 * 
	 * @return \Ws\Mvc\App
	 */
	private static function parseMointpoints(Request $request)
	{
		$pathinfo = $request->pathinfo();

		$mounts = (array) self::$config->get('app.mounts');

		$app = null;
		// 定位挂载点
		foreach ( $mounts as $appId => $options )
		{
			$idstr = '/^' . str_replace('/', '\/', $options['mount']) . '/i';
			if (preg_match($idstr,$pathinfo))
			{
				$app = new App($appId, $options['dir'], $options['mount']);
				
				$pathinfo = preg_replace($idstr,'',$pathinfo);
				
				$app->setPathinfo($pathinfo);
				break;
			}
		}

		return $app;
	}

}
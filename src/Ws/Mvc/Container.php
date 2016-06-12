<?php namespace Ws\Mvc;

use Exception;
use Ws\Env;
use Ws\Helper\Arrays;

class Container
{

	/**
	 * 应用设置对象
	 * 
	 * @var \Ws\Mvc\Config
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

			$timezone = $config->get('timezone', 'Asia/Chongqing');
        	date_default_timezone_set($timezone);

        	if ( !Env::is('cli') )
        	{
        		$session = $config->get('session_autostart', true);
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
	public static function loadApp($appId, $options=false)
	{
		$appId = strtoupper( trim($appId) );
		$idstr = 'app.list/app::' . $appId;
		$app = self::$config->get($idstr, null);
		if ( $app instanceof App )
		{
			return $app;
		}

		if ( false !== $options )
		{
			$app = new App($appId, $options['dir'], $options['mount']);
			self::$config->set($idstr, $app);

			return $app;
		}

		return null;
	}

	/**
	 * 分发请求
	 * 
	 * @param  \Ws\Mvc\Request $request
	 * 
	 * @return mixed
	 */
	public static function dispatch(Request $request=null)
	{
		if (null == $request)
		{
			$accessor = trim(self::$config->get('url.accessor', 'accessor'));

			if ( !empty($accessor) && strlen($accessor) < 16 && isset($_GET[$accessor]) )
			{
				$request = new Request( $_GET[$accessor] );
			}
			else
			{
				$request = new Request( Request::get_request_pathinfo() );
			}
		}
		$app = self::parseMointpoints($request);
		if (!empty($app))
		{
			return $app->run();
		}

		throw new Exception("cannot parse path: " . $request->pathinfo());
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
		static $firstIs = true;
		$pathinfo = $request->pathinfo();

		$mounts = (array) self::$config->get('app.mounts');
		if ( $firstIs )
		{
			// 格式化 $mounts
			foreach ( $mounts as $appId => $options )
			{
				if ( is_dir($options['dir']) )
				{
					$options['dir'] = rtrim($options['dir'], '\/');
					$options['mount'] = rtrim($options['mount'], '\/') . '/';
					$options['len']	 = strlen($options['mount']);

					$mounts[$appId] = $options;					
				}
				else
				{
					unset( $mounts[$appId] );
				}				
			}

			$mounts = Arrays::sort_by_col($mounts, 'len' ,SORT_DESC);
			self::$config->set('app.mounts', $mounts);			
			$firstIs = false;
		}

		$app = null;
		// 定位挂载点
		foreach ( $mounts as $appId => $options )
		{
			$idstr = '/^' . str_replace('/', '\/', $options['mount']) . '/i';
			
			if (preg_match($idstr,$pathinfo))
			{
				$app = self::loadApp($appId, $options);
				if ( !empty($app) )
				{
					$command = preg_replace($idstr,'',$pathinfo);
					$app->setCommand($command);
				}

				break;
			}
		}

		return $app;
	}

}
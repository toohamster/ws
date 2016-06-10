<?php namespace Ws\Mvc;

use Exception;
use Ws\Env;

class Command
{

	/**
	 * 类型: 页面
	 */
	const PAGE 	= '.html';
	
	/**
	 * 类型: 接口
	 */
	const JSON 	= '.json';

	/**
	 * 命令占位符
	 */
	const QUERYKEY = 'q';

	/**
	 * 缺省命令
	 */
	const QUERYDEFAULT = 'index';

	private static $routes = [
		
			Command::PAGE	=>[
				'pattern' => '/{q}.html',
				'config' => [
						Command::QUERYKEY => '[a-z][a-z0-9\/]+'
				],
				'default' => [
						Command::QUERYKEY => Command::QUERYDEFAULT 
				]
			],
					
			Command::JSON	=>[
				'pattern' => '/{q}.json',
				'config' => [
						Command::QUERYKEY => '[a-z][a-z0-9\/]+'
				],
				'default' => [
						Command::QUERYKEY => Command::QUERYDEFAULT  
				]
			],
		];
	
	/**
	 * 解析命令并返回
	 * 
	 * @param  string $command
	 * @return string
	 */
	public static function parse($command)
	{
		if ( empty($command) ) $command = '/';

        foreach(self::$routes as $key => $route)
        {
            # 将路由的配置参数添加到 正则规则中.
            foreach($route['config'] as $ck => $cval)
            {
                $route['pattern'] = str_replace('{'.$ck.'}', '('.$cval.')', $route['pattern']);
            }

            if (preg_match('#^'.$route['pattern'].'/?$#i', $command, $match_result))
            {
                # 处理默认项
                if ( !empty($route['default']) )
                {
                    foreach ($route['default'] as $ck => $cval)
                    {
                        $_GET[$ck] = $_REQUEST[$ck] = $cval;
                    }
                }

                # offset 为0 是 原字符串
                $offset = 1;
                foreach($route['config'] as $ck => $cval)
                {
                    if(isset($match_result[$offset]))
                    {
                        $_GET[$ck] = $_REQUEST[$ck] = $match_result[$offset++];
                    }
                }

				$_GET[Command::QUERYKEY] = str_replace('/', '.', trim( $_GET[Command::QUERYKEY], "\/" ));
                break;
            }
        }

        if ( empty($_GET[Command::QUERYKEY]) ) $_GET[Command::QUERYKEY] = Command::QUERYDEFAULT;

        return $_GET[Command::QUERYKEY];
	}

	/**
     * 生成 Rewrite 模式的命令
     *
     * @param string $route_key
     * @param array $params
     *
     * @return string
     */
    static function rewrite($route_key, $params)
    {
        $route = empty(self::$routes[$route_key]) ? self::$routes[Command::PAGE] : self::$routes[$route_key];
        
        # 找出要参数化的变量(将参数数组同default合并起来)
        $kv = [];
        foreach($route['config'] as $ck => $cval)
        {
            $kv[$ck] = $cval;
        }
        foreach($route['default'] as $ck => $cval)
        {
            $kv[$ck] = $cval;
        }
        
        # 填充必须的参数
        if ( empty($kv[Command::QUERYKEY]) )
        {
            $kv[Command::QUERYKEY] = Command::QUERYDEFAULT;
        }
        if ( !empty($params[Command::QUERYKEY]) )
        {
        	$params[Command::QUERYKEY] = str_replace('.', '/', trim($params[Command::QUERYKEY], "\." ));
        }

        foreach($kv as $ck => $cval)
        {
            if ( !empty($params[$ck]) )
            {
                $cval = $params[$ck];
                unset($params[$ck]);
            }
            $route['pattern'] = str_replace('{'.$ck.'}', $cval, $route['pattern']);
        }

        $rst = ltrim($route['pattern'], '\/');

        if (!empty($params))
        {
            $rst .= '?' . http_build_query($params);
        }

        return $rst;
    }

}

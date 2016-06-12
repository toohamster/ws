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
	const QUERYKEY = '_cmdId';

	/**
	 * 缺省命令
	 */
	const QUERYDEFAULT = 'index';

	private static $types = [
		
			Command::PAGE	=>[
				'pattern' => '/{_cmdId}.html',
				'config' => [
						Command::QUERYKEY => '[a-z][a-z0-9\/]+'
				],
				'default' => [
						Command::QUERYKEY => Command::QUERYDEFAULT 
				]
			],
					
			Command::JSON	=>[
				'pattern' => '/{_cmdId}.json',
				'config' => [
						Command::QUERYKEY => '[a-z][a-z0-9\/]+'
				],
				'default' => [
						Command::QUERYKEY => Command::QUERYDEFAULT  
				]
			],
		];
	
	/**
	 * 解析命令标识
	 * 
	 * @param  string $command
	 * @return string
	 */
	public static function parseId($command)
	{
		if ( empty($command) ) $command = '/';

        foreach(self::$types as $key => $type)
        {
            # 将路由的配置参数添加到 正则规则中.
            foreach($type['config'] as $ck => $cval)
            {
                $type['pattern'] = str_replace('{'.$ck.'}', '('.$cval.')', $type['pattern']);
            }

            if (preg_match('#^'.$type['pattern'].'/?$#i', $command, $match_result))
            {
                # 处理默认项
                if ( !empty($type['default']) )
                {
                    foreach ($type['default'] as $ck => $cval)
                    {
                        $_GET[$ck] = $_REQUEST[$ck] = $cval;
                    }
                }

                # offset 为0 是 原字符串
                $offset = 1;
                foreach($type['config'] as $ck => $cval)
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
	 * 生成命令
	 * 
	 * @param  string $commandId
	 * @param  array  $params
	 * @param  const $typeid
	 * 
	 * @return string
	 */
    public static function build($commandId, $params=[], $typeid=Command::PAGE)
    {
    	if ( !is_array($params) ) $params = [];
        $params[Command::QUERYKEY] = $commandId;

        $type = empty(self::$types[$typeid]) ? self::$types[Command::PAGE] : self::$types[$typeid];
        
        # 找出要参数化的变量(将参数数组同default合并起来)
        $kv = [];
        foreach($type['config'] as $ck => $cval)
        {
            $kv[$ck] = $cval;
        }
        foreach($type['default'] as $ck => $cval)
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
            $type['pattern'] = str_replace('{'.$ck.'}', $cval, $type['pattern']);
        }

        $rst = ltrim($type['pattern'], '\/');

        if (!empty($params))
        {
            $rst .= '?' . http_build_query($params);
        }

        return $rst;
    }

}

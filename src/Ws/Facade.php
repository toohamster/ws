<?php namespace Ws;
/**
 * Facade 包装类
 *
 * 优点:
 * 		使用简单的方式来省掉视图里面长长的命名空间调用,诸如 YII 的模版
 * 缺点:
 * 		这么写在 IDE 中会失去代码自动提示的功能,对于 sublime 狗而言貌似不是缺点
 *
 * 使用方法:
 * 		\Ws\Facade::{方法名}( {facade名称}, 参数1, 参数2,...参数n )
 * 
 * <code>
 * // 初始化
 * \Ws\Facade::setFacade('esClient', '\Elasticsearch\Client');
 * 
 * 	$dsn      = \Ws\Mvc\Container::$config->get('esken.dsn');
 * 	$esClient = \Ws\Facade::newInstance('esClient', $dsn);
 * 	\Ws\Env::dump($esClient);
 * </code>
 * 
 * @author vb2005xu@qq.com
 */
final class Facade
{
	private static $map = [];

	public static function setFacade($alias, $class)
	{
		self::$map[ $alias ] = $class;
	}

	private static function __facade__($facade, $method, $arguments=[])
	{
		if ( is_object($facade) )
		{
			// 调用 对象方法
			return call_user_func_array( [$facade, $method], $arguments );
		}
		else if (is_string($facade))
		{
			if ( empty(self::$map[$facade]) )
			{
				throw new Exception("未定义 'facade': {$facade} ");
			}
			// 调用 静态方法
			if ( 'newInstance' == $method )
	    	{
	    		$class = new ReflectionClass( self::$map[$facade] );
	    		return $class->newInstanceArgs( $arguments );
	    	}
			$class = self::$map[$facade];
			return call_user_func_array( [$class, $method], $arguments );
		}

		throw new Exception("无效 'facade' 调用!");
	}

	public function __call($method, $arguments) 
    {
    	$facade = array_shift($arguments);
    	return self::__facade__($facade, $method, $arguments);
    }

    public static function __callStatic($method, $arguments) 
    {
    	$facade = array_shift($arguments);    	
    	return self::__facade__($facade, $method, $arguments);
    }

}

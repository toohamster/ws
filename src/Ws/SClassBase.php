<?php namespace Ws;

/**
 * 单例类基类
 */
abstract class SClassBase
{
    
    /**
     * 服务实例集合
     * @var array
     */
    private static $selfs = [];

    /**
     * 返回服务类的单态实例
     *
     * @return self
     */
    public static function instance()
    {
        $class = get_called_class();
        if (empty(self::$selfs[$class])) {
            self::$selfs[$class] = new $class;
        }
        return self::$selfs[$class];
    }

}
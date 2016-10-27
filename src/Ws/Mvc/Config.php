<?php namespace Ws\Mvc;

use Ws\Env;

/**
 * 用与保存和读取应用设置的工具类
 */
class Config
{

    /**
     * 仅读
     * 
     * @var boolean
     */
    private $readonly = false;

    /**
     * 配置数组
     *
     * @var array
     */
    private $_config = [];

    /**
     * 构造函数
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
    }

    /**
     * 关闭写操作
     */
    public function setReadonly()
    {
        $this->readonly = true;
    }

    /**
     * 导入设置
     *
     * @param array $config
     */
    public function import($config)
    {
        if ($this->readonly)
        {
            throw new Exception("Config: readonly!");
        }
        $this->_config = array_merge($this->_config, $config);
    }

    /**
     * 读取指定的设置，如果不存在则返回$default参数指定的默认值
     *
     * @param string $item
     * @param mixed $default
     * @param bool $found
     *
     * @return mixed
     */
    public function get($item, $default = null, & $found = false)
    {
        if (is_array($item))
        {
            $found = false;
            foreach ($item as $key)
            {
                $return = $this->get($key, $default, $found);
                if ($found) return $return;
            }
            return $default;
        }

        if (strpos($item, '/') === false)
        {
            $found = array_key_exists($item, $this->_config);
            return $found ? $this->_config[$item] : $default;
        }

        list($keys, $last) = self::_get_nested_keys($item);
        $config =& $this->_config;
        foreach ($keys as $key)
        {
            if (array_key_exists($key, $config))
            {
                $config =& $config[$key];
            }
            else
            {
                return $default;
            }
        }
        $found = array_key_exists($last, $config);
        return $found ? $config[$last] : $default;
    }

    /**
     * 修改指定的设置
     *
     * @param string $item
     * @param mixed $value
     */
    public function set($item, $value)
    {
        if ($this->readonly)
        {
            throw new Exception("Config: readonly!");
        }

        if (strpos($item, '/') === false)
        {
           $this->_config[$item] = $value;
        }

        list($keys, $last) = self::_get_nested_keys($item);
        $config =& $this->_config;
        foreach ($keys as $key)
        {
            if (!array_key_exists($key, $config))
            {
                $config[$key] = [];
            }
            $config =& $config[$key];
        }
        $config[$last] = $value;
    }

    static private function _get_nested_keys($key)
    {
        $keys = \Ws\Helper\Arrays::normalize($key, '/');
        $last = array_pop($keys);
        return [$keys, $last];
    }

}

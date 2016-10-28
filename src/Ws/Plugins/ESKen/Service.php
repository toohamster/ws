<?php namespace Ws\Plugins\ESKen;

use Exception;
use Elasticsearch\Common\Exceptions\Missing404Exception;

final class Service
{

    /**
     * 检索 ES 数据
     *
     * @param  \Ws\Plugins\ESKen\Db $db 索引
     * @param  string $index 索引
     * @param  string $type 类型
     * @param  string $body 查询体
     * @param  array $attrs 额外查询参数
     *
     * @return array
     */
    public static function apiRequest(Db $db, $index, $type, $body, $attrs = [])
    {
        try {
            $response = $db->search($index, $type, $body, $attrs);
        } catch (Exception $ex) {

            $msg = $ex->getMessage();

            if ($ex instanceof Missing404Exception) {
                $msg = "ES Missing404: {$index}|{$type}";
            }

            throw new Exception($msg);
        }

        return $response;
    }

    /**
     * 编译 es 的查询参数,并返回
     *
     * @param  string $template 查询参数模版
     * @param  array $params 查询参数
     *
     * @return string
     */
    public static function compileQueryParams($template, $params = [])
    {
        if (empty($params) || !is_array($params)) {
            return $template;
        }

        foreach ($params as $name => $val) {
            $name = '${' . trim($name) . '}';

            if (empty($val)) {
                $val = '';
            }

            $template = str_ireplace($name, $val, $template);
        }

        return $template;
    }
    
    /**
     * 返回 13位时间戳
     *
     * @param  int $timestamp
     * @return int
     */
    public static function tsTo13bit($timestamp)
    {
        if (10 == strlen($timestamp)) {
            $timestamp = $timestamp * 1000;
        }
        return $timestamp;
    }

}
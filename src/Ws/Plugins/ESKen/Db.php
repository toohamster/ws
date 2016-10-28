<?php namespace Ws\Plugins\ESKen;

use Ws\Plugins\PluginBase;
use Elasticsearch\Client as ESClient;

final class Db
{

    /**
     * @var \Elasticsearch\Client
     */
    private $esClient;

    public function __construct(Dsn $dsn)
    {
        $this->esClient = new ESClient($dsn->toArray());
    }

    /**
     * 返回 ES 客户端对象
     * 
     * @return \Elasticsearch\Client
     */
    public function getDb()
    {
        return $this->esClient;
    }

    /**
     * 查询 结果
     *
     * @param  string $index 索引
     * @param  string $type 类型
     * @param  string $body 查询字符串
     * @param  array  $attrs 额外查询参数
     *
     * @return mixed
     */
    public function search($index, $type, $body, $attrs = [])
    {
        $query = [
            'index' => $index,
            'type' => $type,
            'body' => $body,
        ];
        if (!empty($attrs)) {
            $query = array_merge($attrs, $query);
        }

        return $this->esClient->search($query);
    }

}
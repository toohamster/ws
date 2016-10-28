<?php namespace Ws\Plugins\Hadoop;

final class Service
{

    /**
     * @var \Ws\Plugins\Hadoop\WebHDFS
     */
    private $hdfs;

    public function __construct(array $cc)
    {
        $this->hdfs = new WebHDFS($cc['host'], $cc['port'], $cc['user']);
    }

    /**
     * 返回 hdfs对象
     * 
     * @return \Ws\Plugins\Hadoop\WebHDFS
     */
    public function getHDFS()
    {
        return $this->hdfs;
    }

}
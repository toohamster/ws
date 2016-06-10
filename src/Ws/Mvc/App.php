<?php namespace Ws\Mvc;

use Ws\Env;

class App
{

	private $id;
	private $dir;
	private $mount;
	private $pathinfo;

	public function __construct($id, $dir, $mount)
    {
        $this->id     = $id;
        $this->dir    = $dir;
        $this->mount  = $mount;

        // 加载初始化配置信息
        $this->isInit = true;
    }

    public function isInit()
    {
    	return $this->isInit;
    }

    /**
	 * 设置pathinfo
	 * 
	 * @param  string $pathinfo
	 * 
	 * @return \Ws\Mvc\App
	 */
    public function setPathinfo($pathinfo)
    {	
    	$this->pathinfo = Request::fmtPathinfo($pathinfo);
    	
    	return $this;
    }

    /**
	 * 执行
	 * 
	 * @return void
	 */
    public function run()
    {
        if ( !$this->isInit )
        {
            throw new Exception("{$this->id} not init");
        }

    	Env::dump($this->pathinfo,'pathinfo');
    }

}
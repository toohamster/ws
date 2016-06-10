<?php namespace Ws\Mvc;

use Exception;
use Ws\Env;

class App
{

	private $id;
	private $dir;
	private $mount;
	private $command;

	public function __construct($id, $dir, $mount)
    {
        $this->id     = $id;
        $this->dir    = $dir;
        $this->mount  = $mount;

        //todo 加载初始化配置信息
        //
        $this->isInit = true;
    }

    public function isInit()
    {
    	return $this->isInit;
    }

    /**
	 * 设置 command
	 * 
	 * @param  string $command
	 * 
	 * @return \Ws\Mvc\App
	 */
    public function setCommand($command)
    {
        $command = trim($command);
    	$this->command = empty($command) ? '/' : '/' . trim($command, '\/');
    	
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

        $command = Command::parse($this->command);

    	Env::dump($_GET,'command');
    }

    public function url($command_type, $command='index', $params=null, $anchor=null)
    {
        if ( !is_array($params) ) $params = [];
        $params[Command::QUERYKEY] = $command;
        $url = rtrim( Request::get_request_baseuri(), '\/') . $this->mount . Command::rewrite($command_type, $params);

        if (!empty($anchor)) $url .= '#' . trim($anchor);
        return $url;
    }

}
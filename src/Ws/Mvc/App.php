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

        $fileInit = $this->dir . '/__init.php';
        if ( is_readable($fileInit) )
        {
            require($fileInit);
        }

    }

    /**
     * App 实例对象
     * 
     * @return \Ws\Mvc\App
     */
    public function me()
    {
        return $this;
    }

    /**
	 * 设置 command
	 * 
	 * @param  string $command
	 */
    public function setCommand($command)
    {
        $command = trim($command);
    	$this->command = empty($command) ? '/' : '/' . trim($command, '\/');
    }

    /**
	 * 执行
	 * 
	 * @return void
	 */
    public function run()
    {
        $commandId = Command::parseId($this->command);

    	Env::dump($_GET,'command');
    }

    /**
     * 生成 页面 模式的命令串
     * 
     * @param  string $commandId 命令标识
     * @param  array  $params    参数
     * @param  string $anchor    锚点
     * 
     * @return string
     */
    public function pageCommand($commandId, $params=[], $anchor=null)
    {        
        $url = rtrim(Request::get_request_baseuri(), '\/') . $this->mount;
        $url .= Command::rewrite($commandId, $params, Command::PAGE);

        if (!empty($anchor)) $url .= '#' . trim($anchor);
        return $url;
    }

    /**
     * 生成 数据接口 模式的命令串
     * 
     * @param  string $commandId 命令标识
     * @param  array  $params    参数
     * 
     * @return string
     */
    public function jsonCommand($commandId, $params=[])
    {        
        $url = rtrim( Request::get_request_baseuri(), '\/') . $this->mount;
        $url .= Command::build($commandId, $params, Command::JSON);

        return $url;
    }

}
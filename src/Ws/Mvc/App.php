<?php namespace Ws\Mvc;

use Exception;
use Ws\Env;

class App
{

    /**
     * App 配置对象
     * 
     * @var \Ws\Mvc\Config
     */
    private $config;

    /**
     * 当前执行的 pathing
     * 
     * @var string
     */
    private $pathing = '/';

	public function __construct($id, $dir, $mount)
    {
        $this->config = new Config([
                'app.id'    => $id,
                'app.dir'    => $dir,
                'app.mount'    => $mount,
                'app.cmds'    => [],
            ]);

        $fileInit = $dir . '/__init.php';
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
     * App 配置对象
     * 
     * @return \Ws\Mvc\Config
     */
    public function config()
    {
        return $this->config;
    }

    /**
	 * 设置 pathing
	 * 
	 * @param  string $pathing
	 */
    public function setPathing($pathing)
    {
        $pathing = trim($pathing);
    	$this->pathing = empty($pathing) ? '/' : '/' . trim($pathing, '\/');
    }

    /**
	 * 执行
	 * 
	 * @return void
	 */
    public function run()
    {
        $cmdId = Cmd::parseId($this->pathing);

        $cmd = Cmd::find($cmdId, $this);
        if ( !empty($cmd) && ($cmd instanceof Cmd) )
        {
            $result = $cmd->execute($this);
            return $result;
        }
        else
        {
            throw new Exception("unbind cmd: " . $cmdId);
        }
    }

    /**
     * 生成 页面 模式的访问路径
     * 
     * @param  string $cmdId 命令标识
     * @param  array  $params    参数
     * @param  string $anchor    锚点
     * 
     * @return string
     */
    public function pagePathing($cmdId, $params=[], $anchor=null)
    {
        $url = rtrim(Request::get_request_baseuri(), '\/') . $this->config->get('app.mount');
        $url .= Cmd::build($cmdId, $params, Cmd::PAGE);

        if (!empty($anchor)) $url .= '#' . trim($anchor);
        return $url;
    }

    /**
     * 生成 接口 模式的访问路径
     * 
     * @param  string $cmdId    命令标识
     * @param  array  $params   参数
     * 
     * @return string
     */
    public function jsonPathing($cmdId, $params=[])
    {
        $url = rtrim( Request::get_request_baseuri(), '\/') . $this->config->get('app.mount');
        $url .= Cmd::build($cmdId, $params, Cmd::JSON);

        return $url;
    }

}
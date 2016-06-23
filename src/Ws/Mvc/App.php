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
                'app.commands'    => [],
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
        $commandId = Command::parseId($this->pathing);

        $cmdObject = Command::find($commandId, $this);
        if ( !empty($cmdObject) && ($cmdObject instanceof Command) )
        {
            $result = $cmdObject->execute($this);
            return $result;
        }
        else
        {
            throw new \Exception("unbind command: " . $commandId);
        }
    }

    /**
     * 定义命令
     * 
     * @param  string $commandId    命令标识
     * @param  string $eventId 事件标识
     * @param  mixed  $closure 处理函数
     * 
     * @return \Ws\Mvc\Command
     */
    public function bind($commandId, $eventId, $closure)
    {
        if (empty($commandId)) return null;

        return Command::id($commandId)->bind($eventId, $closure)->bindTo($this);
    }

    /**
     * 生成 页面 模式的访问路径
     * 
     * @param  string $commandId 命令标识
     * @param  array  $params    参数
     * @param  string $anchor    锚点
     * 
     * @return string
     */
    public function pagePathing($commandId, $params=[], $anchor=null)
    {
        $url = rtrim(Request::get_request_baseuri(), '\/') . $this->config->get('app.mount');
        $url .= Command::build($commandId, $params, Command::PAGE);

        if (!empty($anchor)) $url .= '#' . trim($anchor);
        return $url;
    }

    /**
     * 生成 接口 模式的访问路径
     * 
     * @param  string $commandId 命令标识
     * @param  array  $params    参数
     * 
     * @return string
     */
    public function jsonPathing($commandId, $params=[])
    {
        $url = rtrim( Request::get_request_baseuri(), '\/') . $this->config->get('app.mount');
        $url .= Command::build($commandId, $params, Command::JSON);

        return $url;
    }

}
<?php namespace Ws\Mvc;

use Exception;
use Ws\Env;

class App
{

    /**
     * 应用设置对象
     * 
     * @var \Ws\Mvc\Config
     */
    private $config;

    /**
     * 当前执行的 command 指令
     * 
     * @var string
     */
    private $command;

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
     * App 标识
     * 
     * @return string
     */
    public function config()
    {
        return $this->config;
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

        $cmdObject = $this->config->get('app.commands/' . $commandId);
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
     * 绑定命令
     * 
     * @param  string $commandId    命令标识
     * @param  array  $config       执行
     * 
     * @return boolean
     */
    public function bind($commandId, array $config)
    {
        if (empty($commandId) || empty($config) || empty($config[0])) return false;

        $commandId = strtolower(trim($commandId));
        
        $closure = $config[0];
        $httpMethod = Env::val($config, 1 , Request::ANY);

        $cmdObject = null;

        if ( Env::isClosure($closure) )
        {
            $cmdObject = new Command($commandId, $httpMethod, $closure, 'closure');
        }
        else if (is_callable($closure))
        {
            $cmdObject = new Command($commandId, $httpMethod, $closure, 'callback');
        }
        else if (is_string($closure))
        {
            $closure = \Ws\Helper\Arrays::normalize($closure, '@');
            // class, method
            $class = array_shift($closure);

            if (!class_exists($class)) return false;

            $method = array_shift($closure);
            if ( empty($method) ) $method = 'execute';

            if ( is_callable([$class, $method]) )
            {
                $cmdObject = new Command($commandId, $httpMethod, [$class, $method], 'method');
            }            
        }

        if ( empty($cmdObject) ) return false;
      
        $this->config->set('app.commands/' . $commandId, $cmdObject);

        return true;
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
        $url = rtrim(Request::get_request_baseuri(), '\/') . $this->config->get('app.mount');
        $url .= Command::build($commandId, $params, Command::PAGE);

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
        $url = rtrim( Request::get_request_baseuri(), '\/') . $this->config->get('app.mount');
        $url .= Command::build($commandId, $params, Command::JSON);

        return $url;
    }

}
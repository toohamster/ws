<?php namespace Ws\Mvc;

use Exception;
use Ws\Env;

class Command
{

	/**
	 * 类型: 页面
	 */
	const PAGE 	= '.html';
	
	/**
	 * 类型: 接口
	 */
	const JSON 	= '.json';

	/**
	 * 命令占位符
	 */
	const QUERYKEY = '_cmdId';

	/**
	 * 缺省命令
	 */
	const QUERYDEFAULT = 'index';

	private static $types = [
		
			Command::PAGE	=>[
				'pattern' => '/{_cmdId}.html',
				'config' => [
						Command::QUERYKEY => '[a-z][a-z0-9\/]+'
				],
				'default' => [
						Command::QUERYKEY => Command::QUERYDEFAULT 
				]
			],
					
			Command::JSON	=>[
				'pattern' => '/{_cmdId}.json',
				'config' => [
						Command::QUERYKEY => '[a-z][a-z0-9\/]+'
				],
				'default' => [
						Command::QUERYKEY => Command::QUERYDEFAULT  
				]
			],
		];

    /**
     * 构造函数
     *
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->eventId = false;
        $this->closure = null;        
        $this->closureType = null;
        $this->filter = [];
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * 执行命令并返回结果
     */
    public function execute(App $app)
    {        
        $result = false;
        switch ( $this->closureType )
        {
            case 'closure':
            case 'callable':
                $result = call_user_func_array($this->closure, [$app]);
                break;
            case 'method':
                $class = $this->closure[0];
                $obj = new $class($app);

                $method = $this->closure[1];                
                $result = call_user_func_array([$obj, $method],[]);
                break;
        }
        return $result;
    }

    /**
     * 为指令绑定处理函数
     * 
     * @param  string $eventId 事件标识
     * @param  mixed  $closure 处理函数
     *  
     * @return \Ws\Mvc\Command
     */
    public function bind($eventId, $closure)
    {
        $this->eventId = $eventId;

        $closureInfo = Env::getClosure($closure);
        if ( !empty($closureInfo) )
        {
            $this->closureType = $closureInfo['type'];
            $this->closure = $closureInfo['closure'];
        }

        return $this;
    }

    /**
     * 绑定 过滤器
     * 
     * @param  closure    $before
     * @param  closure    $after
     * 
     * @return \Ws\Mvc\Command
     */
    public function filter($before=null, $after=null)
    {
        $before = Env::getClosure($before);
        $after = Env::getClosure($after);

        $this->filter = [
            'before'    => empty($before) ? null : $before['closure'],
            'after'    => empty($after) ? null : $after['closure'],
        ];

        return $this;
    }

    /**
     * 将命令绑定到 应用
     * 
     * @param  App    $app
     *
     * @return \Ws\Mvc\Command
     */
    public function bindTo(App $app)
    {
        $app->config()->set('app.commands/' . $this->id, $this);
        return $this;
    }

    /**
     * 定义命令对象并返回
     * 
     * @param  string   $id 名字
     * 
     * @return \Ws\Mvc\Command
     */
    public static function id($id)
    {
        $id = strtolower(trim($id));
        if ( empty($id) ) return null;
        return new self($id);
    }

    /**
     * 定义命令对象并返回
     * 
     * @param  string   $id 名字
     * 
     * @return \Ws\Mvc\Command
     */
    public static function find($id, App $app)
    {
        $id = strtolower(trim($id));
        if ( empty($id) ) return null;
        return $app->config()->get('app.commands/' . $id);
    }

    /**
     * 定义命令组对象并返回
     * 
     * @param  array   $list 指令集数组
     * 
     * @return \Ws\Mvc\CommandGroup
     */
    public static function group(array $list)
    {
        $ss = [];
        foreach ($list as $item)
        {
            if ( count($item) == 3)
            {
                $ss[] = Command::id($item[0])->bind($item[1], $item[2]);
            }
        }
        return new CommandGroup($ss);
    }
	
	/**
	 * 解析命令标识
	 * 
	 * @param  string $command
	 * @return string
	 */
	public static function parseId($command)
	{
		if ( empty($command) ) $command = '/';

        foreach(self::$types as $key => $type)
        {
            # 将路由的配置参数添加到 正则规则中.
            foreach($type['config'] as $ck => $cval)
            {
                $type['pattern'] = str_replace('{'.$ck.'}', '('.$cval.')', $type['pattern']);
            }

            if (preg_match('#^'.$type['pattern'].'/?$#i', $command, $match_result))
            {
                # 处理默认项
                if ( !empty($type['default']) )
                {
                    foreach ($type['default'] as $ck => $cval)
                    {
                        $_GET[$ck] = $_REQUEST[$ck] = $cval;
                    }
                }

                # offset 为0 是 原字符串
                $offset = 1;
                foreach($type['config'] as $ck => $cval)
                {
                    if(isset($match_result[$offset]))
                    {
                        $_GET[$ck] = $_REQUEST[$ck] = $match_result[$offset++];
                    }
                }

				$_GET[Command::QUERYKEY] = str_replace('/', '.', trim( $_GET[Command::QUERYKEY], "\/" ));
                break;
            }
        }

        if ( empty($_GET[Command::QUERYKEY]) ) $_GET[Command::QUERYKEY] = Command::QUERYDEFAULT;

        $_GET[Command::QUERYKEY] = strtolower($_GET[Command::QUERYKEY]);

        return $_GET[Command::QUERYKEY];
	}

	/**
	 * 生成命令
	 * 
	 * @param  string $commandId
	 * @param  array  $params
	 * @param  const $typeid
	 * 
	 * @return string
	 */
    public static function build($commandId, $params=[], $typeid=Command::PAGE)
    {
        $commandId = strtolower(trim($commandId));
        
    	if ( !is_array($params) ) $params = [];
        $params[Command::QUERYKEY] = $commandId;

        $type = empty(self::$types[$typeid]) ? self::$types[Command::PAGE] : self::$types[$typeid];
        
        # 找出要参数化的变量(将参数数组同default合并起来)
        $kv = [];
        foreach($type['config'] as $ck => $cval)
        {
            $kv[$ck] = $cval;
        }
        foreach($type['default'] as $ck => $cval)
        {
            $kv[$ck] = $cval;
        }
        
        # 填充必须的参数
        if ( empty($kv[Command::QUERYKEY]) )
        {
            $kv[Command::QUERYKEY] = Command::QUERYDEFAULT;
        }
        if ( !empty($params[Command::QUERYKEY]) )
        {
        	$params[Command::QUERYKEY] = str_replace('.', '/', trim($params[Command::QUERYKEY], "\." ));
        }

        foreach($kv as $ck => $cval)
        {
            if ( !empty($params[$ck]) )
            {
                $cval = $params[$ck];
                unset($params[$ck]);
            }
            $type['pattern'] = str_replace('{'.$ck.'}', $cval, $type['pattern']);
        }

        $rst = ltrim($type['pattern'], '\/');

        if (!empty($params))
        {
            $rst .= '?' . http_build_query($params);
        }

        return $rst;
    }

}

/**
 * 命令组
 */
class CommandGroup
{

    /**
     * 构造函数
     * 
     * @param array $list
     */
    public function __construct(array $list)
    {
        $this->list = [];
        foreach ( $list as $cmd )
        {
            if ($cmd instanceof Command)
            {
                $this->list[] = $cmd;
            }
        }
    }

    /**
     * 绑定到 应用对象
     * 
     * @param  App    $app
     * 
     * @return \Ws\Mvc\CommandGroup
     */
    public function bindTo(App $app)
    {
        foreach ($this->list as $cmd)
        {
            $cmd->bindTo($app);
        }

        return $this;
    }

    /**
     * 绑定 过滤器
     * 
     * @param  closure    $before
     * @param  closure    $after
     * 
     * @return \Ws\Mvc\CommandGroup
     */
    public function filter($before, $after=null)
    {
        foreach ($this->list as $cmd)
        {
            $cmd->filter($before, $after);
        }

        return $this;
    }

}
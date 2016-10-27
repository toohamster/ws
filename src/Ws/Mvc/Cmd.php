<?php namespace Ws\Mvc;

use Exception;
use Ws\Env;

class Cmd
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
		
			Cmd::PAGE	=>[
				'pattern' => '/{_cmdId}.html',
				'config' => [
						Cmd::QUERYKEY => '[a-z][a-z0-9\/]+'
				],
				'default' => [
						Cmd::QUERYKEY => Cmd::QUERYDEFAULT 
				]
			],
					
			Cmd::JSON	=>[
				'pattern' => '/{_cmdId}.json',
				'config' => [
						Cmd::QUERYKEY => '[a-z][a-z0-9\/]+'
				],
				'default' => [
						Cmd::QUERYKEY => Cmd::QUERYDEFAULT  
				]
			],
		];

    /**
     * 构造函数
     *
     * @param string $id
     */
    private function __construct($id)
    {
        $this->id = $id;
        $this->event = false;
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
     * @param  string $event 事件
     * @param  mixed  $closure 处理函数
     *  
     * @return \Ws\Mvc\Cmd
     */
    public function bind($event, $closure)
    {
        $this->event = $event;

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
     * @return \Ws\Mvc\Cmd
     */
    public function filter($before=null, $after=null)
    {
        $before = Env::getClosure($before);
        $after = Env::getClosure($after);

        $this->filter = [
            'before'    => empty($before) ? null : $before['closure'],
            'after'    => empty($after) ? null : $after['closure']
        ];

        return $this;
    }

    /**
     * 将命令绑定到 应用
     * 
     * @param  App    $app
     *
     * @return \Ws\Mvc\Cmd
     */
    public function bindTo(App $app)
    {
        $app->config()->set('app.cmds/' . $this->id, $this);
        return $this;
    }

    /**
     * 定义命令对象并返回
     * 
     * @param  string   $id 名字
     * 
     * @return \Ws\Mvc\Cmd
     */
    public static function id($id)
    {
        $id = trim($id);
        if ( empty($id) ) return null;
        return new Cmd(strtolower($id));
    }

    /**
     * 定义命令对象并返回
     * 
     * @param  string   $id 名字
     * 
     * @return \Ws\Mvc\Cmd
     */
    public static function find($id, App $app)
    {
        $id = trim($id);
        if ( empty($id) ) return null;
        return $app->config()->get('app.cmds/' . strtolower($id));
    }

    /**
     * 定义命令组对象并返回
     * 
     * @param  array   $list 指令集数组
     * 
     * @return \Ws\Mvc\CmdGroup
     */
    public static function group(array $list)
    {
        $ss = [];
        foreach ($list as $item)
        {
            if (!empty($item))
            {
                $ss[] = Cmd::id($item['id'])->bind($item['event'], $item['closure']);
            }
        }
        return new CmdGroup($ss);
    }
	
	/**
	 * 解析命令标识
	 * 
	 * @param  string $Cmd
	 * @return string
	 */
	public static function parseId($Cmd)
	{
		if ( empty($Cmd) ) $Cmd = '/';

        foreach(self::$types as $key => $type)
        {
            # 将路由的配置参数添加到 正则规则中.
            foreach($type['config'] as $ck => $cval)
            {
                $type['pattern'] = str_replace('{'.$ck.'}', '('.$cval.')', $type['pattern']);
            }

            if (preg_match('#^'.$type['pattern'].'/?$#i', $Cmd, $match_result))
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

				$_GET[Cmd::QUERYKEY] = str_replace('/', '.', trim( $_GET[Cmd::QUERYKEY], "\/" ));
                break;
            }
        }

        if ( empty($_GET[Cmd::QUERYKEY]) ) $_GET[Cmd::QUERYKEY] = Cmd::QUERYDEFAULT;

        $_GET[Cmd::QUERYKEY] = strtolower($_GET[Cmd::QUERYKEY]);

        return $_GET[Cmd::QUERYKEY];
	}

	/**
	 * 生成命令
	 * 
	 * @param  string $CmdId
	 * @param  array  $params
	 * @param  const $typeid
	 * 
	 * @return string
	 */
    public static function build($CmdId, $params=[], $typeid=Cmd::PAGE)
    {
        $CmdId = strtolower(trim($CmdId));
        
    	if ( !is_array($params) ) $params = [];
        $params[Cmd::QUERYKEY] = $CmdId;

        $type = empty(self::$types[$typeid]) ? self::$types[Cmd::PAGE] : self::$types[$typeid];
        
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
        if ( empty($kv[Cmd::QUERYKEY]) )
        {
            $kv[Cmd::QUERYKEY] = Cmd::QUERYDEFAULT;
        }
        if ( !empty($params[Cmd::QUERYKEY]) )
        {
        	$params[Cmd::QUERYKEY] = str_replace('.', '/', trim($params[Cmd::QUERYKEY], "\." ));
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
class CmdGroup
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
            if ($cmd instanceof Cmd)
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
     * @return \Ws\Mvc\CmdGroup
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
     * @return \Ws\Mvc\CmdGroup
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
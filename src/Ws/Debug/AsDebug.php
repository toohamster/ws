<?php namespace Ws\Debug;

use Ws\Env;
use Ws\Mvc\Request;
use Ws\Mvc\Container;
use Ws\Mvc\App;
use Ws\Mvc\View;
use Ws\Mvc\Cmd;

/**
 * AsDebug 
 * 
 * 使用简单的方式来跟踪运行中的资源调用状况
 */
class AsDebug
{

    private $enable = false;

    private function __construct()
    {
    	$options = Container::$config->get('debug.asdebug');

    	$enable = empty($options['enable']) ? false : $options['enable'];

    	$this->enable = (bool) $enable;
    	if (!$this->enable) return;

    	$qi = empty($options['q']) ? 'asdebug' : $options['q'];
    	$qtag = empty($options['qtag']) ? 'asdebug-tag' : $options['qtag'];

        $this->qargs = [
            'q' => $qi,
            'qtag' => $qtag,
        ];
        $this->tag = Request::get($qtag, 'atag');

    	$secret = empty($options['secret']) ? 'toohamster' : $options['secret'];
    	
    	$ai = Request::get($qi);
    	if ( $secret != $ai )
    	{
    		$this->enable = false;
    		return;
    	}

    	$qdir = empty($options['dir']) ? 'asdebug' : $options['dir'];
    	if (!is_dir($qdir))
    	{
    		$qdir = sys_get_temp_dir();
    	}

    	$path = $qdir . '/asdebug';
        if (!is_dir($path)) {
            mkdir($path, 0700, true);
        }
        $this->filename = $path . '/as-' . md5($this->tag) . '.log';
        
        $this->items = [];
    }

    function __destruct()
    {
        // 进行资源释放
        if (!$this->enable) return;

        $headers = [];

        if (function_exists('getallheaders'))
        {
            $headers['request'] = getallheaders();
        }
        else
        {
            $headers['request'] = self::emu_getallheaders();
        }

        $headers['response'] = headers_list();

        $data = array(
            'url' => Request::get_request_uri(),
            'headers' => $headers,
            'cookies' => isset($_COOKIE) ? $_COOKIE : [],
            'sessions' => isset($_SESSION) ? $_SESSION : [],
            'items' => $this->items,
        );
        $create_at = time();
        $id = md5($data['url'] . $create_at);

        $data = json_encode(array(
            'id' => $id,
            'tag' => $this->tag,
            'content' => $this->output($data),
            'create_at' => date('m-d H:i:s'),
        ));

        file_put_contents($this->filename, $data);
    }

    private static function emu_getallheaders()
    {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$name] = $value;
            } else if ($name == "CONTENT_TYPE") {
                $headers["Content-Type"] = $value;
            } else if ($name == "CONTENT_LENGTH") {
                $headers["Content-Length"] = $value;
            }
        }
        return $headers;
    }

    /**
     * 单例对象
     * 
     * @return \Ws\Debug\AsDebug
     */
    public static function instance()
    {
        static $self = null;
        if (is_null($self)) $self = new self;
        return $self;
    }

    public function getContent()
    {output($this->filename);
        if (is_readable($this->filename)) {
            $json = file_get_contents($this->filename);
            if (!empty($json)) {
                $data = json_decode($json, true);
                if ($data['tag'] == $this->tag) {
                    return $json;
                }
            }
        }

        return '{}';
    }

    public function disable()
    {
        $this->enable = false;
    }

    private function write($vars, $label = '', $type = 'dump')
    {
        if (!$this->enable) return;

        $this->items[] = [
            'label' => $label,
            'vars' => $vars,
        ];
    }

    private function output($vars, $label = '')
    {
        return Env::dump($vars, $label, true);
    }

    public static function dd($vars, $label = '')
    {
        self::instance()->write($vars, $label, 'dump');
    }

    public static function ddexit($vars, $label = '')
    {
        self::instance()->write($vars, $label, 'dump');
        exit;
    }

    public function jqAjaxBind()
    {
        if (!$this->enable) return '';
        $view = new View(__DIR__ . '/_asdebug', 'jqajax', [
                    'qargs'  => $this->qargs,
                ]);
        return $view->execute();
    }

    public static function cmdView(App $app, $type)
    {
        self::instance()->disable();

        if ('json' == $type){
            return self::instance()->getContent();
        }

        if ('ui' == $type){
            $view = new View(__DIR__ . '/_asdebug', 'ui', [
                    'url'   => $app->pagePathing('asdebug'),
                    'qargs'  => $this->qargs,
                    'qtag'  => self::instance()->qargs['qtag'],
                    'tag'   => self::instance()->tag,
                    'sg'    => Request::get('sg', 500),
                ]);
            return $view->execute();
        }
    }

    public static function cmdBind(App $app)
    {
        Cmd::group([
            [
                'id'    => 'asdebug',
                'event' => Request::GET,
                'closure'   => function($a){
                    $f = Request::get('f');
                    if ($f == 'json') {
                        return AsDebug::cmdView($a, 'json');
                    }

                    return AsDebug::cmdView($a, 'ui');
                }
            ]
        ])->bindTo($app);
    }

}
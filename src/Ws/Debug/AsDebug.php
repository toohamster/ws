<?php namespace Ws\Debug;

use Ws\Env;
use Ws\SClassBase;
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
class AsDebug extends SClassBase
{

    private $qargs = [];
    private $items = [];

    function __construct()
    {
        $options = Container::$config->get('debug.asdebug');

    	$enable = empty($options['enable']) ? false : $options['enable'];

    	$this->enable = (bool) $enable;
    	if (!$this->enable) return;

    	$qauth = empty($options['qauth']) ? 'asdebug' : $options['qauth'];
        $authval = Request::get($qauth);
        $this->enable = $options['secret'] == $authval;

        if (!$this->enable) return;

    	$qtag = empty($options['qtag']) ? 'asdebug-tag' : $options['qtag'];

        $this->qargs = [
            'qauth'     => $qauth,
            'authval'      => $authval,
            'qtag'      => $qtag,
            'tagval'       => Request::get($qtag, 'atag'),
        ];

    	$dir = empty($options['dir']) ? 'asdebug' : $options['dir'];
    	if (!is_dir($dir))
    	{
    		$dir = sys_get_temp_dir();
    	}

        $this->qargs['dir'] = $dir . '/asdebug';
        $this->qargs['logfile'] = $this->qargs['dir'] . '/as-' . md5($this->tag) . '.log';
    }

    function __destruct()
    {
        // 进行资源释放
        if (!$this->enable) return;

        if (!is_dir($this->qargs['dir'])) {
            mkdir($this->qargs['dir'], 0700, true);
        }

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
            'tag' => $this->qargs['tagval'],
            'content' => Env::dump($data, '', true),
            'create_at' => date('m-d H:i:s'),
        ));

        file_put_contents($this->qargs['logfile'], $data);
    }

    public static function disable()
    {
        self::instance()->enable = false;
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

    private function getContent()
    {
        if (is_readable($this->qargs['logfile'])) {
            $json = file_get_contents($this->qargs['logfile']);
            if (!empty($json)) {
                $data = json_decode($json, true);
                if ($data['tag'] == $this->qargs['tagval']) {
                    return $json;
                }
            }
        }
        return '{}';
    }

    private function write($vars, $label = '', $type = 'dump')
    {
        if (!$this->enable) return;

        $this->items[] = [
            'label' => $label,
            'vars' => $vars,
        ];
    }

    public static function dd($vars, $label = '')
    {
        self::instance()->write($vars, $label, 'dump');
    }

    public function jqAjaxBind()
    {
        if (!$this->enable) return '';
        $view = new View(__DIR__ . '/_asdebug', 'jqajax', [
                    'qargs'  => $this->qargs,
                ]);
        return $view->execute();
    }

    public function cmdView(App $app, $type)
    {
        if ($this->enable)
        {
            $this->enable = false;

            if ('json' == $type){
                return $this->getContent();
            }
            if ('ui' == $type){
                $view = new View(__DIR__ . '/_asdebug', 'ui', [
                        'url'   => $app->pagePathing('asdebug'),
                        'qargs'  => $this->qargs,
                    ]);
                return $view->execute();
            }
        }

        return 422;
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
                        return AsDebug::instance()->cmdView($a, 'json');
                    }

                    return AsDebug::instance()->cmdView($a, 'ui');
                }
            ]
        ])->bindTo($app);
    }

}
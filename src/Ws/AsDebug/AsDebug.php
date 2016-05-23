<?php namespace Ws\AsDebug;

/**
 * 数据调试 操作类
 *
 * 使用简单的方式来打印脚本运行过程中的请求过程
 * 
 * @author ken.xu@yunzhihui.com
 */
class AsDebug
{

	private $disable = true;

	private function __construct()
	{
		$this->url = Request::fullUrl();
		if ( function_exists('getallheaders') )
		{
			$this->headers['request'] = getallheaders();
		}
		else
		{
			$this->headers['request'] = self::emu_getallheaders();
		}
		$this->headers['response'] = headers_list();
		$this->cookies = isset($_COOKIE) ? $_COOKIE : array();
		$this->sessions = isset($_SESSION) ? $_SESSION : array();
		$this->servers = isset($_SERVER) ? $_SERVER : array();
		$this->items = [];

		$disable = Input::get('asdebug');
		
		if ( PHP_SAPI === 'cli' )
		{
			$this->disable = false;
		}
		else
		{
			if ( $disable == 'ken.xu' )
			{
				$this->disable = false;
			}
		}

		$this->tag = Input::get('asdebug-tag','');

		$path = storage_path() . '/asdebug';
		if ( !is_dir($path) ){
			mkdir($path, 0700, true);
		}
		$this->filename = $path . '/log-' . md5($this->tag) . '.txt';
	}

	function __destruct()
	{
		// 进行资源释放
		if ( $this->disable ) return;

		$data = array(
				'url'	=> $this->url,
				'headers'	=> $this->headers,
				'cookies'	=> $this->cookies,
				'sessions'	=> $this->sessions,
				// 'servers'	=> $this->servers,
				'items'	=> $this->items,
			);
		$create_at = time();
		$id = md5( $data['url'] . $create_at );

		$data = json_encode( array(
				'id' => $id,
				'tag' => $this->tag,
				'content'	=> $this->output($data),
				'create_at'	=> date('m-d H:i:s')
			) );
		
		file_put_contents($this->filename, $data);
	}

	private static function emu_getallheaders() 
	{ 
    	foreach ($_SERVER as $name => $value) 
        { 
            if (substr($name, 0, 5) == 'HTTP_') 
            { 
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

	public static function instance()
	{
		static $self = null;
		if ( is_null($self) ) $self = new self;
		return $self;
	}

	public function getContent()
	{
		if ( is_readable($this->filename) )
		{
			$json = file_get_contents($this->filename);
			if ( !empty($json) )
			{
				$data = json_decode($json, true);
				if ( $data['tag'] == $this->tag)
				{
					return $json;
				}				
			}
		}

		return '{}';
	}

	public function isEnable()
	{
		return !$this->disable;
	}

	public function getTag()
	{
		return $this->tag;
	}

	public function disable($disable=false)
	{
		$this->disable = $disable;
	}

	private function write($vars, $label = '', $type='dump')
	{
		if ( $this->disable ) return;

		$this->items[] = array(
				'label'	=> $label,
				'vars'	=> $vars,
			);
	}

	private function output($vars, $label = '')
	{
		$content = "<pre>\n";
		if ($label != '')
		{
			$content .= "<strong>{$label} :</strong>\n";
		}
		$content .= htmlspecialchars(print_r($vars, true), ENT_COMPAT | ENT_IGNORE);
		$content .= "\n</pre>\n";

		return $content;
	}

	public static function dd($vars, $label = '')
	{
		self::instance()->write($vars, $label, 'dump');
	}

	public static function view()
	{
		self::instance()->disable(true);
		return self::instance()->getContent();
	}

}
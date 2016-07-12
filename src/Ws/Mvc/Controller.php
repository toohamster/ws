<?php namespace Ws\Mvc;

use Ws\Env;

/**
 * 控制器基类
 */
abstract class Controller
{
	/**
	 * 视图变量
	 * @var array
	 */
	protected $viewVars = [];

	/**
	 * 构造函数
	 * 
	 * @param \Ws\Env\App $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;

		$viewDir = $app->config()->get('app.view.dir', false);
		if ( $viewDir && is_dir($viewDir) )
		{
			$viewDir = rtrim($viewDir, '\/');
		}
		else
		{
			$dir = $app->config()->get('app.dir');
			$viewDir = $dir . '/views';
		}
		
		$this->viewDir = $viewDir;
	}

	/**
	 * 注入 视图中使用的工具对象,在模版中可以使用 $_vt['工具名'] 来引用
	 *
	 * @return array
	 */
	protected function getViewTools()
	{
		return [];
	}

	/**
	 * 渲染视图模版
	 * 
	 * @param  string $viewname
	 * 
	 * @return string
	 */
	protected function view($viewname)
	{
		$this->viewVars['_vt'] = $this->getViewTools();
		$viewObj = new View($this->viewDir, $viewname, $this->viewVars);
		return $viewObj->execute();
	}
    
	/**
	 * JSON 成功信息
	 *
	 * @param array $data
	 * @param string $msg
	 * 
	 * @return string
	 */
	protected function jsonSuccess($data = [], $msg = '操作成功')
	{
		$d = ['errcode' => 0, 'msg' => $msg, 'data' => $data];
		return json_encode($d);
	}

	/**
	 * JSON 错误信息
	 * 
	 * @param  string  $data
	 * @param  integer $errcode
	 * @param  string  $msg
	 * 
	 * @return string
	 */
	protected function jsonError($data = '', $errcode = -1, $msg = '操作失败')
	{
		if ( intval($errcode) == 0 ) $errcode = -1;
		
		$d = ['errcode' => $errcode, 'msg' => $msg];
		if ( !empty($data) ) $d['data'] = $data;

		return json_encode($d);
	}

}
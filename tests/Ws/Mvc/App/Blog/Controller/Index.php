<?php namespace Blog\Controller;

use Ws\Mvc\Controller;

class Index extends Controller
{

	public function index()
	{
		output(__METHOD__, 'text');
		output($this->viewDir, 'text');
		$this->viewVars['hello'] = __CLASS__;
		output( $this->view('index'), 'view' );
	}

}
<?php namespace Im\Controller;

use Ws\Mvc\Controller;

class Index extends Controller
{

	public function index()
	{
		$this->viewVars['hello'] = "sds";
		return $this->view('view');
	}

}
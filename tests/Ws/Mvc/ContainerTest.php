<?php

use Ws\Mvc\Container;

class ContainerTest implements ITest
{

	public function __construct()
	{
		$configs = require(__DIR__.'/__config.php');
		Container::init($configs);
	}

	private function configs()
	{
		output(Container::$config->get('app.mounts/default/mount','hhhh'));
		output(Container::$config->get('app.mounts/default/src','hhhh'));
	}

	public function run()
	{
		$this->configs();
		// output(Container::dispatch());
	}

}
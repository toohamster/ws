<?php
use Ws\Env;
use Ws\Debug\AsDebug;
use Ws\Mvc\Request;
use Ws\Mvc\Cmd;

$app = $this->me();
/*@var $app \Ws\Mvc\App */

$dir = $app->config()->get('app.dir');

// 注入类路径
Env::classLoader()->addPsr4('Default\\', $dir);

// 绑定命令
Cmd::id('index')->bind(Request::GET, function($app){
	output('App: ' . $app->config()->get('app.id'), '');

	output('Hello World!', 'text');
	output($app->pagePathing('who.are.you',['name'=>'a test']), 'url');
	output($app->jsonPathing('who.love.you',['tag'=>'php']), 'url');
})->bindTo($app);

// 绑定 AsDebug 工具界面
AsDebug::cmdBind($app);
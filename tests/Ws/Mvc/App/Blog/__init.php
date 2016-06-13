<?php
use Ws\Env;
use Ws\Mvc\Request;

$app = $this->me();
/*@var $app \Ws\Mvc\App */

$dir = $app->config()->get('app.dir');

// 注入类路径
Env::classLoader()->addPsr4('Blog\\', $dir);

// 绑定命令
$app->bind('hello', [ function($app){
	output('Hello World!', 'text');
	output($app->pageCommand('who.are.you',['name'=>'a test']), 'url');
	output($app->jsonCommand('who.love.you',['tag'=>'php']), 'url');
}, Request::GET ]);

$app->bind('index', ['Blog\Controller\Index@index', Request::GET]);

// Env::dump($app);
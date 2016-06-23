<?php
use Ws\Env;
use Ws\Mvc\Request;
use Ws\Mvc\Command;

$app = $this->me();
/*@var $app \Ws\Mvc\App */

$dir = $app->config()->get('app.dir');

// 注入类路径
Env::classLoader()->addPsr4('Blog\\', $dir);

// 绑定命令
$app->bind('hello', Request::GET, function($app){
	output('Hello World!', 'text');
	output($app->pagePathing('who.are.you',['name'=>'a test']), 'url');
	output($app->jsonPathing('who.love.you',['tag'=>'php']), 'url');
});

$app->bind('index', Request::GET, 'Blog\Controller\Index@index');

// Command::group([]);

// Env::dump($app);
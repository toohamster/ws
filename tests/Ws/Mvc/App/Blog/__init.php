<?php
use Ws\Env;
use Ws\Mvc\Request;
use Ws\Mvc\Cmd;

$app = $this->me();
/*@var $app \Ws\Mvc\App */

$dir = $app->config()->get('app.dir');

// 注入类路径
Env::classLoader()->addPsr4('Blog\\', $dir);

// 绑定命令
Cmd::id('hello')->bind(Request::GET, function($app){
	output('App: ' . $app->config()->get('app.id'), '');

	output('Hello World!', 'text');
	output($app->pagePathing('who.are.you',['name'=>'a test']), 'url');
	output($app->jsonPathing('who.love.you',['tag'=>'php']), 'url');
})->bindTo($app);;

Cmd::id('index')->bind(Request::GET, 'Blog\Controller\Index@index')->bindTo($app);

// Cmd::group([
// 		[
// 			'id'	=> 'hello',
// 			'event'	=> Request::GET,
// 			'closure'	=> function($a){
// 				Env::dump($a);
// 			}
// 		]
// 	])->bindTo($app);

// Env::dump($app);
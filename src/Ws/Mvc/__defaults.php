<?php
# 应用配置文件
#
return [
	'app.session_autostart' => true,
	'app.timezone' => 'Asia/Chongqing',

	'debug.tracks'	=> [
		'enable'	=> true,
		'qauth'	=> 'wstracks',
		'secret'=> 'ws.tracks',
	],
	'debug.asdebug'	=> [
		'enable'	=> true,
		'qauth'	=> 'asdebug',
		'qtag'	=> 'asdebug-tag',
		'secret'=> 'toohamster',
		'dir'	=> null,
	],
	
	'cmd.accessor' => '_ws',

	'app.mounts'	=> [
		'default'	=> [
			'mount'	=> '/',
			'dir'	=> __DIR__,
		]
	],
	'app.list'	=> []
];
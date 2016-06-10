<?php
# 应用配置文件
#
return [
	'defaults.session_autostart' => true,
	'defaults.timezone' => 'Asia/Chongqing',

	'app.uribase' => '/',
	'app.mounts'	=> [
		'default'	=> [
			'mount'	=> '/',
			'src'	=> __DIR__,
		]
	],
	'app.list'	=> []
];
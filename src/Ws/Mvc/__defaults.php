<?php
# 应用配置文件
#
return [
	'app.session_autostart' => true,
	'app.timezone' => 'Asia/Chongqing',
	
	'cmd.accessor' => '_ws',

	'app.mounts'	=> [
		'default'	=> [
			'mount'	=> '/',
			'dir'	=> __DIR__,
		]
	],
	'app.list'	=> []
];
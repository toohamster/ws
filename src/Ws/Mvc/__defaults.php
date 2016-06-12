<?php
# 应用配置文件
#
return [
	'defaults.session_autostart' => true,
	'defaults.timezone' => 'Asia/Chongqing',
	
	'command.accessor' => '_ws',

	'app.mounts'	=> [
		'default'	=> [
			'mount'	=> '/',
			'dir'	=> __DIR__,
		]
	],
	'app.list'	=> []
];
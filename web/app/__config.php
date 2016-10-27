<?php

return [

	'app.timezone' => 'PRC',
	
	'cmd.accessor' => '_ws',

	'debug.asdebug'	=> [
		'enable'	=> true,
		'q'	=> 'asdebug',
		'qtag'	=> 'asdebug-tag',
		'secret'	=> 'toohamster',
		'dir'	=> __DIR__ . '/__storage',
	],

	'app.mounts'	=> [
		'default'	=> [
			'mount'	=> '/',
			'dir'	=> __DIR__ . '/Default',
		],
		'im'	=> [
			'mount'	=> '/im',
			'dir'	=> __DIR__ . '/Im',
		],
	],
];
<?php

return [
	
	'url.accessor' => '_ws',

	'app.mounts'	=> [
		'default'	=> [
			'mount'	=> '/',
			'dir'	=> __DIR__,
		],
		'blog'	=> [
			'mount'	=> '/blog',
			'dir'	=> __DIR__,
		]
	],
];
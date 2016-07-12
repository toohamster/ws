<?php

return [
	
	'cmd.accessor' => '_ws',

	'app.mounts'	=> [
		'default'	=> [
			'mount'	=> '/',
			'dir'	=> __DIR__ . '/App/Default',
		],
		'blog'	=> [
			'mount'	=> '/blog',
			'dir'	=> __DIR__ . '/App/Blog',
		]
	],
];
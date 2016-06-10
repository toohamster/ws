<?php

return [
	'app.mounts'	=> [
		'default'	=> [
			'mount'	=> '/',
			'src'	=> __DIR__,
		],
		'blog'	=> [
			'mount'	=> '/blog/',
			'src'	=> __DIR__,
		]
	],
];
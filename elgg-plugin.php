<?php

return [
	'plugin' => [
		'name' => 'Profile Form Prototyper',
		'version' => '7.0.0',
		'dependencies' => [
			'hypeprototyper' => [
				'must_be_active' => true,
			],
		],
	],
	'bootstrap' => \hypeJunction\PrototyperProfile\Bootstrap::class,
	'actions' => [
		'profile/prototype' => [
			'access' => 'admin',
		],
		'profile/edit' => [],
	],
	'events' => [
		'prototype' => [
			'profile/edit' => [
				\hypeJunction\PrototyperProfile\GetPrototypeFields::class => [],
			],
		],
		'profile:fields' => [
			'profile' => [
				\hypeJunction\PrototyperProfile\GetConfigFields::class => [],
			],
		],
		'view_vars' => [
			'input/form' => [
				\hypeJunction\PrototyperProfile\FilterFormVars::class => [
					'priority' => 200,
				],
			],
		],
	],
];

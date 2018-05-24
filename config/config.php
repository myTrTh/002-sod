<?php

return [
	'auth_rule' => [
		'username-min' 				=> 3,
		'username-max' 				=> 18,
		'password-min' 				=> 4
	],
	'auth'      => [
		'registration_confirmation' => true,
		'token_confirmation_time'	=> 84600,
		'lifetime'				    => 2592000,
		'lifetime_long'				=> 15000000,
		'login'						=> 'username.email'
	],
	'session' 		=> [
		'save_path' 			    => __DIR__.'/../var/sessions/',
		'cookie_lifetime' 			=> 0,
		'cookie_httponly' 			=> 1,
		'gc_maxlifetime' 			=> 15000000,
		'gc_probability' 			=> 1,
		'gc_divisor' 				=> 10
	],
	'role_hierarchy'					=> [
		1 => 'ROLE_USER',
		2 => 'ROLE_MODERATOR',
		3 => 'ROLE_ADMIN',
		4 => 'ROLE_SUPER_ADMIN'
	],
	'assets'                        => '../../public',
	'filepath'                      => __DIR__.'/../public/'
];
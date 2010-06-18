<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Set the routes for API
 */
Route::set('api', 'api(/<controller>(/<id>))', array(
		'action'	=> '\w+',
		'id'		=> '\w+',
	))
	->defaults(array(
		'directory'	=> 'api',
		'controller'=> 'core',
		'action'	=> 'index',
	));
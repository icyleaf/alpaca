<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Open link with new window only external links (modified Kohana HTML class)
 * 
 * @location: application/classes/html.php
 */
HTML::$windowed_urls = TRUE;

/**
 * Loading theme
 */
$alpaca_modules = array(
	'themes'	=> ALPPATH.'themes'.DIRECTORY_SEPARATOR.Kohana::config('alpaca.theme'),
	'api'		=> ALPPATH.'api',
);
$modules = Kohana::modules();
$modules = array_merge($modules, $alpaca_modules);
Kohana::modules($modules);

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('auth/actions', '<action>(/<code>)', array(
		'action' => '(?:register|login|logout|lostpassword|changepassword|verity)',
		'code'   => '(\w|[-])+',
	))
	->defaults(array(
		'controller'=> 'auth',
		'action'    => 'register',
	));

Route::set('user', 'user/<id>(/<type>)', array(
		'id'		=> '\w+',
		'type'		=> '\w+',
	))
	->defaults(array(
		'controller'=> 'user',
		'action'    => 'index',
	));

Route::set('topic/views', '<id>', array(
		'id' => '(?:new|hot|top)'
	))
	->defaults(array(
		'controller'=> 'forum',
		'action'    => 'index',
	));

Route::set('topic', '(group/<group_id>/)topic/<id>', array(
		'group_id'	=> '\w+',
		'id'		=> '\w+',
	))
	->defaults(array(
		'controller'=> 'topic',
		'action'    => 'view',
	));

Route::set('topic/create', 'group/<id>/new_topic', array(
		'id'		=> '(\w|[-])+',
	))
	->defaults(array(
		'controller'=> 'topic',
		'action'    => 'create',
	));

Route::set('topic/move', 'topic/move/<topic_id>(/<group_id>)', array(
		'topic_id'	=> '\d+',
		'group_id'	=> '\d+',
	))
	->defaults(array(
		'controller'=> 'topic',
		'action'    => 'move',
	));

Route::set('topic/collectors', 'topic/<id>/collectors', array(
		'id'		=> '\d+',
	))
	->defaults(array(
		'controller'=> 'topic',
		'action'    => 'collectors',
	));

Route::set('group/action', 'group/<action>', array(
		'action'	=> '(create|edit|delete)',
	))
	->defaults(array(
		'controller'=> 'group',
	));

Route::set('group', 'group/<id>', array(
		'id'		=> '(\w|[-])+',
	))
	->defaults(array(
		'controller'=> 'group',
		'action'    => 'view',
	));

// the default entry
Route::set('forum', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'forum',
		'action'     => 'index',
	));

// the media files
Route::set('media', 'media(/<file>)', array(
		'file'		=> '.+'
	))
	->defaults(array(
		'controller'=> 'forum',
		'action'    => 'media',
		'file'      => NULL,
	));


<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Open link with new window only external links (modified Kohana HTML class)
 * 
 * @location: alpaca/classes/html.php
 */
HTML::$windowed_urls = TRUE;

/**
 * Onload theme
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
Route::set('invite', 'invite')
	->defaults(array(
		'controller'=> 'auth',
		'action'    => 'invite',
	));
		
Route::set('register', 'register')
	->defaults(array(
		'controller'=> 'auth',
		'action'    => 'register',
	));
	
Route::set('login', 'login')
	->defaults(array(
		'controller'=> 'auth',
		'action'    => 'login',
	));
	
Route::set('logout', 'logout')
	->defaults(array(
		'controller'=> 'auth',
		'action'    => 'logout',
	));
	
Route::set('lostpassword', 'lostpassword')
	->defaults(array(
		'controller'=> 'auth',
		'action'    => 'lostpassword',
	));
	
Route::set('verity', 'verity(/<code>)', array(
		'code'		=> '(\w|[-])+',
	))
	->defaults(array(
		'controller'=> 'auth',
		'action'    => 'verity',
	));	
	
Route::set('changepassword', 'changepassword/<code>', array(
		'code'		=> '(\w|[-])+',
	))
	->defaults(array(
		'controller'=> 'auth',
		'action'    => 'changepassword',
	));	

Route::set('group/add', 'group/create')
	->defaults(array(
		'controller'=> 'group',
		'action'	=> 'add',
	));

Route::set('group', 'group/<id>', array(
		'id'		=> '(\w|[-])+',
	))
	->defaults(array(
		'controller'=> 'group',
		'action'    => 'view',
	));

Route::set('topic', 'topic/<id>', array(
		'id'		=> '\d+',
	))
	->defaults(array(
		'controller'=> 'topic',
		'action'    => 'view',
	));
 
Route::set('post', 'post(/<action>(/<id>))', array(
		'action'	=> '\w+',
		'id'		=> '\d+',
	))
	->defaults(array(
		'controller'=> 'post',
	));

Route::set('user/feed', 'user/<id>/feed(/<type>)', array(
		'id'		=> '\w+',
		'type'		=> '\w+',
	))
	->defaults(array(
		'controller'=> 'feed',
		'action'    => 'user',
	));

Route::set('user', 'user/<id>(/<type>)', array(
		'id'		=> '\w+',
		'type'		=> '\w+',
	))
	->defaults(array(
		'controller'=> 'user',
		'action'    => 'index',
	));

Route::set('group/action', 'group/<action>', array(
		'action'	=> '(edit|delete)',
	))
	->defaults(array(
		'controller'=> 'group',
	));

Route::set('topic/add', 'group/<id>/new_topic', array(
		'id'		=> '(\w|[-])+',
	))
	->defaults(array(
		'controller'=> 'topic',
		'action'    => 'add',
	));

Route::set('topic/move', 'topic/move/<topic_id>(/<group_id>)', array(
		'topic_id'	=> '\d+',
		'group_id'	=> '\d+',
	))
	->defaults(array(
		'controller'=> 'topic',
		'action'    => 'move',
	));

Route::set('topic/collectors', 'topic/<topic_id>/collectors', array(
		'topic_id'	=> '\d+',
	))
	->defaults(array(
		'controller'=> 'topic',
		'action'    => 'collectors',
	));

// the default entry
Route::set('forum', '(/<controller>(/<action>(/<id>)))', array(
		'controller'=> '\w+',
		'action'	=> '\w+',
		'id'		=> '\d+',
	))
	->defaults(array(
		'controller'=> 'forum',
		'action'    => 'index',
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


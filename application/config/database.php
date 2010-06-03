<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Database Configure
 *
 * hacked by icyleaf<icyleaf.cn@gmail.com>
 */
 
// Development Database Configure
$development = array
(
	'default' => array
	(
		'type'       => 'mysql',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname
			 * integer  port
			 * string   socket
			 * string   username
			 * string   password
			 * boolean  persistent
			 * string   database
			 */
			'hostname'   => 'localhost',
			'username'   => 'test',
			'password'   => 'test',
			'persistent' => FALSE,
			'database'   => 'alpaca',
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => '',
		'profiling'    => TRUE,
	),
	'alternate' => array(
		'type'       => 'pdo',
		'connection' => array(
			/**
			 * The following options are available for PDO:
			 *
			 * string   dsn
			 * string   username
			 * string   password
			 * boolean  persistent
			 * string   identifier
			 */
			'dsn'        => 'mysql:host=localhost;dbname=kohana',
			'username'   => 'root',
			'password'   => 'r00tdb',
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
);

// Production Database Configure
$production = array
(
	'default' => array
	(
		'type'       => 'mysql',
		'connection' => array(
			'hostname'   => 'localhost',
			'username'   => FALSE,
			'password'   => FALSE,
			'persistent' => FALSE,
			'database'   => 'kohana',
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
	'alternate' => array(
		'type'       => 'pdo',
		'connection' => array(
			'dsn'        => 'mysql:host=localhost;dbname=kohana',
			'username'   => 'root',
			'password'   => 'r00tdb',
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
);

// According to different environment using different configuration
if ( ! IN_PRODUCTION)
{
	return $development;
}
else
{
	return $production;
}

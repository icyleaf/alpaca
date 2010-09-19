<?php defined('SYSPATH') or die('No direct script access.');

//-- Environment setup --------------------------------------------------------

/**
 * Set the default time zone.
 *
 * @see  http://docs.kohanaphp.com/features/localization#time
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Asia/Shanghai');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://docs.kohanaphp.com/features/autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

// Define server variables
$server_addr = Arr::get($_SERVER, 'SERVER_ADDR', '127.0.0.1');
$server_port = Arr::get($_SERVER, 'SERVER_PORT', 80);
$server_name = Arr::get($_SERVER, 'SERVER_NAME');
$app_base_url = 'http://'.$server_name;
$app_base_url .= ($server_port != 80) ? ':'.$server_port : '';

/**
 * Set the production status by the ip address.
 */
define('IN_PRODUCTION', $server_addr !== '127.0.0.1');

/**
 * Define application base url
 */
define('BASE_URL', $app_base_url);

// Clean up the configuration vars
unset($server_addr, $server_port, $server_name, $app_base_url);

//-- Configuration and initialization -----------------------------------------

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
	'base_url'   => BASE_URL,
	'index_file' => FALSE,
	'profiling'  => !IN_PRODUCTION,
	'caching'    => IN_PRODUCTION,
	'cache_dir'  => DOCROOT.'cache',
	));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Kohana_Log_File(DOCROOT.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Kohana_Config_File);

/**
 * Enable Kohana modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	'auth'			=> MODPATH.'auth', 			// Basic authentication
	'database'		=> MODPATH.'database',		// Database access
	'orm'			=> MODPATH.'orm',	 		// KO3 Object Relationship Mapping
	'pagination'	=> MODPATH.'pagination',	// Paging of results
	'image'			=> MODPATH.'image',			// Image manipulation
	'gravatar'		=> MODPATH.'gravatar',	 	// Gravatar
	'twig'			=> MODPATH.'twig',			// Twig Tempalte Engine
	));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
require_once APPPATH.'init.php';

/**
 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
 * If no source is specified, the URI will be automatically detected.
 */
$request = Request::instance();
try
{
	// Attempt to execute the response
	$request->execute();
}
catch(Exception $e)
{
	if ( ! IN_PRODUCTION)
	{
		throw $e;
	}

	// Log the error
	Kohana::$log->add(Kohana::ERROR, Kohana::exception_text($e));

	// Request 404 page
	$request = Request::factory('errors')->execute();
}

if ($request->send_headers()->response)
{
	// Get the total memory and execution time
	$total = array(
		'{memory_usage}'   => number_format((memory_get_peak_usage() - KOHANA_START_MEMORY) / 1024, 2).'KB',
		'{execution_time}' => number_format(microtime(TRUE) - KOHANA_START_TIME, 5).__(' seconds')
	);

	// Insert the totals into the response
	$request->response = str_replace(array_keys($total), $total, $request->response);
}

// Display the request response.
echo $request->response;


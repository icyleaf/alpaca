<?php
/**
 * The directory in which the Alaca modules are located.
 */
$alpaca = 'modules';

/**
 * The directory in which the core directory files are located.
 */
$core = 'core';

/**
 * The directory in which your application specific resources are located.
 * The application directory must contain the config/kohana.php file.
 *
 * @see  http://docs.kohanaphp.com/install#application
 */
$application = 'core/application';

/**
 * The directory in which your modules are located.
 *
 * @see  http://docs.kohanaphp.com/install#modules
 */
$modules = 'core/modules';

/**
 * The directory in which the Kohana resources are located. The system
 * directory must contain the classes/kohana.php file.
 *
 * @see  http://docs.kohanaphp.com/install#system
 */
$system = 'core/system';

/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 *
 * @see  http://docs.kohanaphp.com/install#ext
 */
define('EXT', '.php');

/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @see  http://php.net/error_reporting
 *
 * When developing your application, it is highly recommended to enable notices
 * and strict warnings. Enable them by using: E_ALL | E_STRICT
 *
 * In a production environment, it is safe to ignore notices and strict warnings.
 * Disable them by using: E_ALL ^ E_NOTICE
 */
error_reporting(E_ALL | E_STRICT);

/**
 * End of standard configuration! Changing any of the code below should only be
 * attempted by those with a working knowledge of Kohana internals.
 *
 * @see  http://docs.kohanaphp.com/bootstrap
 */

// Set the full path to the docroot
define('DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

// Make the alpaca relative to the docroot
if ( ! is_dir($alpaca) AND is_dir(DOCROOT.$alpaca))
	$alpaca = DOCROOT.$alpaca;

// Make the core relative to the docroot
if ( ! is_dir($core) AND is_dir(DOCROOT.$core))
	$core = DOCROOT.$core;

// Make the application relative to the docroot
if ( ! is_dir($application) AND is_dir(DOCROOT.$application))
	$application = DOCROOT.$application;

// Make the modules relative to the docroot
if ( ! is_dir($modules) AND is_dir(DOCROOT.$modules))
	$modules = DOCROOT.$modules;

// Make the system relative to the docroot
if ( ! is_dir($system) AND is_dir(DOCROOT.$system))
	$system = DOCROOT.$system;

// Define the absolute paths for configured directories
define('ALPPATH', realpath($alpaca).DIRECTORY_SEPARATOR);
define('COREPATH', realpath($core).DIRECTORY_SEPARATOR);
define('APPPATH', realpath($application).DIRECTORY_SEPARATOR);
define('MODPATH', realpath($modules).DIRECTORY_SEPARATOR);
define('SYSPATH', realpath($system).DIRECTORY_SEPARATOR);

// Clean up the configuration vars
unset($alpaca, $core, $application, $modules, $system);

/**
 * Define the start time of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_TIME'))
{
	define('KOHANA_START_TIME', microtime(TRUE));
}

/**
 * Define the memory usage at the start of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_MEMORY'))
{
	define('KOHANA_START_MEMORY', memory_get_usage());
}

// Bootstrap the application
require COREPATH.'bootstrap'.EXT;

/**
 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
 * If no source is specified, the URI will be automatically detected.
 */

$response = NULL;
try
{
	// Attempt to execute the response
	$response = Request::factory()
		->send_headers()
		->execute();
}
catch(Exception $e)
{
	if ( ! IN_PRODUCTION)
	{
		throw $e;
	}

	// Log the error
	Kohana::$log->add(Kohana_Log::ERROR, Kohana_Exception::text($e));

	// Request 404 page
	$response = Request::factory('errors')
		->execute();
}

if ($response->body())
{
	// Get the total memory and execution time
	$total = array(
		'{memory_usage}'   => number_format((memory_get_peak_usage() - KOHANA_START_MEMORY) / 1024, 2).'KB',
		'{execution_time}' => number_format(microtime(TRUE) - KOHANA_START_TIME, 5).__(' seconds')
	);

	// Insert the totals into the response
	$response->body(str_replace(array_keys($total), $total, $response->body()));
}

// Display the request response.
echo $response->body();

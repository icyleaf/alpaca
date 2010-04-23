<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	// Project Alpaca
	'project'		=>	array(
		'name'			=> 'Alpaca',
		'version'		=> '0.2.2',
		'codename'		=> 'whelp',
		'author'		=> 'icyleaf',
		'url'			=> 'http://khnfans.cn',
	),
	
	// The start time of the application
	'execution_time'=> TRUE,
	
	// Display throw exception informations and debug information at bottom of page
	'debug'			=> TRUE,
	
	// Website Maintenance
	'maintenance'	=> TRUE,
	
	// General website settings
	'title'			=> 'Kohana 中文社区',
	'desc'			=> 'Powered by Alpaca',
	'logo'			=> 'logo.jpg',
	'date_format'	=> __('Y-m-d H:i:s'),
	'topic'			=> array(
		'per_on_entry'	=> 15,
		'per_on_group'	=> 25,
	),
	'post'			=> array(
		'per'			=> 40,
	),
	'feed'			=> array(
		'per'			=> 10,
		'excerpt'		=> FALSE,
	),
);


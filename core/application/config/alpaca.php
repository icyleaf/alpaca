<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Alpaca global configuration
 */
$config = array
(
	// Project Alpaca
	'project'		=>	array
	(
		'name'			=> 'Alpaca',
		'version'		=> '0.4',
		'codename'		=> 'whelp',
		'author'		=> 'icyleaf',
		'url'			=> 'http://kohana.cn',
	),
	// start_year, left it empty, the default is current year.
	'copyright_year'=> 2008,
	
	// The start time of the application
	'execution_time'=> TRUE,
	
	// Display throw exception informations and debug information at bottom of page
	'debug'			=> TRUE,
	
	// Website Maintenance
	'maintenance'	=> FALSE,
	
	// General website settings
	'title'			=> 'Kohana 中文',
	'desc'			=> '打造最好的 Kohana 中文技术交流平台',
	'logo'			=> 'media/images/kohana.png',
	'date_format'	=> __('Y-m-d H:i:s'),
	'language'		=> 'zh-cn',
	'theme'			=> 'twig',
		
	// Control repeat post topic. (ONLY post once if it just the same content.)
	// set 'FALSE' means it is't allow repeat post.
	'topic_repeat'	=> FALSE,
	
	// How many items will render
	'topic'			=> array
	(
		'per_page'		=> 20,
	),
	'post'			=> array
	(
		'per_page'		=> 50,
	),
	'feed'			=> array
	(
		'per_page'		=> 10,
		'cache'			=> 0,  // seconds
	),
	
	// temp broadcast 
	'broadcast'		=> NULL,

	// Google Analytics
	'ga_account_id'	=> '',
	
	// (Optional) send mail by smtp server, the default is used by self function of server.
	'smtp_server'	=> array
	(
		'host'			=> '',
		'port'			=> 25,
		'username'		=> '',
		'password'		=> '',
	),
);

/**
 * Holiday LOGO
 *
 *	Date format: YYYY-MM-DDThh:mm::ss+hh::mm
 *	E.g, 2009-11-11T11:11:11+08:00
 *	It means Nov. 11th 2009 11:11:11 in Timezone 08:00
 *
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
$holiday = array
(
	'start_date'	=> '2009-12-22T00:00:00+08:00',
	'end_date'		=> '2009-12-25T23:59:59+08:00',
	'logo'			=> 'media/images/kohana_xmas.png',
);
if ((strtotime($holiday['start_date']) < time()) AND (strtotime($holiday['end_date']) > time()))
{
	if (file_exists(ALPPATH.$holiday['logo']))
	{
		$config['logo'] = $holiday['logo'];
	}
}






return $config;


<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Installer
 *
 * @package controller
 * @author icyleaf
 *
 * @TODO: Finish it
 */
class Controller_Installer extends Controller {

	/**
	 * Installer Entry
	 */
	public function action_index()
	{
		// Database
		$db_name = Kohana::config('alpaca.database');
		$db_config = Kohana::config('database.'.$db_name);
		
		echo Kohana::debug($db_config);

		// Module
		$must_modules = array('auth', 'gravatar');
		$all_modules = Kohana::modules();
		
		$errors = array();
		
		foreach ($must_modules as $module)
		{
			if ( ! array_key_exists($module, $all_modules))
			{
				$errors[] = 'No activate the "'.$module.'" module';
			}
			else
			{
				if ( ! is_dir($all_modules[$module]))
				{
					$errors[] = 'No found the "'.$module.'" folder';
				}
			}
		}
		
		echo Kohana::debug($errors);
	}
	
}


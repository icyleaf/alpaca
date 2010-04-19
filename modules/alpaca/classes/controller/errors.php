<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Error Entry
 *
 * @package controller
 * @author icyleaf
 */
class Controller_Errors extends Controller_Alpaca {
	
	/**
	 * General normal error page
	 */
	public function action_index()
	{
		$this->template->content = View::factory('errors/general');
	}
	
	/**
	 * Display 404 error page
	 */
	public function action_404()
	{
		$this->request->status = 404;
		$this->header->title->prepend(__('No Found the page'));
		$this->template->content = View::factory('errors/404');
	}
	
}


<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Error Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Errors extends Controller_Template_Alpaca {
	
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
		$code = 404;
		$title = __('No Found the page');
		$this->request->status = $code;
		$this->head->title->prepend($title);
		$this->template->content = View::factory('errors/404')
			->set('title', $title)
			->bind('code', $code);
	}
	
}


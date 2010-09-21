<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Error Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Errors extends Controller_Template_Alpaca {
	
	/**
	 * Error page
	 */
	public function action_index()
	{
		$code = 404;
		$title = __('No Found the page');
		$content = NULL;

		$this->request->status = $code;
		$this->head->title->prepend($title);
		$this->template->content = Twig::factory('template/errors')
			->set('title', $title)
			->bind('code', $code)
			->bind('content', $content);
	}

}


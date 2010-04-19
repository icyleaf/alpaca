<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca ToDo List Entry
 *
 * @package controller
 * @author icyleaf
 */
class Controller_Todo extends Controller_Template {
	
	public $template = 'todo';
	public $list = NULL;
	
	protected $config = NULL;
	
	public function before()
	{
		$this->template = View::factory($this->template);
	}
	
	/**
	 * TODO Entry
	 */
	public function action_index()
	{
		$header = Head::instance();
		$this->config = Kohana::config('alpaca');
		$this->list = ORM::factory('todo');
		
		// Title
		$info = array
		(
			'title'			=> $this->config->project['name'],
			'version'		=> $this->config->project['version'],
			'desc'			=> 'ToDo List',
		);
		$header->title->set($info['title']);
		$header->title->append($info['desc']);
		$header->css->append_file('media/css/todo.css');
		
		$this->template->config = Kohana::config('alpaca');
		$this->template->header = $header;
		$this->template->info = $info;
		$this->template->list = $this->list;
	}
	
	// TODO: Add/Edit/Delete methods
}


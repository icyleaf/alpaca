<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Search Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Search extends Controller_Alpaca {
	
	private $_rules = array
	(
		'q'				=> array
		(
			'not_empty'	=> NULL,
		)
	);
	
	public function action_index()
	{
		$this->template->content = View::factory('search/topic')
			->bind('query', $query)
			->bind('total', $result_total)
			->bind('topics', $topics)
			->bind('pagination', $pagination);
			
		$this->template->sidebar = '';
				
		if ($_GET)
		{
			$get = Validate::factory($_GET)
				->filter(TRUE, 'trim')
				->rules('q', $this->_rules['q']);
				
			if ($get->check())
			{
				$pagination = Pagination::factory();
				$query = Arr::get($_GET, 'q');
				$type = Arr::get($_GET, 'type' , 'topic');
				$result_total = ORM::factory($type)
					->search($query)
					->find_all()
					->count();
					
				$topics = ORM::factory($type)
					->search($query)
					->limit($pagination->items_per_page)
					->offset($pagination->offset)
					->find_all();
	
				$pagination->setup(array(
					'view'				=> 'pagination/digg',
					'total_items' 		=> $result_total,
					'items_per_page'	=> $this->config->topic['per_page'],
				));
				
				$list_topic = View::factory('topic/list')
					->bind('topics', $topics)
					->bind('pagination', $pagination);
			}
			
		}
	}
}


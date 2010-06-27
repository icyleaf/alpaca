<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Search Entry
 *
 * TODO: ONLY support for topic
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Search extends Controller_Alpaca {
	
	private $_rules = array
	(
		'q'		=> array
		(
			'not_empty'	=> NULL,
		)
	);
	
	public function action_index()
	{
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
				$total = ORM::factory($type)->search_count($query);
				$topics = ORM::factory($type)->search($query, $pagination->items_per_page, $pagination->offset);

				$pagination->setup(array(
					'view'				=> 'pagination/digg',
					'total_items' 		=> $total,
					'items_per_page'	=> $this->config->topic['per_page'],
				));
				
				$list_topic = View::factory('topic/list')
					->bind('topics', $topics)
					->bind('pagination', $pagination);
			}
			
		}

		$this->template->content = View::factory('search/topic')
			->bind('query', $query)
			->bind('total', $total)
			->bind('topics', $topics)
			->bind('pagination', $pagination);

		$this->template->sidebar = '';
	}

}


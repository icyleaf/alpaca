<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Search Entry
 *
 * TODO: ONLY support for topic
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Search extends Controller_Template_Alpaca {
	
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
				$total = ORM::factory($type)->search($query)->count();

				$pagination->setup(array(
					'view'				=> 'pagination/digg',
					'total_items' 		=> $total,
					'items_per_page'	=> $this->config->topic['per_page'],
				));

				if ($total > 0)
				{
					$topics = ORM::factory($type)->search($query, $pagination->items_per_page, $pagination->offset);
					$topics_array = ORM::factory('topic')->topics_list_array($topics);

					$list_topic = Twig::factory('topic/list')
						->bind('topics', $topics_array)
						->bind('pagination', $pagination);
				}
			}
		}

		$this->template->content = Twig::factory('search/topic')
			->bind('query', $query)
			->bind('total', $total)
			->bind('list_topic', $list_topic)
			->bind('topics', $topics)
			->bind('pagination', $pagination);

		// about
		$about_content = $this->config->about;
		$about = Twig::factory('sidebar/about')
			->set('stats', $this->_generate_stats())
			->bind('about', $about_content);

		$this->template->sidebar = $about;
	}

}


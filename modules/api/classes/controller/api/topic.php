<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Topic API
 *
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_API_Topic extends Controller_API_Core {

	private $_alt = 'json';
	private $_max = 20;

	public function before()
	{
		$this->_alt = Arr::get($_GET, 'alt', $this->_alt);
		$this->_max = Arr::get($_GET, 'max', $this->_max);
		$this->_max = $this->_max > 25 ? 25 : $this->_max;
		
		parent::before();
	}

	public function action_index($type = NULL)
	{
		if ( ! empty($type))
		{
			$topic_model = ORM::factory('topic');
			$topics = FALSE;
			switch ($type)
			{
				case 'latest':
					$title = '最新发布话题';
					$topics = $topic_model->get_topics('touched', $this->_max);
					break;
				case 'hits':
					$title = '最高关注话题';
					$topics = $topic_model->get_topics('hits', $this->_max);
					break;
				case 'collections':
					$title = '最高收藏话题';
					$topics = $topic_model->get_topics('collections', $this->_max);
					break;
			}

			if ($topics)
			{
				$i = 0;
				$entry = array();
				foreach ($topics as $topic)
				{
					$group = $topic->group;
					$group = array(
						'id'		=> $group->id,
						'name'		=> $group->name,
						'link'		=> URL::site(Route::get('group')->uri(array(
								'id'	=> Alpaca_Group::the_uri($group)
								))),
						);

					$author = $topic->author;
					$author = array(
						'id'		=> $author->id,
						'username'	=> $author->username,
						'nickname'	=> $author->nickname,
						'avatar'	=> Alpaca_User::avatar($author)->__toString(),
						'location'	=> $author->location,
						'website'	=> $author->website,
						);
					
					$entry[$i] = array(
						'id'		=> $topic->id,
						'author'	=> $author,
						'title'		=> $topic->title,
						'group'		=> $group,
						'comments'	=> $topic->count,
						'hits'		=> $topic->hits,
						'created'	=> date('r', $topic->created),
						);

					$i++;
				}

				$output = array(
					'title'	=> $title,
					'link'	=> URL::site($this->request->uri),
					'entry' => $entry,
				);

				$this->_render($output);
			}
			else
			{
				$this->_render('Unkown action: ' . $type);
			}
		}
		else
		{
			$this->request->status = 404;
		}
	}

}
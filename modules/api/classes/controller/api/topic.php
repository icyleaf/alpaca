<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Topic API
 *
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_API_Topic extends Controller_API_Core {

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
				case 'new':
					$title = __('Latest topics');
					$topics = $topic_model->get_topics('touched', $this->_max);
					break;
				case 'hot':
					$title = __('Top hit topics');
					$topics = $topic_model->get_topics('hits', $this->_max);
					break;
				case 'top':
					$title = __('Top collection topics');
					$topics = $topic_model->get_topics('collections', $this->_max);
					break;
			}

			if ($topics)
			{
				$entry = array();
				foreach ($topics as $key => $topic)
				{
					$group = $topic->group;
					$group = array(
						'id' => $group->id,
						'name' => $group->name,
						'link' => Route::url('group', array(
							'id' => Alpaca_Group::uri($group)
						)),
					);

					$author = $topic->author;
					$author = array(
						'id' => $author->id,
						'username'	=> $author->username,
						'nickname'	=> $author->nickname,
						'avatar' 	=> Alpaca_User::avatar($author)->__toString(),
						'location'	=> $author->location,
						'website'	=> $author->website,
					);

					$entry[$key] = array(
						'id'		=> $topic->id,
						'author'	=> $author,
						'title'		=> $topic->title,
						'group'		=> $group,
						'comments'	=> $topic->count,
						'hits'		=> $topic->hits,
						'created'	=> date('r', $topic->created),
						'link'		=> Alpaca_Topic::url($topic),
					);
				}

				$output = array(
					'title' => $title,
					'link' => URL::site($this->request->uri()),
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
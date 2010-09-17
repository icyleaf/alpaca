<?php defined('SYSPATH') or die('No direct script access.');

class Model_Topic extends ORM {
	
	// Relationships
	protected $_belongs_to = array(
		'author'	=> array(
			'model' 		=> 'user',
			'foreign_key' 	=> 'user_id',
		),
		'group' 	=> array(),
	);
	protected $_has_many = array(
		'posts' 	=>	array(
			'model' 		=> 'post'
		)
	);
	
	// Validate
	protected $_filters = array(
		TRUE 		=> array('trim' 	 => NULL)
	);
	protected $_rules = array(
		'group_id'	=> array('not_empty' => NULL),
		'user_id' 	=> array('not_empty' => NULL),
		'title' 	=> array('not_empty' => NULL),
		'content' 	=> array('not_empty' => NULL),
		'created' 	=> array('digit' 	 => NULL)
	);
	
	protected $_operators = array('and', 'or');
	
	/**
	 * Get topics
	 *
	 * @param string $name 
	 * @param int $max 
	 * @param int $index 
	 * @param int $cache 
	 * @param string $sort 
	 * @return ORM
	 */
	public function get_topics($name = NULL, $max = 10, $index = 0, $cache = 0, $sort = 'DESC')
	{
		if ($max > 0)
		{
			$this->offset($index)->limit($max);
		}
		
		if ($cache > 0)
		{
			$this->cached($cache);
		}
		
		$name = ($name) ? $name : $this->_primary_key;
		$this->order_by($name, $sort);
		
		return $this->find_all();
	}

	/**
	 * Convert model to array for template
	 *
	 * @param  $topics
	 * @return array
	 */
	public function topics_list_array($topics)
	{
		$topics_array = array();
		if ($topics->count() > 0)
		{
			foreach ($topics as $i => $topic)
			{
				$author = $topic->author;
				$author_array = array(
					'id'		=> $author->id,
					'avatar'	=> Alpaca_User::avatar($author, array('size' => 30), array('class' => 'avatar'), TRUE),
					'nickname'	=> $author->nickname,
					'link'		=> Alpaca_User::url('user', $author)
				);
				$author_array = (object) $author_array;

				$group = $topic->group;
				$group_array = array(
					'id'		=> $group->id,
					'name'		=> $group->name,
					'link'		=> Route::url('group', array('id' => Alpaca_Group::uri($group))),
				);
				$group_array = (object) $group_array;

				$collected = ORM::factory('collection')->is_collected($topic->id, $author->id);
				$topics_array[$i] = array(
					'id'			=> $topic->id,
					'title'			=> $topic->title,
					'link'			=> Alpaca_Topic::url($topic, $group),
					'author'		=> $author_array,
					'group'			=> $group_array,
					'collections'	=> $topic->collections,
					'comments'		=> $topic->count,
					'hits'			=> $topic->hits,
					'collected'		=> $collected,
					'content'		=> Alpaca::format_html($topic->content),
					'created'		=> date(Kohana::config('alpaca')->date_format, $topic->created),
					'time_ago'		=> Alpaca::time_ago($topic->created),
					'updated'		=> Alpaca::time_ago($topic->updated),
				);

				$topics_array[$i] = (object) $topics_array[$i];
			}
		}

		return $topics_array;
	}

	public function topic_detail_array($topic)
	{
		$author = $topic->author;
		return array(
			'id'			=> $topic->id,
			'title'		    => $topic->title,
			'user_avatar'	=> Alpaca_User::avatar($author, NULL, TRUE),
			'author_link'	=> HTML::anchor(Alpaca_User::url('user', $author), $author->nickname),
			'content'		=> Alpaca::format_html($topic->content),
			'created'		=> date(Kohana::config('alpaca')->date_format, $topic->created),
			'time_ago'		=> Alpaca::time_ago($topic->created),
		);
	}

	public function get_topics_by_user($limit = NULL, $cache = 120)
	{
		return $this->limit($limit)
			->order_by('created', 'DESC')
			->cached($cache)
			->find_all();
	}

	/**
	 * Get user posted topics
	 *
	 * @param int $user_id
	 * @return object
	 */
	public function posted_topics_by_user($user_id, $limit = NULL, $cache = 120)
	{
		$this->distinct('topics.id')
			->join('posts')
			->on('posts.topic_id', '=', 'topics.id')
			->where('posts.user_id', '=', $user_id)
			->order_by('posts.created', 'DESC');
		
		if ( ! empty($cache))
		{
			$this->cached($cache);
		}

		return $this->find_all();
	}

	/**
	 * Get user collectioned topics
	 *
	 * @param int $user_id
	 * @return object
	 */
	public function collectioned_topics_by_user($user_id, $limit = NULL, $cache = 120)
	{
		$this->join('collections')
			->on('collections.topic_id', '=', 'topics.id')
			->where('collections.user_id', '=', $user_id)
			->order_by('collections.created', 'DESC');

		if ( ! empty($cache))
		{
			$this->cached($cache);
		}
		
		return $this->find_all();
	}

	/**
	 * Search topics
	 * 
	 * @param  $query
	 * @param int $limit
	 * @param int $offset
	 * @param int $cache
	 * @return ORM
	 */
	public function search($query, $limit = 0, $offset = 0, $cache = 120)
	{
		$keywords = explode(' ', $query);
		$keyword_count = count($keywords);
		$current_keyword = '';
		$keyword_array = array();
		// process keywords
		for ($i = 0; $i < $keyword_count; $i++)
		{
			$current_keyword = Alpaca::force_string($keywords[$i]);
			if ($current_keyword != '')
			{
				if (in_array(strtolower($current_keyword), $this->_operators))
				{
					if ($i+1 < $keyword_count)
					{
						$i++;
						$next_keyword = Alpaca::force_string($keywords[$i]);
						$keyword_array[] = array
						(
							'operator' => $current_keyword,
							'keyword' => $next_keyword
						);
					}
				}
				else
				{
					$keyword_array[] = array
					(
						'operator' => 'and', 
						'keyword' => $current_keyword
					);
				}
			}
		}
		// build sql query
		if (count($keyword_array) > 0)
		{
			$first_flag = TRUE;
			foreach ($keyword_array as $query)
			{
				$content = '%'.$query['keyword'].'%';
				if ($first_flag)
				{
					$this->where_open()
						->where('title', 'LIKE', $content)
						->or_where('content', 'LIKE', $content)
						->where_close();
						
					$first_flag = FALSE;
				}
				else
				{
					$where_open = $query['operator'].'_where_open';
					$where_close = $query['operator'].'_where_close';
					$this->$where_open()
						->where('title', 'LIKE', $content)
						->or_where('content', 'LIKE', $content)
						->$where_close();
				}
			}
			
			$this->order_by('created', 'DESC');
		}

		if ( ! empty($limit))
		{
			$this->limit($limit);
		}

		if ( ! empty($offset))
		{
			$this->offset($offset);
		}

		if ( ! empty($cache))
		{
			$this->cached($cache);
		}

		return $this->find_all();
	}

	public function find_topic(Array $data)
	{
		$first_where = (count($data) > 0) ? TRUE : FALSE;
		
		foreach ($data as $key => $value)
		{
			if ($first_where)
			{
				$this->where($key, '=', $value);
				$first_where = FALSE;
			}
			else
			{
				$this->and_where($key, '=', $value);
			}
		}

		return $this->find();
	}
	
	public function values($values)
	{
		foreach ($values as $key => $value)
		{
			if ($key == 'content')
			{
				continue;
			}
			$values[$key] = Security::xss_clean($value);
		}
		
		return parent::values($values);
	}
	
	public function save()
	{
		if ($this->_changed AND ! array_key_exists('hits', $this->_changed))
		{
			if ( ! empty($this->created))
			{
				$this->updated = time();
			}
			else
			{
				$this->created = $this->updated = $this->touched = time();
			}
		}

		parent::save();
	}
}


<?php defined('SYSPATH') or die('No direct script access.');

class Model_Post extends ORM {
	
	// Relationships
	protected $_belongs_to = array(
		'topic'	=> array(),
		'author'	=> array(
			'model' 		=> 'user',
			'foreign_key'	=> 'user_id',
		),
	);
	protected $_has_many = array(
		'replies'	=> array(
			'model' 		=> 'post',
			'foreign_key'	=> 'reply_id',
		),
	);
	
	// Validate
	protected $_filters = array(
		TRUE 		=> array('trim' => NULL)
	);
	protected $_rules = array(
		'topic_id'	=> array('not_empty' => NULL),
		'user_id' 	=> array('not_empty' => NULL),
		'content'	=> array('not_empty' => NULL),
		'created'	=> array('digit' => NULL)
	);
	
	protected $_operators = array('and', 'or');
	
	/**
	 * Get all the topic's posts
	 *
	 * @param boolean $thread
	 * @param int $limit
	 * @param int $offset
	 * @param int $cache 
	 * @return ORM
	 */
	public function get_posts($thread = FALSE, $limit = 0, $offset = 0, $cache = 0)
	{
		if ($thread)
		{
			$this->where('reply_id', '!=', 0);
		}
		else
		{
			$this->where('reply_id', '=', 0);
		}

		if ( ! empty($limit))
		{
			$this->offset($offset)->limit($limit);
		}
		
		if ( ! empty($cache))
		{
			$this->cached($cache);
		}
		
		$this->order_by('created', 'ASC');
		
		return $this->find_all();
	}

	public function post_list_array($topic, $posts, $auth_user)
	{
		$post_details = array();
		if ($posts->count() > 0)
		{
			foreach ($posts as $key => $post)
			{
				$post_actions = array();
				if ($auth_user)
				{
					$has_admin_role = $auth_user->has_role('admin');
					if (($auth_user->id == $post->author->id) OR $has_admin_role)
					{
						$post_actions[] = HTML::anchor('post/delete/' . $post->id, __('Delete'), array(
							'class'	=> 'delete',
							'title'	=> __('Delete Reply'),
							'rel'	=> __('Do you really want to delete this reply?'),
						));
						$post_actions[] = HTML::anchor('post/edit/' . $post->id, __('Edit'), array(
							'class'	=> 'edit',
							'title'	=> __('Edit Reply'),
						));
					}
				}

				$avatar_config = array
				(
					'default'	=> URL::site('media/images/user-default-small.jpg'),
					'size'		=> 30
				);

				$post_avatar = Alpaca_User::avatar($post->author, $avatar_config, array(
					'id' => 'avatar-'.$post->id,
					'class' => 'avatar',
					TRUE
				));

				$post_author = HTML::anchor(Alpaca_User::url('user', $post->author), $post->author->nickname);

				$post_role = ($topic->author->id == $post->author->id) ? 'owner' : 'poster';
				$post_details[$key] = array(
					'id'		=> $post->id,
					'role'		=> $post_role,
					'actions'	=> $post_actions,
					'author'	=> $post_author,
					'avatar'	=> $post_avatar,
					'content'	=> Alpaca::format_html($post->content),
					'created'	=> date(Kohana::config('alpaca')->date_format, $post->created),
					'time_ago'	=> Alpaca::time_ago($post->created),
				);

				$post_details[$key] = (object)$post_details[$key];
			}
		}

		return $post_details;
	}

	
	/**
	 * Search posts
	 * @param string $query
	 * @return mixed
	 */
	public function search($query)
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
					$this->where('content', 'LIKE', $content);
						
					$first_flag = FALSE;
				}
				else
				{
					$method = $query['operator'].'_where';
					$this->$method('content', 'LIKE', $content);
				}
			}
			
			if ($max > 0)
			{
				$this->offset($index)->limit($max);
			}
			
			$this->order_by('created', 'DESC');
		}
		
		return $this;
	}
	
	public function values(array $values, array $expected = NULL)
	{
		foreach ($values as $key => $value)
		{
			if ($key == 'content')
			{
				continue;
			}
			$values[$key] = Security::xss_clean($value);
		}
		
		return parent::values($values, $expected);
	}
	
	public function save(Validation $validation = NULL)
	{
		if ( ! empty($this->created))
		{
			$this->updated = time();
		}
		else
		{
			$this->created = $this->updated = time();
		}
		
		parent::save($validation);
	}
	
}


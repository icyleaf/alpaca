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
	 * Search topics
	 * @param string $query
	 * @return object
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

		return $this;
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


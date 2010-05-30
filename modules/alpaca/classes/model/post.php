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
	 * Get all the specifically posts
	 *
	 * @param string $poster 
	 * @param int $max 
	 * @param int $index 
	 * @param int $cache 
	 * @return ORM
	 */
	public function get_list($poster = TRUE, $max = 10, $index = 0, $cache = 0)
	{
		if ($max)
		{
			$this->offset($index)->limit($max);
		}
		
		if (is_int($cache) AND $cache > 0)
		{
			$this->cached($cache);
		}
		
		if ($poster)
		{
			$this->where('reply_id', '=', 0);
		}
		else
		{
			$this->where('reply_id', '!=', 0);
		}
		
		$this->order_by('created', 'ASC');
		
		return $this->find_all();
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
	
	public function values($values)
	{
		foreach ($values as $key => $value)
		{
			$values[$key] = Security::xss_clean($value);
		}
		
		return parent::values($values);
	}
	
	public function save()
	{
		if ( ! empty($this->created))
		{
			$this->updated = time();
		}
		else
		{
			$this->created = $this->updated = time();
		}
		
		parent::save();
	}
	
}


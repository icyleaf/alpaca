<?php defined('SYSPATH') or die('No direct script access.');

class Model_Todo extends ORM {
	
	// Validate
	protected $_filters = array(
		TRUE 		=> array('trim' => NULL)
	);
	protected $_rules = array(
		'title' 	=> array('not_empty' => NULL),
		'created'	=> array('digit' => NULL)
	);
	
	/**
	 * get all todo lists
	 *
	 * @param int $progress 
	 * @param int $cache 
	 * @param string $sort 
	 * @return ORM
	 */
	public function todo_list($progress, $cache = 0, $sort = 'ASC')
	{
		$progress = ($progress=='done') ? 1 : 0;
		$this->where('progress', '=', $progress);
		
		if (is_int($cache) AND $cache > 0)
		{
			$this->cached($cache);
		}
		
		$this->order_by('created', $sort);
		
		return $this->find_all();
	}
	
	/**
	 * Get last updated date
	 *
	 * @return int
	 */
	public function last_updated()
	{
		$result = $this->select('created')
			->limit(1)
			->order_by('created', 'DESC')
			->find();
			
		if ($result->loaded())
		{
			return $result->created;
		}
		else
		{
			return 0;
		}
	}
	
}


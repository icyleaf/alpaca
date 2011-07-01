<?php defined('SYSPATH') or die('No direct script access.');

class Model_Group extends ORM {
	
	// Relationships
	protected $_belongs_to = array(
		'user'		=> array(),
		'parent'	=> array(
			'model' 		=> 'group',
			'foreign_key'	=> 'parent_id',
		),
	);
	protected $_has_many = array(
		'children'	=> array(
			'model' 		=> 'group',
			'foreign_key'	=> 'parent_id',
		),
		'topics' 	=>	array(
			'model' 		=> 'topic'
		),
		'users' 	=>	array(
			'model' 		=> 'user',
			'through' 		=> 'groups_users'
		),
	);
	
	// Validate
	protected $_filters = array(
		TRUE		=> array('trim' => NULL)
	);
	protected $_rules = array(
		'name' 		=> array('not_empty' => NULL),
		'created'	=> array('digit' => NULL)
	);
	
	/**
	 * Get hottest groups
	 *
	 * @param int $level 
	 * @return ORM
	 */
	public function hot($level = 1)
	{
		return $this->where('level', '=', $level)
			->order_by('hits', 'DESC')
			->find_all();
	}

	public function get_group($group_id)
	{
		return $this->where($this->unique_key($group_id), '=', $group_id)
			->find();
	}

	/**
	 * Allows a model use both id and uri as unique identifiers
	 *
	 * @param   string  unique value
	 * @return  string  field name
	 */
	public function unique_key($value)
	{
		return is_numeric($value) ? $this->pk() : 'uri';
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


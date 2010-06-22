<?php defined('SYSPATH') or die('No direct script access.');

class Model_Collection extends ORM {
	
	// Relationships
	protected $_belongs_to = array(
		'topic'	=> array(),
		'user'		=> array(),
	);
	
	// Validate
	protected $_filters = array(
		TRUE => array('trim' => NULL)
	);
	protected $_rules = array(
		'user_id'				=> array
		(
			'not_empty'			=> NULL,
			'min_length'			=> array(7),
			'validate::numeric'	=> NULL,
		),
		'topic_id'				=> array
		(
			'not_empty'			=> NULL,
			'min_length'			=> array(7),
			'validate::numeric'	=> NULL,
		),
	);

	/**
	 * Check the topic if collected by user
	 *
	 * @param string $topic_id
	 * @param string $user_id
	 * @return boolean
	 */
	public function is_collected($topic_id, $user_id)
	{
		$result = $this->where('user_id', '=', $user_id)
			->and_where('topic_id', '=', $topic_id)
			->find();

		return $result->loaded();
	}
	
	public function values($values)
	{
		foreach ($values as $key => $value)
		{
			$values[$key] = Security::xss_clean($value);
		}
		
		if (isset($values['privacy']) AND $values['privacy'] != 'public')
		{
			$values['privacy'] = 'private';
		}
		
		return parent::values($values);
	}
	
	public function save()
	{
		if ($this->_changed AND empty($this->created))
		{
			$this->created = time();
		}

		parent::save();
	}
}


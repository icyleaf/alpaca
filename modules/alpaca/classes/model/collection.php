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


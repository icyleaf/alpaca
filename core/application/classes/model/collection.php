<?php defined('SYSPATH') or die('No direct script access.');

class Model_Collection extends ORM {
	
	// Relationships
	protected $_belongs_to = array(
		'topic'	=> array(),
		'user'	=> array(),
	);
	
	// Validate
	protected $_filters = array(
		TRUE => array('trim' => NULL)
	);
	protected $_rules = array(
		'user_id'				=> array
		(
			'not_empty'			=> NULL,
			'min_length'		=> array(7),
			'numeric'	=> NULL,
		),
		'topic_id'				=> array
		(
			'not_empty'			=> NULL,
			'min_length'		=> array(7),
			'numeric'	=> NULL,
		),
	);

	public function get_collectors($topic_id)
	{
		$collections = $this->where('topic_id', '=', $topic_id)
			->find_all();

		if ($collections->count() > 0 )
		{
			$user_array = array();
			foreach ($collections as $i => $collection)
			{
				$user = $collection->user;
				$user_array[$i] = $user->as_array();
				$user_array[$i]['avatar'] = Alpaca_User::avatar($user, array('size' => 48), array('class' => 'avatar'), TRUE);
				$user_array[$i]['nickname'] = (strlen($user->nickname) > 24) ? substr($user->nickname, 0, 24).'...' : $user->nickname;
				$user_array[$i]['link'] = Alpaca_User::url('user', $user);
			}

			return $user_array;
		}

		return FALSE;
	}

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
			->where('topic_id', '=', $topic_id)
			->find();

		return $result->loaded();
	}
	
	public function values(array $values, array $expected = NULL)
	{
		foreach ($values as $key => $value)
		{
			$values[$key] = Security::xss_clean($value);
		}
		
		if (isset($values['privacy']) AND $values['privacy'] != 'public')
		{
			$values['privacy'] = 'private';
		}
		
		return parent::values($values, $expected);
	}
	
	public function save(Validation $validation = NULL)
	{
		if ($this->_changed AND empty($this->created))
		{
			$this->created = time();
		}

		parent::save($validation);
	}
}


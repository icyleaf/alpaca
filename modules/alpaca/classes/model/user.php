<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends Model_Auth_User {
			
	// Relationships
	protected $_has_many = array(
		// forum
		'topics' 	=>	array(
			'model' 	=> 'topic'
		),
		'posts'		=>	array(
			'model' 	=> 'post'
		),
		'favorites' =>	array(
			'model' 	=> 'favorite'
		),
		'messages' 	=>	array(
			'model' 	=> 'message'
		),
		'friends' 	=>	array(
			'model'		=> 'user'
		),
		'groups' 	=> array(
			'model' 	=> 'group', 
			'through' 	=> 'groups_users'
		),
		'collections' 	=> array(
			'model' 	=> 'collection', 
		),
		// auth
		'user_tokens' => array(
			'model' 	=> 'user_token'
		),
		'roles' 	=> array(
			'model' 	=> 'role', 
			'through' 	=> 'roles_users'
		),
	);
	
	// Validate
	protected $_filters = array(
		TRUE => array('trim' => NULL)
	);
	protected $_rules = array
	(
		'email'				=> array
		(
			'not_empty'			=> NULL,
			'max_length'		=> array(127),
			'validate::email'	=> NULL,
		),
		'password'			=> array
		(
			'not_empty'			=> NULL,
			'min_length'		=> array(5),
			'max_length'		=> array(20),
		),
		'nickname'			=> array
		(
			'not_empty'			=> NULL,
			'min_length'		=> array(3),
			'max_length'		=> array(32),
		),
	);
	
	protected $_password_rules = array
	(
		'password'			=> array
		(
			'not_empty'			=> NULL,
			'min_length'		=> array(5),
			'max_length'		=> array(20),
		),
		'password_confirm'	=> array
		(
			'matches'		=> array('password'),
		),
	);
	
	/**
	 * Validates an array for a matching password and password_confirm field.
	 *
	 * @param  array    values to check
	 * @param  boolean   save the user if
	 * @return boolean
	 */
	public function change_password(array & $array, $save = FALSE)
	{
		$fields = $array;
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('password', $this->_password_rules['password'])
			->rules('password_confirm', $this->_password_rules['password_confirm']);

		if ($status = $array->check())
		{
			$this->where('email', '=', $fields['email'])->find();
			
			// Change the password
			$this->password = $array['password'];
			if ($save !== FALSE AND $status = $this->save())
			{
				if (is_string($save))
				{
					// Redirect to the success page
					Request::current()->redirect($save);
				}
			}
		}

		return $status;
	}

	/**
	 * Check user role by role name
	 *
	 * @param  $role_name
	 * @return bool
	 */
	public function has_role($role_name)
	{
		return $this->has('roles', ORM::factory('role', array('name' => $role_name)));
	}
	
	/**
	 * Get random uesrs
	 *
	 * @param int $number 
	 * @param int $cache 
	 * @return object
	 */
	public function random_users($number = 1, $cache = 60)
	{
		return $this->order_by(DB::expr('RAND()'))
			->limit($number)
			->cached($cache)
			->find_all();
	}
	
	/**
	 * Get new members
	 *
	 * @param int $max 
	 * @param int $offset 
	 * @param int $cache 
	 * @return object
	 */
	public function recruits($max = 10, $offset = 0, $cache = 60)
	{
		return $this->order_by('created', 'DESC')
			->offset($offset)
			->limit($max)
			->cached($cache)
			->find_all();
	}
	
	/**
	 * Search users
	 * @param string $query
	 * @return mixed
	 */
	public function search($query)
	{
		$query = '%'.Alpaca::force_string($query).'%';
		$this->where('nickname', 'LIKE', $query)
			->or_where('username', 'LIKE', $query)
			->order_by('created', 'DESC');
		
		return $this;
	}
	
}


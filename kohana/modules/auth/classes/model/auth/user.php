<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Auth_User extends ORM {

	// Relationships
	protected $_has_many = array
	(
		'user_tokens' => array('model' => 'user_token'),
		'roles' => array(
			'model' => 'role', 
			'through' => 'roles_users'
		)
	);
	// Rules
	protected $_rules = array
	(
		'username'			=> array
		(
			'not_empty'			=> NULL,
			'min_length'		=> array(4),
			'max_length'		=> array(32),
			'regex'			=> array('![a-zA-Z0-9_.]!u'),
		),
		'password'			=> array
		(
			'not_empty'			=> NULL,
			'min_length'		=> array(5),
			'max_length'		=> array(42),
		),
		'password_confirm'	=> array
		(
			'matches'		=> array('password'),
		),
		'email'				=> array
		(
			'not_empty'			=> NULL,
			'min_length'		=> array(4),
			'max_length'		=> array(127),
			'validate::email'	=> NULL,
		),
	);

	// Columns to ignore
	protected $_ignored_columns = array('password_confirm');

	/**
	 * Validates login information from an array, and optionally redirects
	 * after a successful login.
	 *
	 * @param  array    values to check
	 * @param  string   URI or URL to redirect to
	 * @return boolean
	 */
	public function login(array & $array, $remember = FALSE, $redirect = FALSE)
	{
		$user_type = Kohana::config('auth.login_mode');
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules($user_type, $this->_rules[$user_type])
			->rules('password', $this->_rules['password']);

		// Login starts out invalid
		$status = FALSE;

		if ($array->check())
		{
			$this->where($user_type, '=', $array[$user_type])->find();

			if ($this->loaded() AND Auth::instance()->login($this, $array['password'], $remember))
			{
				if (is_string($redirect))
				{
					// Redirect after a successful login
					Request::instance()->redirect($redirect);
				}

				// Login is successful
				$status = TRUE;
			}
			else
			{
				$array->error($user_type, 'invalid');
			}
		}

		return $status;
	}

	/**
	 * Validates an array for a matching password and password_confirm field.
	 *
	 * @param  array    values to check
	 * @param  string   save the user if
	 * @return boolean
	 */
	public function change_password(array & $array, $save = FALSE)
	{
		$fields = $array;
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('password', $this->_rules['password'])
			->rules('password_confirm', $this->_rules['password_confirm']);

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
					Request::instance()->redirect($save);
				}
			}
		}

		return $status;
	}

	/**
	 * Does the reverse of unique_key_exists() by triggering error if username exists
	 * Validation Rule
	 *
	 * @param    Validate  $array   validate object
	 * @param    string    $field   field name
	 * @param    array     $errors  current validation errors
	 * @return   array
	 */
	public function username_available(Validate $array, $field)
	{
		if ($this->unique_key_exists($field, $array[$field])) {
			$array->error($field, 'username_available', array($array[$field]));
		}
	}

	/**
	 * Does the reverse of unique_key_exists() by triggering error if email exists
	 * Validation Rule
	 *
	 * @param    Validate  $array   validate object
	 * @param    string    $field   field name
	 * @param    array     $errors  current validation errors
	 * @return   array
	 */
	public function email_available(Validate $array, $field)
	{
		// Added error when the email is found in database.
		if ($this->unique_key_exists($field, $array[$field])) {
			$array->error($field, 'email_available', array($array[$field]));
		}
	}

	/**
	 * Does the reverse of unique_key_exists() by triggering error if email does not exists
	 * Validation Rule
	 *
	 * @param    Validate  $array   validate object
	 * @param    string    $field   field name
	 * @param    array     $errors  current validation errors
	 * @return   array
	 */
	public function email_absent(Validate $array, $field)
	{
		// Added error when the email is not found in database.
		if ( ! $this->unique_key_exists($field, $array[$field]) ) {
			$array->error($field, 'unregistered', array($array[$field]));
		}
	}

	/**
	 * Tests if a unique key value exists in the database
	 *
	 * @param   string       column	 the column to test
	 * @param   string       value   the value to test
	 * @return  boolean
	 */
	public function unique_key_exists($column, $value)
	{
		return $this->where($column, '=', $value)->find()->loaded();
	}

	/**
	 * Saves the current object. Will hash password if it was changed
	 *
	 * @chainable
	 * @return  $this
	 */
	public function save()
	{
		// hash password
		if (array_key_exists('password', $this->_changed))
		{
			$this->_object['password'] = Auth::instance()->hash_password($this->_object['password']);
		}
		
		// add created time for new member
		if (empty($this->created))
		{
			$this->created = time();
		}
		
		return parent::save();
	}

} // End Auth User Model
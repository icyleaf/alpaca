<?php defined('SYSPATH') or die('No direct script access.');

class Model_Verity extends ORM {

	// Rules
	protected $_hash_code_rules = array
	(
		'hash_code'	=> array
		(
			'not_empty'		=> NULL,
		),
	);
	
	/**
	 * Check user if it is actived
	 *
	 * @param string $user 
	 * @param string $type 
	 * @return boolean
	 */
	public function actived($type, $email)
	{
		$result = $this->where('email', '=', $email)
			->and_where('type', '=', $type)
			->and_where('actived', '=', 1)
			->find();
		
		return $result->loaded();
	}
	
	/**
	 * General hash code
	 *
	 * @param string $user 
	 * @param string $type 
	 * @param int $offset 
	 * @param string $split 
	 * @param boolean $save 
	 * @return mixed
	 */
	public function general_code($email, $type = 'verity', $offset = 5, $split = '-', $save = TRUE)
	{
		$salt_one 	= rand(1000, 9999) + time();
		$salt_two 	= rand(1000, 9999) - time();
		$salt_three = rand(1000, 9999) * time();
		$salt_four 	= rand(1000, 9999) / time();
			
		$hash = substr(sha1($salt_one), rand(0, 10), $offset). 
			substr(md5($salt_two), rand(0, 10), $offset).
			substr(sha1($salt_three), rand(0, 10), $offset). 
			substr(md5($salt_four), rand(0, 10), $offset);
			
		$hash_length = strlen($hash);
		$hash_array = array();
		for ($i = 0; $i < $hash_length; $i = $i + $offset)
		{
			$hash_array[] = strtoupper(substr($hash, $i, $offset));
		}
		$hash_code = implode($split, $hash_array);
		
		if ($save)
		{
			$this->email = $email;
			$this->type = $type;
			$this->code = $hash_code;
			$this->save();
		}
		
		return $hash_code;
	}
	
	/**
	 * Validate email
	 *
	 * @param string $email 
	 * @param string $type 
	 * @return void
	 */
	public function verity_email($email, $type = 'verity')
	{
		$verity = $this->where('email', '=', $email)
			->and_where('type', '=', $type)
			->find();
			
		if ($verity->loaded() AND $verity->actived == 0)
		{
			return FALSE;
		}
		else
		{
			// the record will delete when user passed verity.
			return TRUE;
		}
	}
	
	/**
	 * Verity hash code
	 *
	 * @param string $code 
	 * @param string $type 
	 * @param boolean $delete 
	 * @return mixed
	 */
	public function verity_code($code, $type = 'verity', $delete = TRUE)
	{
		$verity = $this->where('code', '=', $code)
			->and_where('type', '=', $type)
			->find();

		if ($verity->loaded()  AND $verity->actived == 0)
		{
			if ($delete)
			{
				$verity->delete();
			}
			
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Validate hash code by form post
	 *
	 * @param array $array 
	 * @return boolean
	 */
	public function validate_hash_code(array & $array)
	{
		$array = Validation::factory($array)
			->filter(TRUE, 'trim')
			->rules('hash_code', $this->_hash_code_rules['hash_code']);

		if ($array->check())
		{
			if ($this->verity_code($array['hash_code']))
			{
				return TRUE;
			}
			else
			{
				$array->error('hash_code', 'invalid', array($array['hash_code']));
			}
		}
	
		return FALSE;
	}
	
	/**
	 * Save the column 'created' into the schema
	 *
	 * @return void
	 */
	public function save(Validation $validation = NULL)
	{
		if (empty($this->created))
		{
			$this->created = time();
		}
		
		parent::save($validation);
	}
	
}


<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca User helper
 *
 * @package Alpaca
 * @author icyleaf
 */
class Alpaca_User {
	
	/**
	 * General user uri (either number or string)
	 *
	 * @param Model_User $user 
	 * @return mixed
	 */
	public static function the_uri($user)
	{
		if (is_array($user))
		{
			return empty($user['username']) ? $user['id'] : $user['username'];
		}
		else
		{
			return empty($user->username) ? $user->id : $user->username;
		}
	}
	
	/**
	 * General user avatar
	 *
	 * @param Model_User $user 
	 * @param mixed $config 
	 * @param boolean $attr 
	 * @param boolean $link 
	 * @return string
	 */
	public static function avatar(Model_User $user, $config = NULL, $attr = FALSE, $link = FALSE)
	{
		$gravatar_config = array
		(
			'default'	=> URL::site('media/images/user-default.jpg'),
		);
		
		if (is_array($config))
		{
			if (isset($config['size']) AND $config['size'] != 30)
			{
				$gravatar_config['default'] = URL::site('media/images/user-default-'.$config['size'].'x'.$config['size'].'.jpg');
			}
			$gravatar_config = array_merge($gravatar_config, $config);
		}
		
		$image = Gravatar::instance($user->email, $gravatar_config);
		
		if (is_array($attr))
		{
			$image = HTML::image($image, $attr);
		}
		else if ($attr)
		{
			$image = HTML::image($image);
		}

		$user_uri = array(
			'id' => Alpaca_User::the_uri($user)
		);
		if (is_array($link))
		{
			$image = HTML::anchor(Route::get('user')
				->uri($user_uri), $image, $attr);
		}
		elseif ($link)
		{
			$image = HTML::anchor(Route::get('user')
				->uri($user_uri), $image);
		}
		
		return $image;
	}
	
	/**
	 * Get random users
	 *
	 * @param int $number 
	 * @param int $cache 
	 * @return string
	 */
	public static function get_random($number = 1, $cache = 60)
	{
		$users = ORM::factory('user')->random_users($number, $cache);
		$output = NULL;
		if ($users->count() > 0)
		{
			$output .= '<ul class="novice">';
			foreach ($users as $user)
			{
				$the_uri = Alpaca_User::the_uri($user);
				$avatar = Alpaca_User::avatar($user, array('size' => 16), array('class' => 'avatar'));
				$link = HTML::anchor(Route::get('user')->uri(array('id' => $the_uri)), $avatar.$user->nickname);
				$date = '<small>'.Alpaca::time_ago($user->created).'</small>';
				$output .= '<li>'.$link.$date.'</li>';
			}
			$output .= '</ul>';
		}
		
		return $output;
	}
	
	/**
	 * Get novice
	 *
	 * @param int $max 
	 * @param int $offset 
	 * @param int $cache 
	 * @return string
	 */
	public static function get_recruits($max = 10, $offset = 0, $cache = 60)
	{
		$users = ORM::factory('user')->recruits($max, $offset, $cache);
		$output = NULL;
		if ($users->count() > 0)
		{
			$output .= '<ul class="novice">';
			foreach ($users as $user)
			{
				$the_uri = Alpaca_User::the_uri($user);
				$avatar = Alpaca_User::avatar($user, array('size' => 16), array('class' => 'avatar'));
				$link = HTML::anchor(Route::get('user')->uri(array('id' => $the_uri)), $avatar.$user->nickname);
				$date = '<small>'.Alpaca::time_ago($user->created).'</small>';
				$output .= '<li>'.$link.$date.'</li>';
			}
			$output .= '</ul>';
		}
		
		return $output;
	}
	
}


<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca User Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_User extends Controller_Alpaca {
	
	/**
	 * Asign request to related action
	 *
	 * @param mixed $user_id 
	 * @param string $type 
	 * @return void
	 */
	public function action_index($user_id, $type = NULL)
	{
		if (is_numeric($user_id))
		{
			$user = ORM::factory('user', $user_id);
		}
		else
		{
			$user = ORM::factory('user')
				->where('username', '=', $user_id)
				->find();
		}

		if ($user->loaded())
		{
			$methods = array_diff
			(
				get_class_methods($this),
				get_class_methods(get_parent_class($this)),
				array('action_index', 'action_view')
			);
			
			$type = trim(strtolower($type));
			if (array_keys($methods, $type))
			{
				$this->$type($user);
			}
			else
			{
				$this->profile($user);
			}
		}
		else
		{
			$this->template->content = __('Not found the user, please spell check the username.');
		}
	}
	
	/**
	 * View user's profile
	 *
	 * @param Model_User $user_id 
	 * @return void
	 */
	protected function profile(Model_User $user)
	{
		$title = __('Subscribe the latest updates @:user...', array(
			':user' => $user->nickname
		));
		$this->header->title->set($user->nickname);
		$this->header->title->append($this->config->title);
		// Insert the user rss link
		$this->header->link->append(Alpaca_User::url('user/feed', $user), $title);
		
		$this->template->content = View::factory('user/profile')
			->bind('user', $user)
			->bind('user_profiles', $user_profiles)
			->bind('topics', $topics)
			->bind('replies', $replies)
			->bind('groups', $groups)
			->bind('collections_count', $collections_count)
			->bind('following_count', $following_count)
			->bind('follower_count', $follower_count);

		$topics = $user->topics->order_by('created', 'DESC')->find_all();
		$replies = ORM::factory('topic')->posted_topics_by_user($user->id);
		$groups = $user->groups->order_by('created', 'DESC')->find_all();
		$collections_count = ORM::factory('collection')->where('user_id', '=', $user->id)->find_all()->count();
		$following_count = 0;
		$follower_count = 0;

		$user_profiles = array();
		foreach ($user->as_array() as $key => $value)
		{
			// NEVER display the key below in public page
			$hidden = array(
				'id', 'password', 'username', 'email', 'gender',
				'hits', 'logins', 'last_login', 'last_ua'
			);

			if ( ! empty($value) AND  ! in_array($key, $hidden))
			{
				if ($key == 'created')
				{
					$key = 'Member Since';
					$value = date('Y-m-d', $value);
				}
				elseif ($key == 'website' AND Validate::url($value))
				{
					$value = Text::auto_link_urls($value);
				}

				$user_profiles[__(ucfirst($key))] = $value;
			}
		}
	}
	
	/**
	 * View user posted topics
	 *
	 * @param Model_User $user 
	 * @return void
	 */
	protected function topics(Model_User $user)
	{
		$title = __('Posted Topics by :user', array(':user' => $user->nickname));
		$topics = $user->topics->order_by('created', 'DESC')->find_all();
		$head = array(
			'title' => $title,
			'class' => 'hits',
		);
		
		$this->header->title->set($title);
		$this->header->title->append($this->config->title);
		
		$this->template->content = View::factory('topic/list')
			->set('head', $head)
			->set('topics', $topics);
		$this->template->sidebar = '';
	}
	
	/**
	 * View user commented topics
	 *
	 * @param Model_User $user 
	 * @return void
	 */
	protected function posts(Model_User $user)
	{
		$title = __('Replies Topics by :user', array(':user' => $user->nickname));
		$topics = ORM::factory('topic')->posted_topics_by_user($user->id);
		$head = array(
			'title' => $title,
			'class' => 'hits',
		);

		$this->header->title->set($title);
		$this->header->title->append($this->config->title);
		
		$this->template->content = View::factory('topic/list')
			->set('head', $head)
			->set('topics', $topics);
		$this->template->sidebar = '';
	}
	
	/**
	 * View user created groups
	 *
	 * @param Model_User $user 
	 * @return void
	 */
	protected function groups(Model_User $user)
	{
		$this->header->title->set(__(':user\'s Groups', array(':user' => $user->nickname)));
		$this->header->title->append($this->config->title);
		
		$this->template->content = View::factory('group/list')
			->set('groups', $user->groups->order_by('created', 'DESC')
			->find_all());
			
		$this->template->sidebar = '';
	}
	
	/**
	 * View user collection's topics
	 *
	 * @param Model_User $user 
	 * @return void
	 */
	protected function collections(Model_User $user)
	{
		$title = __('Replies Topics by :user', array(':user' => $user->nickname));
		$topics = ORM::factory('topic')->collectioned_topics_by_user($user->id);

		$head = array(
			'title' => $title,
			'class' => 'hits',
		);

		$this->header->title->set($title);
		$this->header->title->append($this->config->title);
		
		$this->template->content = View::factory('topic/list')
			->set('head', $head)
			->set('topics', $topics);
			
		$this->template->sidebar = '';
	}
	
	/**
	 * View user is following people
	 *
	 * @param Model_User $user 
	 * @return void
	 */
	protected function followings(Model_User $user)
	{
		$this->template->content = 'Nothing';
	}
	
	/**
	 * View user followed people
	 *
	 * @param Model_User $user 
	 * @return void
	 */
	protected function followers(Model_User $user)
	{
		$this->template->content = 'Nothing';
	}

}


<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca User Entry
 *
 * @package controller
 * @author icyleaf
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
			$this->template->content = '未找到该用户，请合适用户名或 id 是否正确拼写。';
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
		
		$link = Route::url('user/feed', array('id' => Alpaca_User::the_uri($user)));
		$this->header->link->append($link, $title);
				
		$this->template->content = View::factory('user/profile')
			->bind('user', $user);
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
		$topics = (object) $user->posted_topics($user->id);
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
		$this->header->title->set(__(':user的小组', array(':user' => $user->nickname)));
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
		$collections = ORM::factory('collection')
			->where('user_id', '=', $user->id)
			->find_all();

		$topics = array();
		if ($collections->count() > 0)
		{
			foreach ($collections as $collection)
			{
				$topic = $collection->topic;
				if ($topic->loaded())
				{
					$topics[] = $topic;
				}
				else
				{
					/* TODO: Collected the deleted topic
					 * 1. Store the topic title in collection table to render collection items
					 * 2. Render 'deleted tips' on the missing topic
					 */
				}
			}
		}

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


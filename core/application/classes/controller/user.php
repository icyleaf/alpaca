<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca User Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_User extends Controller_Template_Alpaca {

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
		$user_feed_link = Alpaca_User::url('user', $user, 'feed');

		$user_array = $user->as_array();
		$user_array['avatar'] = Alpaca_User::avatar($user, NULL, array('class' => 'avatar'));
		$user_array['feed_link'] = $user_feed_link;
		$user_array['profiles'] = array();
		foreach ($user->as_array() as $key => $value)
		{
			// NEVER display the key below in public page
			$hidden = array(
				'id', 'password', 'username', 'email', 'gender',
				'hits', 'logins', 'last_login', 'last_ua'
			);

			if ( ! empty($value) AND ! in_array($key, $hidden))
			{
				if ($key == 'created')
				{
					$key = 'Member Since';
					$value = date('Y-m-d', $value);
				}
				elseif ($key == 'website' AND Valid::url($value))
				{
					$value = Text::auto_link_urls($value);
				}

				if ($key == 'qq')
				{
					$key = strtoupper($key);
				}
				else
				{
					$key = ucfirst($key);
				}

				$user_array['profiles'][__($key)] = $value;
			}
		}

		$topic = ORM::factory('topic');
		$user_array['stats'] = array(
			array(
				'title' => __('Topics'),
				'total' => $user->topics->user_topic_total(),
				'link'  => Alpaca_User::url('user', $user, 'topics'),
			),
			array(
				'title' => __('Replies'),
				'total' => $topic->user_reply_topic_total($user->id),
				'link'  => Alpaca_User::url('user', $user, 'posts'),
			),
			array(
				'title' => __('Collections'),
				'total' => $topic->user_collected_topic_total($user->id),
				'link'  => Alpaca_User::url('user', $user, 'collections'),
			),
		);

		$title = __('Subscribe the latest updates @:user...', array(
			':user' => $user->nickname
		));
		$this->head->title->set($user->nickname);
		$this->head->title->append($this->config->title);
		// Insert the user rss link
		$this->head->link->append($user_feed_link, $title);

		$this->template->content = Twig::factory('user/profile')
			->set('user', $user_array);
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

		$this->head->title->set($title);
		$this->head->title->append($this->config->title);

		if ($topics->count() > 0)
		{
			foreach ($topics as $i => $topic)
			{
				$author = $topic->author;
				$author_array = array(
					'id'		=> $author->id,
					'avatar'	=> Alpaca_User::avatar($author, array('size' => 30), array('class' => 'avatar'), TRUE),
					'nickname'	=> $author->nickname,
					'link'		=> Alpaca_User::url('user', $author)
				);
				$author_array = (object) $author_array;

				$group = $topic->group;
				$group_array = array(
					'id'		=> $group->id,
					'name'		=> $group->name,
					'link'		=> Route::url('group', array('id' => Alpaca_Group::uri($group))),
				);
				$group_array = (object) $group_array;

				$collected = ORM::factory('collection')->is_collected($topic->id, $author->id);
				$topics_array[$i] = array(
					'id'			=> $topic->id,
					'title'			=> $topic->title,
					'link'			=> Alpaca_Topic::url($topic, $group),
					'author'		=> $author_array,
					'group'			=> $group_array,
					'collections'	=> $topic->collections,
					'comments'		=> $topic->count,
					'hits'			=> $topic->hits,
					'collected'		=> $collected,
					'content'		=> Alpaca::format_html($topic->content),
					'created'		=> date($this->config->date_format, $topic->created),
					'time_ago'		=> Alpaca::time_ago($topic->created),
					'updated'		=> Alpaca::time_ago($topic->updated),
				);

				$topics_array[$i] = (object) $topics_array[$i];
			}
		}

		$this->template->content = Twig::factory('topic/list')
			->set('head', $head)
			->bind('topics', $topics_array);
		// TODO: Show message if topics is empty
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

		$this->head->title->set($title);
		$this->head->title->append($this->config->title);

		if ($topics->count() > 0)
		{
			foreach ($topics as $i => $topic)
			{
				$author = $topic->author;
				$author_array = array(
					'id'		=> $author->id,
					'avatar'	=> Alpaca_User::avatar($author, array('size' => 30), array('class' => 'avatar'), TRUE),
					'nickname'	=> $author->nickname,
					'link'		=> Alpaca_User::url('user', $author)
				);
				$author_array = (object) $author_array;

				$group = $topic->group;
				$group_array = array(
					'id'		=> $group->id,
					'name'		=> $group->name,
					'link'		=> Route::url('group', array('id' => Alpaca_Group::uri($group))),
				);
				$group_array = (object) $group_array;

				$collected = ORM::factory('collection')->is_collected($topic->id, $author->id);
				$topics_array[$i] = array(
					'id'			=> $topic->id,
					'title'			=> $topic->title,
					'link'			=> Alpaca_Topic::url($topic, $group),
					'author'		=> $author_array,
					'group'			=> $group_array,
					'collections'	=> $topic->collections,
					'comments'		=> $topic->count,
					'hits'			=> $topic->hits,
					'collected'		=> $collected,
					'content'		=> Alpaca::format_html($topic->content),
					'created'		=> date($this->config->date_format, $topic->created),
					'time_ago'		=> Alpaca::time_ago($topic->created),
					'updated'		=> Alpaca::time_ago($topic->updated),
				);

				$topics_array[$i] = (object) $topics_array[$i];
			}
		}

		$this->template->content = Twig::factory('topic/list')
			->set('head', $head)
			->bind('topics', $topics_array);
		// TODO: Show message if topics is empty
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

		$this->head->title->set($title);
		$this->head->title->append($this->config->title);

		if ($topics->count() > 0)
		{
			foreach ($topics as $i => $topic)
			{
				$author = $topic->author;
				$author_array = array(
					'id'		=> $author->id,
					'avatar'	=> Alpaca_User::avatar($author, array('size' => 30), array('class' => 'avatar'), TRUE),
					'nickname'	=> $author->nickname,
					'link'		=> Alpaca_User::url('user', $author)
				);
				$author_array = (object) $author_array;

				$group = $topic->group;
				$group_array = array(
					'id'		=> $group->id,
					'name'		=> $group->name,
					'link'		=> Route::url('group', array('id' => Alpaca_Group::uri($group))),
				);
				$group_array = (object) $group_array;

				$collected = ORM::factory('collection')->is_collected($topic->id, $author->id);
				$topics_array[$i] = array(
					'id'			=> $topic->id,
					'title'			=> $topic->title,
					'link'			=> Alpaca_Topic::url($topic, $group),
					'author'		=> $author_array,
					'group'			=> $group_array,
					'collections'	=> $topic->collections,
					'comments'		=> $topic->count,
					'hits'			=> $topic->hits,
					'collected'		=> $collected,
					'content'		=> Alpaca::format_html($topic->content),
					'created'		=> date($this->config->date_format, $topic->created),
					'time_ago'		=> Alpaca::time_ago($topic->created),
					'updated'		=> Alpaca::time_ago($topic->updated),
				);

				$topics_array[$i] = (object) $topics_array[$i];
			}
		}

		$this->template->content = Twig::factory('topic/list')
			->set('head', $head)
			->bind('topics', $topics_array);
		// TODO: Show message if topics is empty

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
		$this->head->title->set(__(':user\'s Groups', array(':user' => $user->nickname)));
		$this->head->title->append($this->config->title);

		$this->template->content = Twig::factory('group/list')
			->set('groups', $user->groups->order_by('created', 'DESC')
			->find_all());

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

	/**
	 * View user feed
	 *
	 * @param Model_User $user
	 * @return void
	 * @use Controller_Feed
	 */
    protected function feed(Model_User $user)
    {
    	$this->auto_render = FALSE;
        $controller_feed = new Controller_Feed($this->request);
        $controller_feed->before();
        $controller_feed->action_user($user->id);
    }

}


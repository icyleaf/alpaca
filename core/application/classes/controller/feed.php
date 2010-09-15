<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Feed Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Feed extends Controller_Template_Alpaca {
	
	public function before()
	{
		parent::before();
		
		$this->auto_render = FALSE;
	}
	
	/**
	 * Get latest topic discuz
	 */
	public function action_index()
	{
		$feed = array();
		$feed_config = $this->config->feed;
		$topics = ORM::factory('topic')->get_topics('', $feed_config['per_page'], 0, $feed_config['cache']);
		if ($topics->count() > 0)
		{
			foreach ($topics as $topic)
			{
				$feed[] = array(
					'title'		=> htmlspecialchars($topic->title),
					'link'			=> Alpaca_Topic::url($topic),
					'description'	=> Alpaca::format_html($topic->content, TRUE),
					'author'		=> $topic->author->nickname,
					'pubDate'		=> $topic->created,
				);
			}
		}

		$title = __('Latest topics');
		$this->_render($title, $feed);
	}
	
	/**
	 * Get user activity recently
	 *
	 * @param mixed $user_id 
	 * @return void
	 */
	public function action_user($user_id)
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
			$feed = array();
			$feed_config = $this->config->feed;
			$topics = $user->topics->get_topics_by_user($feed_config['per_page'], $feed_config['cache']);

			if ($topics->count() > 0)
			{
				foreach ($topics as $topic)
				{
					$feed[] = array(
						'title'		=> htmlspecialchars($topic->title),
						'link'			=> Alpaca_Topic::url($topic),
						'description'	=> Alpaca::format_html($topic->content, TRUE),
						'author'		=> $topic->author->nickname,
						'pubDate'		=> $topic->created,
					);
				}
			}
			
			if (I18n::$lang == 'zh-cn')
			{
				$user_link = Alpaca::beautify_str($user->nickname, TRUE, TRUE);
			}
			else
			{
				$user_link = $user->nickname;
			}

			$title = __('Latest updates @:user', array(':user' => $user_link));
			$this->_render($title, $feed, Alpaca_User::url('user', $user));
		}
	}
	
	/**
	 * General feed xml format and render
	 *
	 * @param string $title 
	 * @param array $feed 
	 * @param string $link 
	 * @return void
	 */
	private function _render($title, $feed = array(), $link = NULL)
	{
		$link = empty($link) ? URL::base(FALSE) : $link;
		$rss = Feed::create(array(
				'title' => $title.' - '.$this->config->title,
				'link'  => $link,
			),
			$feed
		);
		
		// Set the header and render
		$this->request->headers = array
		(
			'Content-Type'	=> 'text/xml',	
		);
		$this->request->response = $rss;
	}
}


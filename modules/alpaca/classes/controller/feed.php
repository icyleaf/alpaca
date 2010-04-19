<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Feed Entry
 *
 * @package controller
 * @author icyleaf
 */
class Controller_Feed extends Controller {
	
	private $_config = null;
	
	public function before()
	{
		$this->_config = Kohana::config('alpaca');
	}
	
	/**
	 * Get latest topic discuz
	 */
	public function action_index()
	{
		$feed = array();
		$topics = ORM::factory('topic')->get_topics('', $this->_config->feed['per_page'], 0, $this->_config->feed['cache']);
		if ($topics->count() > 0)
		{
			foreach ($topics as $topic)
			{
				$feed[] = array(
					'title'       => htmlspecialchars($topic->title),
					'link'        => URL::site(Route::get('topic')->uri(array('id' => $topic->id))),
					'description' => Alpaca::format_html($topic->content, TRUE),
					'author'      => $topic->author->nickname,
					'pubDate'     => $topic->created,
				);
			}
		}
		
		$this->_render(__('Latest topics'), $feed);
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
			$user = ORM::factory('user')->where('username', '=', $user_id)->find();
		}
		
		if ($user->loaded())
		{
			$feed = array();
			$topics = $user->topics
				->limit($this->_config->feed['per_page'])
				->order_by('created', 'DESC')
				->cached($this->_config->feed['cache'])
				->find_all();
				
			if ($topics->count() > 0)
			{
				foreach ($topics as $topic)
				{
					$feed[] = array(
						'title'       => htmlspecialchars($topic->title),
						'link'        => URL::site(Route::get('topic')->uri(array('id' => $topic->id))),
						'description' => Alpaca::format_html($topic->content, TRUE),
						'author'      => $topic->author->nickname,
						'pubDate'     => $topic->created,
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
			
			$link = URL::site(Route::get('user')->uri(array('id' => Alpaca_user::the_uri($user))));
			$this->_render(__('Latest updates @:user', array(':user' => $user_link)), $feed, $link);
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
				'title' => $title.' - '.$this->_config->title,
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


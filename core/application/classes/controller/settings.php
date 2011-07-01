<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Settings Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Settings extends Controller_Template_Alpaca {
	
	private $user		= NULL;
	private $links		= NULL;
	private $status		= FALSE;
	
	/**
	 * Settings Init
	 */
	public function before()
	{
		parent::before();
		
		$this->status = FALSE;
		$this->user = $this->auth->get_user();

		// Check login status else redirect to login page
		Alpaca::logged_in();

		$user_name = $this->user->nickname;
		if (I18n::$lang == 'zh-cn')
		{
			$user_name = Alpaca::beautify_str($this->user->nickname, FALSE, TRUE);
		}

		$this->links = array(
			'settings'  => array(
				'link'      => 'settings',
				'title'     => __('About yourself'),
			),
			'changepassword'  => array(
				'link'      => 'settings/changepassword',
				'title'     => __('Change Password'),
			),
//			'notifications'  => array(
//				'link'      => 'settings/notifications',
//				'title'     => __('Notifications'),
//			),
//			'destroy'       => array(
//				'link'      => 'settings/destroy',
//				'title'     => __('Destroy Account'),
//			),
		);

		foreach ($this->links as $i => $item)
		{
			if ($item['link'] == $this->request->uri())
			{
				$this->links[$i]['attr'] = array(
					'class' => 'current'
				);
				break;
			}
		}

		$title = __('My profile', array(':user' => $user_name));
		$this->head->title->prepend($title);
		
		$this->template->content = Twig::factory('template/settings')
			->bind('title', $title)
			->bind('setting_nav', $this->links)
			->bind('status', $this->status);

		$user = $this->auth->get_user();
		$user_profile_link = Alpaca_User::url('user', $user);
		$user = $user->as_array();
		$user['link'] = $user_profile_link;

		$this->template->sidebar = Twig::factory('sidebar/settings')
			->set('user', $user);
	}
	
	/**
	 * View/Edit user profile
	 */
	public function action_index()
	{
		if ($_POST)
		{
			foreach ($_POST as $key => $value)
			{
				$_POST[$key] = Security::xss_clean($value);
			}

			$post = Validate::factory($_POST)
				->filter(TRUE, 'trim')
				->rules('nickname', array(
					'not_empty'			=> NULL,
					'min_length'		=> array(3),
					'max_length'		=> array(32),
					))
				->rules('username', array(
					'min_length'		=> array(0),
					'max_length'		=> array(15),
					'alpha_numeric'     => NULL,
					))
				->rules('location', array(
					'min_length'		=> array(0),
					'max_length'		=> array(15),
					))
				->rules('website', array(
					'min_length'		=> array(0),
					'max_length'		=> array(100),
					'url'               => NULL,
					))
				->rules('qq', array(
					'min_length'		=> array(0),
					'max_length'		=> array(50),
					))
				->rules('msn', array(
					'min_length'		=> array(0),
					'max_length'		=> array(50),
					'email'	            => NULL,
					))
				->rules('gtalk', array(
					'min_length'		=> array(0),
					'max_length'		=> array(50),
					'email'	            => NULL,
					))
				->rules('skype', array(
					'min_length'		=> array(0),
					'max_length'		=> array(50),
					));
			
			$status = 'error';
			if ($post->check())
			{
				$user_id = $_POST['id'];
				$user = ORM::factory('user', $user_id);
				// registered user cannt change email and username
				unset($_POST['id'], $_POST['email']);
				if ( ! empty($this->user->username) AND ! empty($_POST['username']))
				{
					// Save username ONLY once
					unset($_POST['username']);
				}

				$user->values($_POST)->save();

				// update saved user information
				$this->user = $user;

				$status = 'success';
			}
			else
			{
				$errors = $post->errors('validate');
			}

			$this->status = array
			(
				'type'		=> $status,
				'count'		=> isset($errors) ? count($errors) : NULL,
			);
		}

		$user_avatar = HTML::image(Alpaca_User::avatar($this->user));
		$user = $this->user->as_array();
		$user['avatar'] = $user_avatar;

		$this->template->content->body = Twig::factory('settings/profile')
			->bind('user', $user)
			->bind('errors', $errors);
	}
	
	/**
	 * Changed user password
	 */
	public function action_changepassword()
	{
		if ($_POST)
		{
			$rules = array
			(
				'not_empty'	=> NULL,
				'min_length'	=> array(5),
				'max_length'	=> array(20),
			);

			$post = Validate::factory($_POST)
				->filter(TRUE, 'trim')
				->rules('current_password', $rules)
				->rules('password', $rules)
				->rules('password_confirm', $rules);

			$status = 'error';
			if ($post->check())
			{
				$current_data = array
				(
					'username'	=> $this->user->email,
					'password'	=> $_POST['current_password'],
				);
				$_POST['username'] = $this->user->email;

				$user = ORM::factory('user');
				if ($user->login($current_data))
				{
					if ($user->change_password($_POST, TRUE))
					{
						$status = 'success';
					}
					else
					{
						$errors = $_POST->errors('validate');
					}
				}
				else
				{
					$errors = $current_data->errors('validate');
					
					if (isset($errors['username']))
					{
						$errors['current_password'] = $errors['username'];
					}
				}
			}
			else
			{
				$errors = $post->errors('validate');
			}

			$this->status = array
			(
				'type'	=> 'error',
				'count'	=> isset($errors) ? count($errors) : NULL,
			);
		}

		$this->template->content->body = Twig::factory('settings/changepassword')
			->bind('user', $this->user)
			->bind('errors', $errors);
	}
	
	/**
	 * Mail notification
	 */
	public function action_notifications()
	{
		$this->template->content->body = View::factory('settings/notifications')
			->bind('user', $this->user);
	}
	
	/**
	 * Destroy user account
	 */
	public function action_destroy()
	{
		$this->template->content->body = View::factory('settings/destroy');
	}
	
}


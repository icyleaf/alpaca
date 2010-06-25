<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Settings Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Settings extends Controller_Alpaca {
	
	private $user		= NULL;
	private $links		= NULL;
	private $status	= FALSE;
	
	/**
	 * Settings Init
	 */
	public function before()
	{
		parent::before();
		
		$this->status = FALSE;
		$this->user = $this->auth->get_user();
		if ( ! $this->user)
		{
			$current_uri = URL::query(array('redir' => $this->request->uri));
			$this->request->redirect(Route::url('login').$current_uri);
		}
		
		if (I18n::$lang == 'zh-cn')
		{
			$user_name = Alpaca::beautify_str($this->user->nickname, FALSE, TRUE);
		}
		else
		{
			$user_name = $this->user->nickname;
		}
		$title = __('My profile', array(':user' => $user_name));
		$this->header->title->prepend($title);
		$this->template->content = View::factory('settings/general')
			->bind('title', $title)
			->bind('links', $this->links)
			->bind('status', $this->status);
			
		$this->template->sidebar = View::factory('sidebar/settings');

		$this->links = array
		(
			'settings'						=> __('About yourself'),
			'settings/changepassword'	=> __('Change Password'),
			'settings/notifications'		=> __('Notifications'),
			//'settings/destroy'			=> __('Destroy Account'),
		);
	}
	
	/**
	 * View/Edit user profile
	 */
	public function action_index()
	{
		$this->template->content->body = View::factory('settings/profile')
			->bind('user', $this->user)
			->bind('errors', $errors);
		
		$this->template->sidebar->view = View::factory('sidebar/settings/profile')
			->bind('user', $this->user);
		
		if ($_POST)
		{
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
					))
				->rules('location', array(
					'min_length'		=> array(0),
					'max_length'		=> array(15),
					))
				->rules('website', array(
					'min_length'		=> array(0),
					'max_length'		=> array(100),
					))
				->rules('qq', array(
					'min_length'		=> array(0),
					'max_length'		=> array(50),
					))
				->rules('msn', array(
					'min_length'		=> array(0),
					'max_length'		=> array(50),
					'validate::email'	=> NULL,
					))
				->rules('gtalk', array(
					'min_length'		=> array(0),
					'max_length'		=> array(50),
					'validate::email'	=> NULL,
					))
				->rules('skype', array(
					'min_length'		=> array(0),
					'max_length'		=> array(50),
					));
				
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
				$this->status = array
				(
					'type'		=> 'success',
					'content'	=> __('Successful! Your profile has been updated!'),
				);
			}
			else
			{
				$errors = $post->errors('validate');
				$this->status = array
				(
					'type'		=> 'error',
					'content'	=> __(':count errors prohibited this user from being saved.', array(
						':count' => count($errors)
						)),
				);
			}
		}
	}
	
	/**
	 * Changed user password
	 */
	public function action_changepassword()
	{		
		$this->template->content->body = View::factory('settings/changepassword')
			->bind('user', $this->user)
			->bind('errors', $errors);
			
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
			
			if ($post->check())
			{
				$current_data = array
				(
					'email'	=> $this->user->email,
					'password'	=> $_POST['current_password'],
				);
				$_POST['email'] = $this->user->email;
				
				$user = ORM::factory('user');
				if ($user->login($current_data))
				{
					if ($user->change_password($_POST, TRUE))
					{
						$this->status = array
						(
							'type'		=> 'success',
							'content'	=> __('Successful! Your password has been update!'),
						);
					}
					else
					{
						$errors = $_POST->errors('validate');
						$this->status = array
						(
							'type'		=> 'error',
							'content'	=> __(':count errors prohibited this user from being saved.', array(
								':count' => count($errors)
								)),
						);
					}
					
				}
				else
				{
					$this->status = array
					(
						'type'		=> 'error',
						'content'	=> __(':count errors prohibited this user from being saved.', array(
							':count' => count($errors)
							)),
					);
					
					$errors = $current_data->errors('validate');
				}
			}
			else
			{
				$errors = $post->errors('validate');
				$this->status = array
				(
					'type'		=> 'error',
					'content'	=> __(':count errors prohibited this user from being saved.', array(
						':count' => count($errors)
						)),
				);
			}
		}
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


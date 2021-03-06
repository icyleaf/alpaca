<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Auth Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Auth extends Controller_Template_Alpaca {
	
	// Validate rules
	protected $_rules = array(
		'email'				=> array(
		'not_empty'			=> NULL,
		'min_length'		=> array(4),
		'max_length'		=> array(127),
		'validate::email'	=> NULL,
		)
	);
	
	private $_website		= NULL;
	
	public function before()
	{
		parent::before();
		if (I18n::$lang == 'zh-cn')
		{
			$this->_website = Alpaca::beautify_str($this->config->title, TRUE, TRUE);
		}
		else
		{
			$this->_website = $this->config->title;
		}
	}
	
	/**
	 * User Register
	 */
	public function action_register()
	{
		$email = Arr::get($_POST, 'email');
		$nickname = Arr::get($_POST, 'nickname');
		$random = time();

		$title = __('Register');
		$this->template->content = Twig::factory('auth/register')
			->bind('title', $title)
			->bind('email', $email)
			->bind('nickname', $nickname)
			->bind('random', $random)
			->bind('errors', $errors);
		
		if ($_POST)
		{
			$random = Arr::get($_POST, 'random');
			$nospam = Arr::get($_POST, 'nospam');

			$user = ORM::factory('user')->values($_POST);
			$user->validate()->callback('email', array(new Model_User, 'email_available'));
			if ($user->check())
			{
				// Human
				if ($random == $nospam)
				{
					//Save this user to schema
					$user->save();

					// append the first user as administrator role
					if ($user->id == 1000001)
					{
						$user->add('roles', ORM::factory('role', array('name' => 'admin')));
					}
					$user->add('roles', ORM::factory('role', array('name' => 'member')));

					// check record if it exists resend active email or create a new one and send.
					$verity = ORM::factory('verity', array('email' => $user->email));
					if ($verity->loaded())
					{
						$hash_code = $verity->code;
					}
					else
					{
						$hash_code = $verity->general_code($user->email);
					}

					$email_subject = __('Account Verity');
					$verity_url = Route::url('auth/actions', array(
						'action' => 'verity',
 						'code'   => $hash_code,
					));
					$email_content = '感谢您在 Kohana 中文注册的账户，于此同时，麻烦您一点时间，我们需要验证注册账户的真实性，'.
						'需要您点击下面的链接完成验证过程：<br /><br />'.
						HTML::anchor($verity_url, $verity_url).'<br /><br />'.
						'温馨提示：如果您忘记了密码，使用网站提供的重设密码功能也会把重设密码的链接发给本邮箱哦 :)';

					//TODO: validate email configurations before sending
					if (Alpaca::email($user->email, $email_subject, $email_content))
					{
						// Display success information
						$this->template->content = Alpaca::error_page($title, $content);

						$verity_url = Route::url('auth/actions', array('action' => 'verity'));
						$verity_url .= URL::query(array('email' => $user->email));
						$title = __('Welcome to join :website!', array(':website' => $this->_website));
						$content = __('Successful! We will send a mail to verity account in a moment.', array(
								':email' => $user->email
							)).
							'<br /><br />'.
							HTML::anchor($verity_url, __('Complete Verity'), array('class'=>'button')).
							__(' or ').
							HTML::anchor(URL::site('login'), __('Done! Continue Login'), array('class'=>'button')).
							'<br /><br />'.
							__('Thanks for support to %s.', array('%s' => $this->_website));
					}
					else
					{
						$this->template->content = Alpaca::error_page($title, $content);

						$content = __('Send failed! You may :try_again. '.
							'If it also failed, contact the website administrator.', array(
								':try_again' => '<a href="javascript:history.go(-1)">'.__('try again').'</a>')
							).'<br /><br />'.
							__('Thanks for support to %s.', array('%s' => $this->_website));
					}
				}
				else
				{
					// Maybe a robot (spam)
					$this->template->content = Alpaca::error_page($title, $content);
					$content = __('Are you a robot (spam) ?');

					// Write log
					$log_file = DOCROOT.'spam.log';
					if ( ! file_exists($log_file))
					{
						// Create the log file
						file_put_contents($log_file, Kohana::FILE_SECURITY.' ?>'.PHP_EOL);

						// Allow anyone to write to log files
						chmod($log_file, 0666);
					}

					$email = Arr::get($_POST, 'email');
					$ip = Arr::get($_SERVER, 'REMOTE_ADDR');
					$message = PHP_EOL.'============='.PHP_EOL.
						'Date: '.date('Y-m-d H:i', time()).PHP_EOL.
						'Email: '.$email.PHP_EOL.
						'IP: '.$ip.PHP_EOL;

					file_put_contents($log_file, $message, FILE_APPEND);
				}
			}
			else
			{
				$errors = $user->validate()->errors('validate');
			}
		}
		
		$this->head->title->set($title);
		$this->head->javascript->append_script('alpaca.anti_spam("random", "nospam");');
	}
	
	/**
	 * User login
	 */
	public function action_login()
	{
		$title = __('Log in').Alpaca::beautify_str($this->config->title, TRUE);
		$this->head->title->set($title);

		$username = Arr::get($_POST, 'username');
		$this->template->content = Twig::factory('auth/login')
			->set('username', $username)
			->bind('redir', $redirect)
			->bind('title', $title)
			->bind('errors', $errors);

		$redirect = Arr::get($_SERVER, 'HTTP_REFERER', URL::base());
		if ($data = $_POST)
		{
			$user = ORM::factory('user');
			if ($user->login($data))
			{
				if (ORM::factory('verity')->verity_email($user->email))
				{
					$disable_redirect = array
					(
						'auth', 'register', 'login', 'logout', 'invate',
						'lostpassword', 'changepassword', 'verity'
					);

					$redirect = Arr::get($_POST, 'redir', $redirect);
					$redirect = Arr::get($_GET, 'redir', $redirect);
					foreach ($disable_redirect as $key)
					{
						if (strpos($redirect, $key) !== FALSE)
						{
							$redirect = URL::base(FALSE);
							break;
						}
					}

					$this->request->redirect(URL::site($redirect));
				}
				else
				{
					$validate = Validate::factory($_POST)
						->filter(TRUE, 'trim')
						->rules('username', array('not_empty' => NULL))
						->error('username', 'not_actived');

					$errors = $validate->errors('validate');
				}
			}
			else
			{
				$errors = $data->errors('validate');
			}
		}
	}

	/**
	 * User logout
	 */
	public function action_logout()
	{
		$this->auth->logout();
		
		$title = __('Welcome back!');
		$this->head->title->set($title);
		$this->template->content = Twig::factory('auth/logout')
			->bind('title', $title);
	}
	
	/**
	 * Find user password
	 */
	public function action_lostpassword()
	{
		$this->template->content = Twig::factory('auth/lostpassword')
			->bind('title', $title)
			->bind('email', $email)
			->bind('errors', $errors);
			
		$title = __('Reset Password');
		$this->head->title->set($title);
		if ($_POST)
		{
			$email = Arr::get($_POST, 'email');

			$user = ORM::factory('user');
			$post = Validate::factory($_POST)
				->filter(TRUE, 'trim')
				->rules('email',  $this->_rules['email'])
				->callback('email', array(new Model_User, 'email_not_available'));
			
			if ($post->check())
			{
				$verity = ORM::factory('verity', array('email' => $email));
				if ($verity->loaded())
				{
					$hash_code = $verity->code;
				}
				else
				{
					$hash_code = $verity->general_code($email, 'lostpassword', 4, '');
				}
				$verity_url = Route::url('auth/actions', array(
					'action' => 'changepassword',
 					'code'   => $hash_code,
				));
				$email_content = __('Your request has been passed, Click the following link to reset your password:').
					'<br /><br />'.
					HTML::anchor($verity_url, $verity_url).'<br /><br />'.
					__('Gentle Reminder: For the purpose of your account safety, the link ONLY use once and Validity of 7 days!');

				if (Alpaca::email($email, $title, $email_content))
				{
					$this->template->content = Alpaca::error_page($title, $content);
						
					$content = __('Done! We sended a mail to your :email address to reset password.', array(
							':email' => $email
						)).'<br /><br />'.
						__('Thanks for support to %s.', array('%s' => $this->_website));
				}
				else
				{
					$this->template->content = Alpaca::error_page($title, $content);
					
					$content = __('Send failed! You may :try_again. '.
						'If it also failed, contact the website administrator.', array(
							':try_again' => '<a href="javascript:history.go(-1)">'.__('try again').'</a>')
						).'<br /><br />'.
						__('Thanks for support to %s.', array('%s' => $this->_website));
				}
			}
			else
			{
				$errors = $post->errors('validate');
			}
		}
	}
	
	/**
	 * Changed user password
	 *
	 * @param string $hash_code 
	 * @return void
	 */
	public function action_changepassword($hash_code)
	{
		$title = __('Reset Password');			
		$verity = ORM::factory('verity');
			
		if ($verity->verity_code($hash_code, 'lostpassword', FALSE))
		{
			$user = ORM::factory('user', array('email' => $verity->email));
			$this->template->content = View::factory('auth/changepassword')
				->bind('title', $title)
				->set('user', $user)
				->bind('errors', $errors);
			
			if ($_POST AND $user->loaded())
			{
				$post = $_POST;
				if ($user->change_password($post, TRUE))
				{
					$verity->delete();
					
					// Display successful information
					$this->template->content = Alpaca::error_page($title, $content);
						
					$content = __('Your password has been updated!').' '.
						__('Thanks for support to %s.', array('%s' => $this->_website)).
						'<br /><br />'.
						HTML::anchor(URL::site('login'), __('Continue Login'), array('class' => 'button'));
				}
				else
				{
					$errors = $post->errors('validate');
				}
			}
		}
		else
		{
			$this->template->content = Alpaca::error_page($title, $content);
			$content = __('Invalid validation code! Please confirm it correctly or had been actived.').
				'<br /><br />'.
				HTML::anchor(Route::url('auth/actions', array(
					'action' => 'changepassword',
					'code' => $hash_code
				)), __('Try again'), array('class' => 'button')).
				__(' or ').
				HTML::anchor(URL::site('login'), __('Try to login'), array('class' => 'button'));
		}

		$this->head->title->set($title);
	}
	
	/**
	 * Verity user email
	 *
	 * @param string $code 
	 * @return void
	 */
	public function action_verity($code = NULL)
	{
		$title = __('Account Verity');
		$this->head->title->set($title);
		$content = __('Congratulations! Your account passed the verification.').'<br /><br />'.
			HTML::anchor(URL::site('login'), __('Continue Login'), array('class' => 'button'));
		
		$verity = ORM::factory('verity');
		if (empty($code))
		{
			$this->template->content = Twig::factory('auth/verity')
				->bind('title', $title)
				->bind('action', $action)
				->bind('email', $email)
				->bind('hash_code', $hash_code)
				->bind('errors', $errors);
				
			$email = Arr::get($_GET, 'email');
			$action = Arr::get($_GET, 'action');

			if ($_POST)
			{
				$hash_code = Arr::get($_POST, 'hash_code');
				if (isset($hash_code))
				{
					if ($verity->validate_hash_code($_POST))
					{
						$this->template->content = Alpaca::error_page($title, $content);
					}
					else
					{
						$errors = $_POST->errors('validate');
					}
				}
				elseif (isset($_POST['email']))
				{
					$post = Validate::factory($_POST)
						->filter(TRUE, 'trim')
						->rules('email',  $this->_rules['email']);
					
					if ($post->check())
					{
						$email = Arr::get($_POST, 'email');
						$verity = ORM::factory('verity')
							->where('email', '=', $email)
							->and_where('type', '=', 'verity')
							->find();
						if ($verity->loaded())
						{
							$verity_url = Route::url('auth/actions', array(
								'action' => 'verity',
								'code' => $verity->code
							));
							$email_content = '感谢您在'.$this->_website.
								'注册的账户，于此同时，麻烦您一点时间，我们需要验证注册账户的真实性，'.
								'需要您点击下面的链接完成验证过程：<br /><br />'.
								HTML::anchor($verity_url, $verity_url).'<br /><br />'.
								'温馨提示：如果您忘记了密码，使用网站提供的重设密码功能也会把重设密码的链接发给本邮箱哦 :)';
	
							if (Alpaca::email($email, $title, $email_content))
							{
								$this->template->content = Alpaca::error_page($title, $content);
									
								$content = __('Done! We sended a mail to your :email address again.', array(
										':email' => $email
									)).
									'<br /><br />'.
									HTML::anchor(URL::site('verity'), __('Complete Verity'), array('class'=>'button')).
									__(' or ').
									HTML::anchor(URL::site('login'), __('Done! Continue Login'), array('class'=>'button')).
									'<br /><br />'.
									__('Thanks for support to %s.', array('%s' => $this->_website));
							}
							else
							{
								$this->template->content = Alpaca::error_page($title, $content);
								
								$content = __('Send failed! You may :try_again. '.
									'If it also failed, contact the website administrator.', array(
										':try_again' => '<a href="javascript:history.go(-1)">'.__('try again').'</a>')
									).'<br /><br />'.
									__('Thanks for support to %s.', array('%s' => $this->_website));
							}
						}
						else
						{
							$post->error('email', 'not_exists');
							$errors = $post->errors('validate');
						}
					}
					else
					{
						$errors = $post->errors('validate');
					}
				}
			}
		}
		else
		{
			$this->template->content = Alpaca::error_page($title, $content);
			
			if ( ! $verity->verity_code($code))
			{
				$content = __('Invalid validation code! Please confirm it correctly or had been actived.');
			}
		}
	}
}


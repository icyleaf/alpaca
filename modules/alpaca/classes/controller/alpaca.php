<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Base Alpaca Template 
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Alpaca extends Controller_Template {

	public $template 	= 'template/forum';
	public $header 		= NULL;
	public $auth 		= NULL;
	public $session 	= NULL;
	public $config 		= NULL;
	
	/**
	 * Alpaca Init
	 */
	public function before()
	{
		if (isset($_GET['lang']))
		{
			$lang = $_GET['lang'];

			// Load the accepted language list
			$translations = array_keys(Kohana::message('alpaca', 'translations'));

			if (in_array($lang, $translations))
			{
				// Set the language cookie
				Cookie::set('alpaca_language', $lang, Date::YEAR);
			}

			// Reload the page
			$this->request->redirect($this->request->uri);
		}

		// Set the translation language
		I18n::$lang = Cookie::get('alpaca_language', Kohana::config('alpaca')->language);
		
		// Instance classes
		$this->auth = Auth::instance();
		$this->session = Session::instance();
		$this->config = Kohana::config('alpaca');
		$this->header = Head::instance();
		// Title
		$this->header->title->set($this->config->title);
		$this->header->title->append($this->config->desc);
		// Css
		$this->header->css->append_file('media/css/screen.css', '0.9', 'all');
		$this->header->css->append_file('media/css/layout.css', 'date');
		// Javascript
		$this->header->javascript->append_file('media/js/jquery-1.4.2.min.js');
		$this->header->javascript->append_file('media/js/jquery/ittabs.js');
		$this->header->javascript->append_file('media/js/alpaca.js', '0.1');
		$this->header->javascript->append_file('media/js/common.js');
		$this->header->javascript->append_script('var BASH_URL = "'.URL::base(FALSE).'";');
		// Links
		$this->header->link->append('favicon.ico', '', 'icon', 'image/x-icon');
		// Menu
		$menu = $this->general_menu();
		// Check remember me 
		$this->auth->auto_login();

		if ($user = $this->auth->get_user())
		{
			$user_link = Route::url('user', array('id' => Alpaca_User::the_uri($user)));
			$auth_links = array
			(
				$user_link => array(
					'title' => $user->nickname,
					'attr' => array('class' => 'user')
				),
				'settings' => array(
					'title' => __('Settings')
				),
				'logout' => array(
					'title' => __('Log out')
				),
			);
		}
		else
		{
			$auth_links = array
			(
				'register' => array(
					'title' => __('Sign up'),
					'attr' => array('style' => 'color: #7F2D20')
				),
				'login' => array(
					'title' => __('Log in')
				),
			);
		}

		
		// Set global varibales in View
		View::bind_global('config', $this->config);
		View::bind_global('auth', $this->auth);

		// Base Template
		$this->template = View::factory($this->template)
			->bind('menu', $menu)
			->bind('auth_links', $auth_links)
			->bind('header_body', $header_body)
			->bind('footer_body', $footer_body);
		
		$header_body = View::factory('header')
			->bind('header', $this->header);
		$footer_body = View::factory('footer');
	}

	/**
	 * General top menu
	 *
	 * @return mixed
	 */
	protected function general_menu()
	{
		$groups = ORM::factory('group')
			->order_by('count', 'DESC')
			->cached(60)
			->find_all();
		
		// dropdown style layout
		$this->header->css->append_file('media/css/dropdown/dropdown.css');
		$this->header->css->append_file('media/css/dropdown/themes/flickr.com/default.ultimate.css');
		
		$menu = Menu::factory()
			->add(URL::base(), __('Home'));
		// loop group name	
		if ($groups->count() > 0)
		{
			foreach ($groups as $group)
			{
				if ($group->level == 0)
				{
					$child_menu = Menu::factory();
					$children = $group->children->find_all();
					if ($children->count() > 0)
					{
						foreach ($children as $child)
						{
							$child_menu->add(Route::url('group', array(
									'id' => Alpaca_Group::the_uri($child)
								)),
								$child->name
							);
						}
						
						$menu->add('', '<span class="dir">'.$group->name.'</span>', $child_menu);
					}
				}
			}
		}
		
		// switch languages
		$language = Menu::factory();
		foreach (Kohana::message('alpaca', 'translations') as $key => $value)
		{
			$language->add(URL::site($this->request->uri.'?lang='.$key), $value);
		}
		$menu->add('', '<span class="dir">'.__('Switch Language').'</span>', $language);

		$config = array
		(
			'id'	=> 'nav',
			'class'=> 'dropdown dropdown-horizontal',
		);
	
		return $menu->render($config);
	}
	
}


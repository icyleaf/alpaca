<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Base Alpaca Template 
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Template_Alpaca extends Controller_Template_Twig {

	public $template 	= 'template/forum';
	public $head 		= NULL;
	public $auth 		= NULL;
	public $session 	= NULL;
	public $config 		= NULL;
	
	/**
	 * Alpaca Init
	 */
	public function before()
	{
		// Instance classes
		$this->auth = Auth::instance();
		$this->session = Session::instance();
		$this->config = Kohana::config('alpaca');
		$this->head = Head::instance();

		// I18n
		$this->_i18n($this->config);
		// Title
		$this->head->title->set($this->config->title);
		$this->head->title->append($this->config->desc);
		// Css
		$this->head->css->append_file('media/css/screen.css', '0.9', 'all');
		$this->head->css->append_file('media/css/layout.css', 'date');
		// Javascript
		$this->head->javascript->append_file('media/js/jquery-1.4.2.min.js');
		$this->head->javascript->append_file('media/js/jquery/ittabs.js');
		$this->head->javascript->append_file('media/js/alpaca.js', '0.1');
		$this->head->javascript->append_file('media/js/common.js');
		$this->head->javascript->append_script('var BASH_URL = "'.URL::base(FALSE).'";');
		// Links
		$this->head->link->append('favicon.ico', '', 'icon', 'image/x-icon');
		// Check remember me 
		$this->auth->auto_login();

		if ($auth_user = $this->auth->get_user())
		{
			$user_nav = array(
				'user'     => array(
					'link'      => Alpaca_User::url('user', $auth_user),
					'title'     => $auth_user->nickname,
				),
				'settings' => array(
					'link'      => 'settings',
					'title'     => __('Settings'),
				),
				'logout' => array(
					'link'      => Route::url('auth/actions', array('action' => 'logout')),
					'title'     => __('Log out'),
				),
			);
		}
		else
		{
			$user_nav = array(
				'register'      => array(
					'link'      => Route::url('auth/actions', array('actions' => 'register')),
					'title'     => __('Sign up'),
					'attr'      => array(
						'style'     => 'color: #7F2D20'
					)
				),
				'login' => array(
					'link'      => Route::url('auth/actions', array('action' => 'login')),
					'title'     => __('Log in'),
				),
			);
		}

		$logo = HTML::anchor(URL::base(), HTML::image($this->config->logo), array('alt' => $this->config->title));
		$copyrights = Alpaca::copyright(Kohana::config('alpaca.copyright_year'));
		$powered_by = HTML::anchor($this->config->project['url'], $this->config->project['name']);
		if ($this->config->debug)
		{
			$debug = View::factory('profiler/stats');
		}

		// Set global varibales in View
		Twig::bind_global('config', $this->config);
		Twig::bind_global('auth', $this->auth);

		// Base Template
		$this->template = Twig::factory($this->template)
			->set('header', $this->head)
			->set('logo', $logo)
			->set('user_nav', $user_nav)
			->set('menu', $this->general_menu())
			->set('copyrights', $copyrights)
			->set('powered_by', $powered_by)
			->bind('debug', $debug);
	}

	protected function _i18n($config, $expire = Date::YEAR)
	{
		if ($lang = Arr::get($_GET, 'lang'))
		{
			// Load the accepted language list
			$translations = array_keys(Kohana::message('alpaca', 'translations'));

			if (in_array($lang, $translations))
			{
				// Set the language cookie
				Cookie::set('alpaca_language', $lang, $expire);
			}

			// Reload the page
			$this->request->redirect($this->request->uri);
		}

		// Set the translation language
		I18n::$lang = Cookie::get('alpaca_language', $config->language);
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
		$this->head->css->append_file('media/css/dropdown/dropdown.css');
		$this->head->css->append_file('media/css/dropdown/themes/flickr.com/default.ultimate.css');
		
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
									'id' => Alpaca_Group::uri($child)
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


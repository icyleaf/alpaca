<?php
/**
 * Menu Builder for Kohana 3
 *
 * @package 	libraries
 * @author		Corey Worrell
 * @link		http://coreyworrell.com
 * @version 	1.0
 *
 * @license 	http://www.opensource.org/licenses/bsd-license.php
 *
 * @cracked		icyleaf <icyleaf.cn@gmail.com>
 */
class Menu {

	// Associative array of list items
	public $items = array();
	
	// Associative array of attributes for list
	public $attrs = array();
	
	// Associative array of active list item
	public $current = array();
	
	
	/**
	 * Creates and returns a new Menu object
	 *
	 * @chainable
	 * @param   array   Array of list items (instead of using add() method)
	 * @return  Menu
	 */
	public static function factory(array $items = NULL)
	{
		return new Menu($items);
	}
	
	/**
	 * Constructor, globally sets $items array
	 *
	 * @param   array   Array of list items (instead of using add() method)
	 * @return  void
	 */
	public function __construct(array $items = NULL)
	{
		$this->items = $items;
	}
	
	/**
	 * Add's a new list item to the menu
	 *
	 * @chainable
	 * @param   string   Title of link
	 * @param   string   URL (address) of link
	 * @param   Menu     Instance of class that contain children
	 * @return  Menu
	 */
	public function add($url, $title, Menu $children = NULL)
	{
		$this->items[] = array
		(
			'url'		=> $url,
			'title'		=> $title,
			'children'	=> is_object($children) ? $children->items : NULL,
		);
		return $this;
	}
	
	/**
	 * Renders the HTML output for the menu
	 *
	 * @param   array   Associative array of html attributes
	 * @param   array   Associative array containing the key and value of current url
	 * @param   array   The parent item's array, only used internally
	 * @return  string  HTML unordered list
	 */
	public function render(array $attrs = NULL, $current = NULL, array $items = NULL)
	{
		static $i;
		
		$items = empty($items) ? $this->items : $items;
		$current = empty($current) ? $this->current : $current;
		$attrs = empty($attrs) ? $this->attrs : $attrs;
		
		$i++;
		
		$output = '<ul'.($i == 1 ? Menu::_attributes($attrs) : NULL).'>';
		foreach ($items as $key => $item)
		{
			//echo Kohana::debug($item);
			$has_children = ! empty($item['children']);

			$class = array();
			$has_children ? $class[] = 'parent' : NULL;
			
			if ( ! empty($current))
			{
				if ($current_class = Menu::_current($current, $item))
				{
					$class[] = $current_class;
				}
			}
			
			$classes = ! empty($class) ? Menu::_attributes(array('class' => implode(' ', $class))) : NULL;

			$link = empty($item['url']) ? $item['title'] : HTML::anchor($item['url'], $item['title']);
			$output .= '<li'.$classes.'>' . $link;
			$output .= $has_children ? $this->render(NULL, $current, $item['children']) : NULL;
			$output .= '</li>';
		}
		$output .= '</ul>';
		
		$i--;
		
		return $output;
	}
	
	/**
	 * Recursive function to check if active item is child of parent item
	 *
	 * @param   array   The list item
	 * @param   string  The current active item
	 * @param   string  Key to match current against
	 * @return  bool
	 */
	public static function active($array, $value, $key)
	{
		foreach ($array as $val)
		{
			if (is_array($val))
			{
				if (Menu::active($val, $value, $key))
					return TRUE;
			}
			else
			{
				if ($array[$key] === $value)
					return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * Renders the HTML output for menu without any attributes or active item
	 *
	 * @return   string
	 */
	public function __toString()
	{
		return $this->render();
	}
	
	/**
	 * Easily set the current url, or list attributes
	 *
	 * @param   mixed   Value to set to
	 * @return  void
	 */
	public function __set($key, $value)
	{
		if ($key === 'current')
		{
			$this->current = $value;
		}
		else
		{
			$this->attrs[$key] = $value;
		}
	}
	
	/**
	 * Get the current url or a list attribute
	 *
	 * @return   mixed   Value of key
	 */
	public function __get($key)
	{
		if ($key === 'current')
		{
			return $this->current;
		}
		else
		{
			return $this->attrs[$key];
		}
	}
		
	/**
	 * Nicely outputs contents of $this->items for debugging info
	 *
	 * @return   string
	 */
	public function debug()
	{
		return Kohana::debug($this->items);
	}
	
	/**
	 * Compiles an array of HTML attributes into an attribute string.
	 *
	 * @param   string|array  array of attributes
	 * @return  string
	 */
	protected static function _attributes($attrs)
	{
		if (empty($attrs))
			return '';

		if (is_string($attrs))
			return ' '.$attrs;

		$compiled = '';
		foreach ($attrs as $key => $val)
		{
			$compiled .= ' '.$key.'="'.htmlspecialchars($val).'"';
		}

		return $compiled;
	}
	
	/**
	 * Figures out if items are parents of the active item.
	 *
	 * @param   array   The current url array (key, match)
	 * @param   array   The array to check against
	 * @return  bool
	 */
	protected static function _current($current, array $item)
	{
		if ($current === $item['url'])
			return 'active current';
			
		else
		{
			if (Menu::active($item, $current, 'url'))
				return 'active';
		}
		
		return '';
	}
	
}


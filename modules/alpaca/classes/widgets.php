<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Widget Class
 *
 * @package alpaca
 * @author icyleaf
 */
class Widgets {
	
	// Widgets singleton
	private static $_instance;
	
	protected $widgets = array();

	public static function instance()
	{
		// Create the instance if it does not exist
		empty(Widgets::$_instance) AND new Widgets;

		return Widgets::$_instance;
	}
	
	public function __construct()
	{
		self::$_instance = $this;
	}

	public function add($config = array())
	{
		$widget = array
		(
			'name'			=> __('Widget'),
			'child_of'		=> FALSE,
			'sort' 			=> array(
				'column'		=>'created', 
				'direction'		=>'ASC'
			),
			'link_before'	=> '',
			'link_after' 	=> '',
			'render' 		=> TRUE
		);
		
		$widget = array_merge($widget, $config);
		
		$this->widgets[] = $widget;
	}

	public function render()
	{
		$output = '';
		foreach ($this->widgets as $widget)
		{
			$output .= $this->_general($widget);
		}

		return $output;
	}
	
	private function _general($widget)
	{
		
	}

	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
	}
	
}


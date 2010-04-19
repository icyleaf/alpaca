<?php defined('SYSPATH') or die('No direct script access.');
/**
 * URI helper
 *
 * @package helper
 * @author icyleaf
 */
class URI {
	
	private $request = null;
	private static $segments = array();
	
	public static function instance()
	{
		static $instance;

		if ($instance == NULL)
		{
			// Initialize the URI instance
			$instance = new URI;
		}

		return $instance;
	}
	
	public function __construct()
	{
		$this->request = Request::instance();
		URI::$segments = $this->segment_array();
	}
	
	public function segment_array()
	{
		$reg = '/[^\w\s]/';
		$current = $this->current();
		
		URI::$segments = preg_split($reg, $current);
		
		return URI::$segments;
	}

	
	public function segment($index = 1, $default = FALSE)
	{
		if (is_string($index))
		{
			if (($key = array_search($index, URI::$segments)) === FALSE)
				return $default;

			$index = $key + 2;
		}

		$index = (int) $index - 1;
		
		return isset(URI::$segments[$index]) ? URI::$segments[$index] : $default;
	}
	
	public function total_segments()
	{
		return count(URI::$segments);
	}
	
	public function last_segment($default = FALSE)
	{
		if (($end = $this->total_segments()) < 1)
			return $default;
		
		return URI::$segments[$end - 1];
	}
	
	public function current($full = FALSE)
	{
		$current_uri = $this->request->uri;
		
		if ( $full )
		{
			$current_uri = URL::site($current_uri);
		}
		
		if ( $_GET )
		{
			$current_uri .= '?'.http_build_query($_GET, '&');
		}
		
		return $current_uri;
	}

}
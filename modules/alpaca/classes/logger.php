<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Log Viwer for Kohana 3
 *
 * @package 	libraries
 * @author 	icyleaf <icyleaf.cn@gmail.com>
 * @link		http://icyleaf.com
 * @version 	0.1
 *
 * @license 	http://www.opensource.org/licenses/bsd-license.php
 */
class Logger {
	
	// Kohana log directory
	private $path = null;
	
	// Kohana all log files
	private $logs = null;
	
	/**
	 * Creates a new Logger object.
	 *
	 * @param string $path	log file path
	 * @return object
	 */
	public static function instance($path = NULL)
	{
		static $instance;

		if ($instance == NULL)
		{
			// Initialize the Logger instance
			$instance = new Logger($path);
		}

		return $instance;
	}
	
	/**
	 * Creates a new Logger object.
	 *
	 * @param string $path	log file path
	 */
	public function __construct($path = NULL)
	{
		if ($path == NULL)
		{
			$this->path = APPPATH.'logs';
		}
		else
		{
			$this->path = $path;
		}
		
		$this->logs = $this->_list_files($this->path);
	}
	
	/**
	 * Get Log files
	 *
	 * @param int $year
	 * @param int $month 
	 * @param int $day 
	 * @return array
	 */
	public function get_logs($year = NULL, $month = NULL, $day = NULL)
	{
		if ( ! empty($year))
		{
			if ( ! empty($month))
			{
				if ( ! empty($day))
				{
					return isset($this->logs[$year][$month][$day]) ? $this->logs[$year][$month][$day] : NULL;
				}
				else
				{
					return isset($this->logs[$year][$month]) ? $this->logs[$year][$month] : NULL;
				}
			}
			else
			{
				return isset($this->logs[$year]) ? $this->logs[$year] : NULL;
			}
		}

		return $this->logs;
	}
	
	/**
	 * Get single log file
	 *
	 * @param int $year 
	 * @param int $month 
	 * @param int $day 
	 * @param mixed $ext 
	 * @return array
	 */
	public function get_log($year, $month, $day, $ext = FALSE)
	{
		$ext = ($ext) ? '.' . $ext : EXT;
		$file = $this->path . '/' . $year . '/' . $month . '/' . $day . $ext;

		if ( ! file_exists($file))
		{
			return NULL;
		}
		
		$reg = '/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) --- (\w+):(.*)/';
		$logs = array();
		$i = 0;
		$fp = fopen($file, 'r');
		while ( ! feof($fp))
		{
			$line = fgets($fp, 4096);
			if (preg_match($reg, $line, $match))
			{
				$logs[$i]['date'] = strtotime($match[1]);
				$logs[$i]['type'] = strtoupper($match[2]);
				$logs[$i]['desc'] = trim($match[3]);
				$i++;
			}
		}
		fclose($fp);

		return $logs;
	}
	
	/**
	 * Returns a Logger property.
	 *
	 * @param   string $key	property name
	 * @return  mixed	Logger property; NULL if not found
	 */
	public function __get($key)
	{
		return isset($this->$key) ? $this->$key : NULL;
	}
	
	
	/**
	 * Updates a single config setting, and recalculates pagination if needed.
	 *
	 * @param   string	$key	property name
	 * @param   mixed	$value	property value
	 */
	public function __set($key, $value)
	{
		if (isset($this->$key))
		{
			$this->$key = $value;
		}
	}
	
	/**
	 * List log files
	 *
	 * @param string $path	log file path
	 * @return array
	 */
	private function _list_files($path = NULL)
	{
		$path = empty($path) ? $this->path : $path;
		$list = array();
		foreach (glob($path.'/*') as $item)
		{
			if(is_dir($item))
			{
				$directory_name =  substr($item, strlen($path)+1);
				$list[$directory_name] = $this->_list_files($item);
			}
			else
			{
				$reg = '/(\w+).\w+/';
				$full_name = substr($item, strlen($path)+1);
				if (preg_match($reg, $full_name, $match))
				{
					$list[$match[1]] = array
					(
						'file' 			=> substr($item, strlen(DOCROOT)),
						'created_date'	=> filectime($item),
						'modify_date'	=> filemtime($item),
					);
				}
				else
				{
					$list[] = $item;
				}
			}
		}
		
		return $list;
	}

}


<?php defined('SYSPATH') or die('No direct script access.');
/**
 * L10n
 *
 * @package library
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class L10n {
	
	private $_path = NULL;
	private $_files = array();
	
	public $type = array
	(
		'php', 
		'html',
	);
	
	private static $_instance = array();
	
	// Instance
	public static function instance($path, $traverse = FALSE)
	{
		if ( ! empty(L10n::$_instance['path']))
		{
			return L10n::$_instance['path'];
		}
		
		L10n::$_instance['path'] = new L10n($path, $traverse);
		
		return L10n::$_instance['path'];
	}
	
	public function __construct($path, $traverse = FALSE)
	{
		$this->_path = $path;
		$this->_files = $traverse ? $this->_list_files() : array();
	}

	/**
	 * Fetch single file
	 *
	 * @param string $filename 
	 * @param string $directory 
	 * @return array
	 */
	public function fetch($filename, $directory = NULL)
	{
		$this->_files = empty($this->_files) ? $this->_list_files() : $this->_files;
		
		return $this->_extract($filename, $directory);
	}
	
	/**
	 * Fetch all files from specified folder
	 *
	 * @param mixed $directory 
	 * @return array
	 */
	public function fetch_all(array $directory = NULL)
	{
		if (empty($directory))
		{
			$directory = empty($this->_files) ? $this->_list_files() : $this->_files;
		}
		
		$i18n = array();
		foreach ($directory as $name => $file)
		{
			if (is_array($file))
			{
				//$i18n[$name] = $this->fetch_all($file);
			}
			else
			{
				$i18n[$name] = $this->_extract($name, $directory);
			}
		}
		
		return $i18n;
	}
	
	/**
	 * Get files form specified folder
	 *
	 * @param string $path 
	 * @return array
	 */
	private function _list_files($path = NULL)
	{
		$path = empty($path) ? $this->_path : $path;
		$files = array();
		foreach (glob($path.'/*') as $item)
		{
			if( ! is_dir($item))
			{
				$full_name = substr($item, strlen($path)+1);
				if (preg_match('/^\w+$/', $full_name))
				{
					$files[$full_name] = $item;
				}
				elseif (preg_match('/(\w+).\w+/', $full_name, $match))
				{
					$files[$match[1]] = $item;
				}
				else
				{
					$files[] = $item;
				}
			}
			else
			{
				$directory_name =  substr($item, strlen($path)+1);
				$files[$directory_name] = $this->_list_files($item);
			}
		}
		
		return $files;
	}
	
	/**
	 * Extract I18n key words
	 *
	 * @param string $filename 
	 * @param string $directory 
	 * @return array
	 */
	private function _extract($filename, $directory = NULL)
	{
		$files = empty($this->_files) ? $this->_list_files() : $this->_files;
		if ( ! empty($directory))
		{
			if (is_array($directory))
			{
				$file = $directory[$filename];
			}
			elseif (preg_match('/^(\w+\/)+/', $directory))
			{
				$folders = explode('/', $directory);
				foreach($folders as $folder)
				{
					if ( ! array_key_exists($folder, $files))
					{
						return FALSE;
					}
					
					$files = $files[$folder];
				}
				$file = $files[$filename];
			}
			else
			{
				$file = $files[$directory][$filename];
			}
		}
		else
		{
			$file = $files[$filename];
		}
		
		$suffix = substr($file, -3, 3);
		if ( ! is_array($file) AND in_array($suffix, $this->type) AND array_key_exists($filename, $files))
		{
			$file_content = file_get_contents($file);
			$reg = '/__\((("(.*)")|(\'(.*)\'))/';
			if (preg_match_all($reg, $file_content, $match))
			{
				return $match[5];
			}
			else
			{
				return NULL;
			}
		}
		else
		{
			return FALSE;
		}
	}
	
}


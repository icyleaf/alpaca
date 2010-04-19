<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Append title, css, javascript codes in head section
 *
 * @package libraries
 * @author maartenvanvliet, icyleaf
 * @license http://www.opensource.org/licenses/bsd-license.php
 * 
 *
 * Original file was come form MPTT for Kohana writting by maartenvanvliet, and url is 
 * http://code.google.com/p/kohana-mptt/source/browse/trunk/test/Head.php. icyleaf append 
 * and modify some new or exist functions.
 * 
 * maartenvanvliet's url: http://code.google.com/p/kohana-mptt/
 * icyleaf's url: http://icyleaf.com
 *
 *
 * ====  Update List[02/13/2009]  ====
 *
 * New:
 * function Head_Css_Brower_Hacks
 *      - brower hacks with css (could custom brower type. eg, ie, opera, safari).
 * function Head_Js_Brower_Hacks
 *      - brower hacks with javascript (could custom brower type. eg, ie, opera, safari).
 *
 *
 * Modify:
 * function Head_Css_File
 *     - add one new parameter: $version, is append a version sign to the end of css file.
 *     - $version could be set any strings, if set 'date', it will display creat date of specify file.
 * function Head_Javascript_File
 *     - it is similar to Head_Css_File function.
 */

class Head extends ArrayObject {

	// Head singleton
	private static $_instance;

	/**
	 * Head instance of Head.
	 */
	public static function instance()
	{
		// Create the instance if it does not exist
		empty(self::$_instance) AND new Head;

		return self::$_instance;
	}

	public function __construct()
	{
		$this['title']      = new Head_Title;
		$this['base']       = new Head_Base;
		$this['javascript'] = new Head_Javascript;
		$this['css']        = new Head_Css;
		$this['link']       = new Head_Link;

		$this->setFlags(ArrayObject::ARRAY_AS_PROPS);

		// Singleton instance
		self::$_instance = $this;
	}

	public function __tostring()
	{
		return (string) $this->render();
	}

	public function render()
	{
		$html = '';
		foreach ($this as $field)
		{
			$html .= $field->render();
		}

		return $html;
	}
}

class Head_Partial extends Head {

	public function __construct()
	{
		$this->setFlags(ArrayObject::ARRAY_AS_PROPS);
	}

}

// Title
class Head_Title extends Head_Partial {

	public function __construct($title = '')
	{
		$this['title'] = $title;
	}

	public function set($title)
	{
		$this['title'] = $title;
		return $this;
	}

	public function append($title)
	{
		$this['title'] .= ' - '.$title;
		return $this;
	}

	public function prepend($title)
	{
		$this['title'] = $title.' - '.$this['title'];
		return $this;
	}

	public function render()
	{
		if ($this['title'] != '')
		{
			return (string) '<title>'.$this['title'].'</title>'."\r\n";
		}

		return '';
	}
}

class Head_Base extends Head_Partial {

	public function __construct($base = '')
	{
		$this['base_href'] = $base;
	}

	public function set($base_href)
	{
		$this['base_href'] = $base_href;
		return $this;
	}

	public function render()
	{
		if ($this['base_href'] != '')
		{
			return (string) '<base href="'.$this['base_href'].'" />'."\r\n";
		}

		return '';
	}

}

// Javascript
class Head_Javascript extends Head_Partial {

	public function __construct()
	{
		$this->setFlags(ArrayObject::ARRAY_AS_PROPS);
		$this['files']   = new Head_Javascript_File;
		$this['scripts'] = new Head_Js_Script;
		$this['hacks_files'] = new Head_Js_Brower_Hacks;
	}

	public function append_file($file, $version = '')
	{
		$this['files'][] = array($file, $version);
		return $this;
	}

	public function append_script($script)
	{
		$this['scripts'][] = $script;
		return $this;
	}
	
	public function brower_hacks($file, $brower = 'IE', $version = '')
	{
		$this['hacks_files'][] = array($file, $brower, $version);
		return $this;
	}

}

class Head_Javascript_File extends Head_Partial {

	public function render()
	{
		$html = '';
		foreach ($this as $field)
		{
			if ( empty($field[1]) )
			{
				$html .= HTML::script($field[0]);
			}
			else if ( $field[1]=='date' )
			{
				$date = file_exists($field[0])?(filemtime($field[0])?'?ver='.(date('Ymd', filemtime($field[0]))):''):'';
				$html .= '<script type="text/javascript" src="'.url::base().$field[0].$date.'"></script>';
			}
			else
			{
				$html .= '<script type="text/javascript" src="'.url::base().$field[0].'?ver='.$field[1].'"></script>';
			}
			$html .= "\r\n";
		}

		return $html;
	}
}

class Head_Js_Script extends Head_Partial {

	public function render()
	{
		$html = '';
		foreach ($this as $script)
		{
			$html .= '<script type="text/javascript">'."\n".$script."\n".'</script>'."\r\n";
		}

		return $html;
	}

}

class Head_Js_Brower_Hacks extends Head_Partial{
	
	public function render()
	{
		$html = '';
		foreach ($this as $field)
		{
            if ( $field[2]=='7' ) {
                $html .= "<!--[if lt ".$field[1]." 7]>\n".HTML::script($field[0])."<![endif]-->";
            } else if ( $field[2]=='8' ) {
                $html .= "<!--[if lt ".$field[1]." 8]>\n".HTML::script($field[0])."<![endif]-->";
            } else {
                $html .= "<!--[if ".$field[1]."]>\n".HTML::script($field[0])."<![endif]-->";
            }
            $html .= "\r\n";
		}
		return $html;
	}
}

// CSS
class Head_Css extends Head_Partial {

	public function __construct()
	{
		$this->setFlags(ArrayObject::ARRAY_AS_PROPS);
		$this['files']  = new Head_Css_File;
		$this['styles'] = new Head_Css_Style;
        $this['hacks_files'] = new Head_Css_Brower_Hacks;
	}

	public function append_file($file, $version = null, $type = 'screen')
	{
		$this['files'][] = array($file, $version, $type);
		return $this;
	}

	public function append_style($script)
	{
		$this['styles'][] = $script;
		return $this;
	}

    public function brower_hacks($file, $brower = 'IE', $version = null)
	{
		$this['hacks_files'][] = array($file, $brower, $version);
		return $this;
	}
}

class Head_Css_File extends Head_Partial {

	public function render()
	{
		$html = '';
		foreach ($this as $field)
		{
			if ( empty($field[1]) )
			{
				$html .= HTML::style($field[0], array('media' => $field[2]));
			}
			else if ( $field[1]=='date' )
			{
				$date = file_exists($field[0])?(filemtime($field[0])?'?ver='.(date('Ymd', filemtime($field[0]))):''):'';
				$html .= HTML::style($field[0].$date, array('media' => $field[2]));
			}
			else if ( $field[1]=='all' || $field[1]=='screen' || $field[1]=='print')
			{
				$html .= HTML::style($field[0], array('media' => $field[1]));
			}
			else
			{
				$html .= HTML::style($field[0].'?ver='.$field[1], array('media' => $field[2]));
			}
			
			$html .= "\r\n";
		}

		return $html;
	}

}

class Head_Css_Style extends Head_Partial {

	public function render()
	{
		$html = '';
		foreach ($this as $script)
		{
			$html .= '<style type="text/css">'.$script.'</style>'."\r\n";
		}

		return $html;
	}

}

class Head_Css_Brower_Hacks extends Head_Partial {

	public function render()
	{
		$html = '';
		foreach ($this as $field)
		{
            if ( $field[2]=='7' ) {
                $html .= "<!--[if lt ".$field[1]." 7]>\n".HTML::style($field[0])."<![endif]-->";
            } else if ( $field[2]=='8' ) {
                $html .= "<!--[if lt ".$field[1]." 8]>\n".HTML::style($field[0])."<![endif]-->";
            } else {
                $html .= "<!--[if ".$field[1]."]>\n".HTML::style($field[0])."<![endif]-->";
            }
            $html .= "\r\n";
		}
		return $html;
	}

}

// Other Links
class Head_Link extends Head_Partial {

	public function append($link, $title = '', $rel = 'alternate', $type = 'application/rss+xml')
	{
		$this[] = array($link, $title, $rel, $type);
		return $this;
	}

	public function render()
	{
		$html = '';
		foreach ($this as $link)
		{
			$attributes = array
			(
				'rel' 	=> $link[2],
			);
			
			if ( preg_match('/^http:\/\//i', $link[0]) )
			{
				$attributes['href'] = $link[0];
			}
			else
			{
				$attributes['href'] = url::base(FALSE).$link[0];
			}
			
			if ( !empty($link[3]) )
			{
				$attributes['type'] = $link[3];
			}
			
			if ( !empty($link[1]) )
			{
				$attributes['title'] = $link[1];
			}
			
			$html .= '<link'.HTML::attributes($attributes).' />'."\r\n";;
		}

		return $html;
	}

}
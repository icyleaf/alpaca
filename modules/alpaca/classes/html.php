<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Overwite HTML
 *
 * @package kohana
 * @author icyleaf
 */
class HTML extends Kohana_HTML {
	
	/**
	 * @var  boolean  automatically target external URLs to a new window
	 */
	public static $windowed_urls = FALSE;
	
	/**
	 * Create HTML link anchors. Note that the title is not escaped, to allow
	 * HTML elements within links (images, etc).
	 *
	 * @param   string  URL or URI string
	 * @param   string  link text
	 * @param   array   HTML anchor attributes
	 * @param   string  use a specific protocol
	 * @return  string
	 */
	public static function anchor($uri, $title = NULL, array $attributes = NULL, $protocol = NULL)
	{
		if ($title === NULL)
		{
			// Use the URI as the title
			$title = $uri;
		}

		if ($uri === '')
		{
			// Only use the base URL
			$uri = URL::base(FALSE, $protocol);
		}
		else
		{
			if (strpos($uri, '://') !== FALSE)
			{
				if (HTML::$windowed_urls === TRUE AND empty($attributes['target']))
				{
					// Append external Attribute to external link
					if (preg_match('/^(http:\/\/)?([^\/]+)\//i', $uri, $match) AND $match[0] != url::base(FALSE))
					{
						// Make the link open in a new window
						$attributes['target'] = '_blank';
					}
				}
			}
			elseif ($uri[0] !== '#')
			{
				// Make the URI absolute for non-id anchors
				$uri = URL::site($uri, $protocol);
			}
		}

		// Add the sanitized link to the attributes
		$attributes['href'] = $uri;

		return '<a'.HTML::attributes($attributes).'>'.$title.'</a>';
	}
}
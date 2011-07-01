<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Helper
 *
 * @package Alpaca
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Alpaca {
	
	/**
	 * BBCODE to HTML
	 *
	 * @param string $text 
	 * @param string $emoticon 
	 * @return string
	 */
	public static function format_html($text, $whitespace = FALSE, $br = TRUE)
	{
		// Convert special characters to HTML entities
		$text = htmlspecialchars($text);

		// image and link
		$regular = array(
			'#\[img\]([\w]+?://[\w\#$%&~/.\-;:=,' . "'" . '?@\[\]+]*?)\[/img\]#is',
			// [img=xxxx://www.kohana.cn]image url[/img]
			'#\[img=([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([\w]+?://[\w\#$%&~/.\-;:=,' . "'" . '?@\[\]+]*?)\[/img\]#is',
			// [img=www.kohana.cn]image url[/img]
			'#\[img=((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([\w]+?://[\w\#$%&~/.\-;:=,' . "'" . '?@\[\]+]*?)\[/url\]#is',
			
			// [url]xxxx://www.kohana.cn[/url]
			'#\[url\]([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)\[/url\]#is',
			// [url]www.kohana.cn[/url]
			'#\[url\]((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*?)\[/url\]#is',
			// [url=xxxx://www.kohana.cn]KohanaCN[/url]
			'#\[url=([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([^?\n\r\t].*?)\[/url\]#is',
			// [url=www.kohana.cn]KohanaCN[/url]
			'#\[url=((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([^?\n\r\t].*?)\[/url\]#is',
			
			'/\[b\](.*?)\[\/b\]/i',
			'/\[strong\](.*?)\[\/strong\]/i',
			'/\[i\](.*?)\[\/i\]/i',
			'/\[em\](.*?)\[\/em\]/i',
			'/\[u\](.*?)\[\/u\]/i',
			'/\[s\](.*?)\[\/s\]/i',
			'/\[strike\](.*?)\[\/strike\]/i',
		);
		
		$replace = array
		(
			'<img class="tpi" src="$1" border="0" />',
			'<a href="$1" rel="nofollow external" class="tpa"><img class="code" src="$2" border="0" /></a>',
			'<a href="http://$1" rel="nofollow external" class="tpa"><img class="code" src="$2" border="0" /></a>',
			
			'<a href="$1" rel="nofollow external" class="tpa">$1</a>',
			'<a href="http://$1" rel="nofollow external" class="tpa">http://$1</a>',
			'<a href="$1" rel="nofollow external" class="tpa">$2</a>',
			'<a href="http://$1" rel="nofollow external" class="tpa">$2</a>',
			
			'<strong>$1</strong>',
			'<strong>$1</strong>',
			'<em>$1</em>',
			'<em>$1</em>',
			'<u>$1</u>',
			'<strike>$1</strike>',
			'<strike>$1</strike>',
		);
		$text = preg_replace($regular, $replace, $text);
		
		// Quote
		preg_match('/\[quote\]/i', $text, $bbcode_quote_open);
		preg_match('/\[\/quote\]/i', $text, $bbcode_quote_close);
		if (count($bbcode_quote_open) == count($bbcode_quote_close))
		{
			$text = str_ireplace("[quote]\n", '[quote]', $text);
			$text = str_ireplace("\n[/quote]", '[/quote]', $text);
			$text = str_ireplace("[quote]\r", '[quote]', $text);
			$text = str_ireplace("\r[/quote]", '[/quote]', $text);
			$text = str_ireplace('[quote]', '<blockquote>', $text);
			$text = str_ireplace('[/quote]', '</blockquote>', $text);
		}
	
		// Code
		preg_match('/\[code\]/i', $text, $bbcode_code_open);
		preg_match('/\[\/code\]/i', $text, $bbcode_code_close);
		if (count($bbcode_code_open) == count($bbcode_code_close))
		{
			$text = str_ireplace("[code]\n", '[code]', $text);
			$text = str_ireplace("\n[/code]", '[/code]', $text);
			$text = str_ireplace("[code]\r", '[code]', $text);
			$text = str_ireplace("\r[/code]", '[/code]', $text);
			$text = str_ireplace('[code]', '<pre class="code">', $text);
			$text = str_ireplace('[/code]', '</pre>', $text);
		}
		
		// smiles:
		$emoticon_path = 'media/images/icons/emoticon/';
		$attribute = array('class' => 'emoticon');
		$text = str_ireplace(':)', HTML::image($emoticon_path . 'smile.png', $attribute), $text);
		$text = str_ireplace(':-)', HTML::image($emoticon_path . 'smile.png', $attribute), $text);
		$text = str_ireplace(':o', HTML::image($emoticon_path . 'surprised.png', $attribute), $text);
		$text = str_ireplace(':-o', HTML::image($emoticon_path . 'surprised.png', $attribute), $text);
		$text = str_ireplace(':(', HTML::image($emoticon_path . 'unhappy.png', $attribute), $text);
		$text = str_ireplace(':-(', HTML::image($emoticon_path . 'unhappy.png', $attribute), $text);
		$text = str_replace(':D', HTML::image($emoticon_path . 'grin.png', $attribute), $text);
		$text = str_replace(':-D', HTML::image($emoticon_path . 'grin.png', $attribute), $text);
		$text = str_ireplace(':p', HTML::image($emoticon_path . 'tongue.png', $attribute), $text);
		$text = str_ireplace('^_^', HTML::image($emoticon_path . 'waii.png', $attribute), $text);
		$text = str_ireplace('^-^', HTML::image($emoticon_path . 'waii.png', $attribute), $text);
		$text = str_ireplace('^o^', HTML::image($emoticon_path . 'happy.png', $attribute), $text);
		$text = str_ireplace('^^', HTML::image($emoticon_path . 'happy.png', $attribute), $text);
		$text = str_ireplace('XD', HTML::image($emoticon_path . 'evilgrin.png', $attribute), $text);
		$text = str_ireplace(';)', HTML::image($emoticon_path . 'wink.png', $attribute), $text);
		unset($emoticon_path, $attribute);
		
		// Inserts HTML line breaks before all newlines in a string
		$text = Alpaca::auto_p($text, $whitespace, $br);
		if (strpos($text, '<pre') !== FALSE)
		{
			$text = preg_replace_callback('!(<pre[^>]*>)(.*?)</pre>!is', array('self', 'clean_pre'), $text);
		}
		
		return $text;
	}
	
	/**
	 * Copy Kohana Text::auto_p method
	 * but added one parameter to control if it auto corvert whitespace
	 *
	 * @param string $str 
	 * @param boolean $whitespace 
	 * @param boolean $br 
	 * @return string
	 */
	public static function auto_p($str, $whitespace = FALSE, $br = TRUE)
	{
		// Trim whitespace
		if (($str = trim($str)) === '')
			return '';

		// Standardize newlines
		$str = str_replace(array("\r\n", "\r"), "\n", $str);

		// Trim whitespace on each line
		if ($whitespace === TRUE)
		{
			$str = preg_replace('~^[ \t]+~m', '', $str);
			$str = preg_replace('~[ \t]+$~m', '', $str);
		}

		// The following regexes only need to be executed if the string contains html
		if ($html_found = (strpos($str, '<') !== FALSE))
		{
			// Elements that should not be surrounded by p tags
			$no_p = '(?:p|div|h[1-6r]|ul|ol|li|blockquote|d[dlt]|pre|t[dhr]|t(?:able|body|foot|head)|c(?:aption|olgroup)|form|s(?:elect|tyle)|a(?:ddress|rea)|ma(?:p|th))';

			// Put at least two linebreaks before and after $no_p elements
			$str = preg_replace('~^<'.$no_p.'[^>]*+>~im', "\n$0", $str);
			$str = preg_replace('~</'.$no_p.'\s*+>$~im', "$0\n", $str);
		}

		// Do the <p> magic!
		$str = '<p>'.trim($str).'</p>';
		$str = preg_replace('~\n{2,}~', "</p>\n\n<p>", $str);

		// The following regexes only need to be executed if the string contains html
		if ($html_found !== FALSE)
		{
			// Remove p tags around $no_p elements
			$str = preg_replace('~<p>(?=</?'.$no_p.'[^>]*+>)~i', '', $str);
			$str = preg_replace('~(</?'.$no_p.'[^>]*+>)</p>~i', '$1', $str);
		}

		// Convert single linebreaks to <br />
		if ($br === TRUE)
		{
			$str = preg_replace('~(?<!\n)\n(?!\n)~', "<br />\n", $str);
		}

		return $str;
	}
	
	/**
	 * preg_replace_callback method to clean pre (No <br>, <p> labels)
	 * 
	 *
	 * @param string $matches 
	 * @return string
	 */
	private static function clean_pre($matches)
	{
		if (is_array($matches))
		{
			$text = $matches[1] . $matches[2] . "</pre>";
		}	
		else
		{
			$text = $matches;
		}
		
		$text = str_replace('<br />', '', $text);
		$text = str_replace('<p>', "\n", $text);
		$text = str_replace('</p>', '', $text);

		// smiles:
		$emoticon_path = 'media/images/icons/emoticon/';
		$attribute = array('class' => 'emoticon');
		$text = str_ireplace(HTML::image($emoticon_path . 'smile.png', $attribute), ':)', $text);
		$text = str_ireplace(HTML::image($emoticon_path . 'smile.png', $attribute), ':-)', $text);
		$text = str_ireplace(HTML::image($emoticon_path . 'surprised.png', $attribute), ':o', $text);
		$text = str_ireplace(HTML::image($emoticon_path . 'surprised.png', $attribute), ':-o', $text);
		$text = str_ireplace(HTML::image($emoticon_path . 'unhappy.png', $attribute), ':(', $text);
		$text = str_ireplace(HTML::image($emoticon_path . 'unhappy.png', $attribute), ':-(', $text);
		$text = str_ireplace(HTML::image($emoticon_path . 'grin.png', $attribute), ':D', $text);
		$text = str_ireplace(HTML::image($emoticon_path . 'grin.png', $attribute), ':-D', $text);
		$text = str_ireplace(HTML::image($emoticon_path . 'tongue.png', $attribute), ':p', $text);
		$text = str_ireplace(HTML::image($emoticon_path . 'waii.png', $attribute), '^_^', $text);
		$text = str_ireplace(HTML::image($emoticon_path . 'waii.png', $attribute), '^-^', $text);
		$text = str_ireplace(HTML::image($emoticon_path . 'happy.png', $attribute), '^o^', $text);
		$text = str_ireplace(HTML::image($emoticon_path . 'happy.png', $attribute), '^^', $text);
		$text = str_ireplace(HTML::image($emoticon_path . 'evilgrin.png', $attribute), 'XD', $text);
		$text = str_ireplace(HTML::image($emoticon_path . 'wink.png', $attribute), ';)', $text);
		unset($emoticon_path, $attribute);
	
		return $text;
	}
	
	/**
	 * Convert Seconds to time ago mode
	 *
	 * @param int $date 
	 * @return string
	 */
	public static function time_ago($date)
	{
		$timesince = FALSE;
		if ( ! empty($date))
		{
			$ago = date('U') - $date;
			$periods = array(__('second'), __('minute'), __('hour'), __('day'), __('week'), __('month'), __('year'), __('ten year'));
			$lengths = array('60', '60', '24', '7', '4.35', '12', '10');
			for ($j = 0; $ago >= $lengths[$j]; $j++)
			{
				$ago /= $lengths[$j];
			}
			$ago = round($ago);

			if ($ago != 1)
			{
				$periods[$j].= __('s');
			}
			$timesince = $ago.' '.$periods[$j].__(' ago');
		}

		return $timesince;
	}
	
	/**
	 * Send email by phpmailer 
	 *
	 * the default:
	 * 		$from_address is Website Name <noreply@domain>
	 *		$content include a mail template 
	 *
	 * @param string $to 
	 * @param string $subject 
	 * @param string $content 
	 * @param string $from 
	 * @return boolean
	 */
	public static function email($to, $subject, $content, $from = NULL)
	{
		$config = Kohana::config('alpaca');
		$prefix = '['.$config->title.']';
		
		if ( ! strstr($subject, $prefix))
		{
			$subject = $prefix . ' ' . trim($subject);
		}

		$mailer = iMailer::instance()->subject($subject);
		// STMP server
		if ( ! empty($config->smtp_server['host']) AND ! empty($config->smtp_server['port']))
		{
			$mailer->smtp = $config->smtp_server;
		}
		// send to address
		if (is_array($to))
		{
			$email = $to['email'];
			$mailer->to_address($to['email'], $to['name']);
		}
		else
		{
			$email = $to;
			$mailer->to_address($to);
		}
		// send from address
		if (is_array($from))
		{
			$mailer->from_address($from['email'], $from['name']);
		}
		elseif ( ! empty($from))
		{
			$mailer->from_address($from);
		}
		else
		{
			$domain = substr(str_replace('http://', '', URL::site()), 0, -1);
			$mailer->from_address('noreply@'.$domain, $config->title);
		}

		$user = ORM::factory('user')->where('email', '=', $email)->find();
		if (I18n::$lang == 'zh-cn')
		{
			$website = Alpaca::beautify_str($config->title, TRUE, TRUE);
			$username = Alpaca::beautify_str($user->nickname);
		}
		else
		{
			$website = $config->title;
			$username = $user->nickname;
		}

		// mail message
		$content = Twig::factory('template/mail')
			->set('username', $username)
			->set('website', $website)
			->set('link', URL::site())
			->set('description', $config->desc)
			->set('content', $content)
			->set('footnote', Alpaca::random_footnote());
			
		$mailer->content($content->render());
		
		return $mailer->send();
	}
	
	/**
	 * Force string
	 * @param string $string
	 * @param string $default
	 * @return string
	 */
	public static function force_string($string, $default = NULL)
	{
		if (is_string($string))
		{
			$string = trim($string);
			if (empty($string) AND strlen($string) == 0)
			{
				$string = $default;
			}
		} 
		else
		{
			$string = $default;
		}
		
		return $string;
	}
	
	/**
	 * General copyright
	 *
	 * @param string $start_year 
	 * @param boolean $by 
	 * @return string
	 */
	public static function copyright($start_year = NULL, $by = FALSE)
	{
		$config = Kohana::config('alpaca');
		$years = (empty($start_year)) ? date('Y') : $start_year.'-'.date('Y');
		$name = HTML::anchor(URL::base(), $config['title']);
		$author = HTML::anchor($config['project']['url'], $config['project']['author']);
		
		$output = '&copy; '.$years.' '.$name;
		if ($by)
		{
			$output .= ' by '.$author.'.';
		}
		elseif (is_array($by))
		{
			$attr = array_key_exists('attr', $by) ? $by['attr'] : NULL;
			$output .= ' by '.HTML::anchor($by['link'], $by['name'], $attr).'.';
		}
		
		return $output;
	}
	
	/**
	 * Random general email footnotes 
	 *
	 * @return string
	 */
	public static function random_footnote()
	{
		$footnotes = array
		(
			'这是一封自动产生的邮件，请勿回复。',
			'此封邮件发送地址只用于邮件提醒，不能够接收邮件，请不要直接回复。',
			'神兽快递值得信赖！',
			'不要迷恋哥 ，哥不止是个传说。',
			'一阵妖风刮落了一封邮件到你的手中。',
			'杯具，河蟹喊我回家吃饭。',
			'本邮件由淫荡小琵琶亲情奉送。',
			'世界需要你们来拯救，不要管我！',
		);
		
		return $footnotes[rand(0, (count($footnotes)-1))];
	}
	
	/**
	 * Make string mixed both english words and chinese words beautiful
	 *
	 * @param string $string 
	 * @param string $fill_before 
	 * @param string $fill_after 
	 * @return string
	 */
	public static function beautify_str($string, $fill_before = TRUE, $fill_after = FALSE)
	{
		if ($fill_before AND preg_match('/^\w/', $string))
		{
			$string = ' '.$string;
		}
		
		if ($fill_after AND preg_match('/\w$/', $string))
		{
			$string = $string.' ';
		}
		
		return $string;
	}

	/**
	 * Check login status else redirect to source page (default: login page)
	 *
	 * @param  $redirect_url
	 * @return boolean
	 */
	public static function logged_in($redirect_url = NULL)
	{
		$logged_in = Auth::instance()->logged_in();
		if ( ! $logged_in)
		{
			$request= Request::current();
			if (empty($redirect_url))
			{
				$redirect_url = Route::url('auth/actions', array(
					'action' => 'login'
				));

				$redirect_url .= URL::query(array(
					'redir' => $request->uri()
				));
			}

			$request->redirect($redirect_url);
		}

		return $logged_in;
	}

	/**
	 * Error Page
	 *
	 * @param  $title
	 * @param  $content
	 * @return Kohana_View
	 */
	public static function error_page(&$title, &$content)
	{
		return Twig::factory('template/errors')
			->bind('title', $title)
			->bind('content', $content);
	}
}


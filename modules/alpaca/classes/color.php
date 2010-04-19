<?php defined('SYSPATH') or die('No direct script access.');
/**
 * General color library
 *
 * @package library
 * @author livid
 */
class Color {

	public static function hex2rgb($hex)
	{
		if (substr($hex, 0, 1) != '#')
		{
			$hex = '#'.$hex;
		}
		
		if ( strlen($hex) == 4 )
		{
			$R = substr($hex, 1, 2);
			$G = substr($hex, 2, 3);
			$B = substr($hex, 3, 4);
			
			$color = '#'.$R.$R.$G.$G.$B.$B;
		}
		elseif (strlen($hex) > 7)
		{
			$hex = substr($hex, 0, 7);
		}
		
		list($R, $G, $B) = sscanf($hex, '#%2x%2x%2x');
		$RGB = array
		(
			'red' 	=> $R,
			'green' => $G,
			'blue' 	=> $B,
		);

		return $RGB;
	}

	public static function rgb2hex($R, $G = -1, $B = -1)
	{
		if (is_array($R) AND sizeof($R) == 3)
		{
			 list($R, $G, $B) = $R;
		}
		else
		{
			$R = intval($R); 
			$G = intval($G);
			$B = intval($B);
		}
	
		$R = dechex($R<0?0:($R>255?255:$R));
		$G = dechex($G<0?0:($G>255?255:$G));
		$B = dechex($B<0?0:($B>255?255:$B));
	
		$hex = (strlen($R) < 2?'0':'').$R;
		$hex .= (strlen($G) < 2?'0':'').$G;
		$hex .= (strlen($B) < 2?'0':'').$B;

		return '#'.$hex;
	}
	
	public static function rand_color($color_start = 0, $color_end = 3) 
	{
		$color = array
		(
			0 => '0', 
			1 => '3', 
			2 => '6', 
			3 => '9', 
			4 => 'C', 
			5 => 'F'
		);
		
		while (($o ='#' . $color[rand($color_start, $color_end)] . $color[rand($color_start, $color_end)] . $color[rand($color_start, $color_end)]) != '#FFF') {
			return $o;
		}
	}
	
	public static function rand_gray($color_start = 1, $color_end = 3) 
	{
		$color = array
		(
			0 => '0', 
			1 => '3',
			2 => '6', 
			3 => '9', 
			4 => 'A', 
			5 => 'D'
		);
		
		$g = $color[rand($color_start, $color_end)];
		while (($o = '#' . $g . $g . $g) != '#DDD') {
			return $o;
		}
	}
	
}


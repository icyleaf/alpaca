<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Group helper
 *
 * @package Alpaca
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Alpaca_Group {
		 
	const GROUP_DEFAULT_IMAGE = 'media/images/group-default.jpg';
	
	// Groups attributes
	private static $_group_id = 0;
	private static $_group_name = NULL;
	
	/**
	 * Get group id either number or string
	 *
	 * @param boolean $render 
	 * @return mixed
	 */
	public static function the_id($render = TRUE)
	{
		$group_id = empty(Alpaca_Group::$_group_id) ? 0 : Alpaca_Group::$_group_id;
		if ($render)
		{
			echo $group_id;
		}
		else 
		{
			return $group_id;
		}
	}
	
	/**
	 * Get group's title with modify suitable
	 *
	 * @param string $before 
	 * @param string $after 
	 * @param boolean $render 
	 * @return mixed
	 */
	public static function the_title($before = '', $after = '', $render = TRUE)
	{
		if (strlen(Alpaca_Group::$_group_name) == 0)
		{
			return NULL;
		}
		
		$group_name = $before . Alpaca_Group::$_group_name . $after;
		if ($render)
		{
			echo $group_name;
		}
		else
		{
			return $group_name;
		}
	}
	
	/**
	 * Get group id either number or string
	 *
	 * @param Model_Group $group 
	 * @return mixed
	 */
	public static function the_uri(Model_Group $group)
	{
		if ($group->loaded())
		{
			return empty($group->uri) ? $group->id : $group->uri;
		}
	}
	
	/**
	 * General group image
	 *
	 * @param Model_Group $group 
	 * @param boolean $attr 
	 * @param boolean $link 
	 * @return string
	 */
	public static function image(Model_Group $group, $attr = FALSE, $link = FALSE)
	{
		$image_url = URL::site('media/alpaca/group/'.$group->id.'.jpg');
		$image_path = ALPPATH . 'media/alpaca/group/'.$group->id.'.jpg';
		$image = (file_exists($image_path)) ? $image_url : Alpaca_Group::GROUP_DEFAULT_IMAGE;
		unset($image_url, $image_path);

		if (is_array($attr))
		{
			$image = HTML::image($image, $attr);
		}
		elseif ($attr)
		{
			$image = HTML::image($image);
		}

		$group_uri = array(
			'id' => Alpaca_Group::the_uri($group)
		);
		if (is_array($link))
		{
			$image = HTML::anchor(Route::get('group')
				->uri($group_uri), $image, $attr);
		}
		elseif ($link)
		{
			$image = HTML::anchor(Route::get('group')
				->uri($group_uri), $image);
		}
		
		return $image;
	}
	
	/**
	 * List all groups
	 *
	 * @param mixed $config 
	 * @return mixed
	 */
	public static function get_list($config = NULL)
	{
		$default = array
		(
			'title'			=> __('Group'),
			'child_of'		=> FALSE,
			'sort' 			=> array(
				'column'		=>'count',
				'direction'		=>'DESC'
				),
			'link_before'	=> '',
			'link_after' 	=> '',
			'render' 		=> FALSE
		);
		
		if (is_array($config))
		{
			$config = array_merge($default, $config);
		}
		else
		{
			$config = $default;
		}
		
		$groups = ORM::factory('group')
			->order_by($config['sort']['column'], $config['sort']['direction'])
			->find_all();
			
		$output = NULL;
		if ($groups->count() > 0)
		{
			$output .= '<div id="list_groups">'.$config['title'].'<ul>';
			foreach ($groups as $group)
			{
				Alpaca_Group::$_group_id = $group->id;
				Alpaca_Group::$_group_name = $group->name;
				
				$link_uri = Route::get('group')->uri(array('id'=>Alpaca_Group::the_uri($group)));
				$link_title = $config['link_before'] . $group->name . $config['link_after'];
					
				if ($config['child_of'])
				{
					if ($group->level == 0)
					{
						$output .= '<li class="group_item group-item-'.$group->id.'">' . HTML::anchor($link_uri, $link_title);
						$children = $group->children
							->order_by($config['sort']['column'], $config['sort']['direction'])
							->find_all();
						if ($children->count() > 0)
						{
							$output .= '<ul id="group_children">';
							foreach ($children as $child)
							{
								$link_uri = Route::get('group')->uri(array(
									'id' => Alpaca_Group::the_uri($child)
								));
								$link_title = $config['link_before'] . $child->name . $config['link_after'];
								$output .= '<li class="group_item group-item-'.$child->id.'">' . 
									HTML::anchor($link_uri, $link_title) . '</ul>';
							}
							$output .= '</ul>';
						}
						$output .= '</li>';
					}		
				}
				else
				{
					if ($group->level == 1)
					{
						$output .= '<li class="group_item group-item-'.$group->id.'">' . 
							HTML::anchor($link_uri, $link_title) . '</li>';
					}
				}
			}
			$output .= '</ul></div>';
		}
		
		Alpaca_Group::_clear();
		
		if ($config['render'])
		{
			echo $output;
		}
		else
		{
			return $output;
		}
	}
	
	/**
	 * Get all topic form a group
	 *
	 * @param int $group_id 
	 * @param mixed $config 
	 * @return mixed
	 */
	public static function get_topics($group_id, $config = NULL)
	{
		$default = array
		(
			'title'			=> __('Latest topics'),
			'count'			=> 10,
			'sort' 			=> array(
				'column'		=>'updated',
				'direction'		=>'DESC'
				),
			'link_before'	=> '',
			'link_after' 	=> '',
			'render' 		=> FALSE
		);
		
		if (is_array($config))
		{
			$config = array_merge($default, $config);
		}
		else
		{
			$config = $default;
		}
		
		$topics = ORM::factory('group', $group_id)
			->topics
			->order_by($config['sort']['column'], $config['sort']['direction'])
			->limit($config['count'])
			->find_all();
			
		$output = NULL;
		if ($topics->count() > 0)
		{
			$output .= '<div id="list_topics" class="widget">'.
				'<h3>'.$config['title'].'</h3>'.
				'<div class="content"><ul>';
			foreach ($topics as $topic) 
			{
				$link_uri = Route::get('topic')->uri(array(
					'group_id' => Alpaca_Group::the_uri($topic->group),
					'id' => $topic->id
				));
				$link_title = $config['link_before'] . $topic->title . $config['link_after'];
				
				$output .= '<li class="topic_item topic-item-'.$topic->id.'">' . 
					HTML::anchor($link_uri, $link_title) . '</li>';
			}
			$output .= '</ul></div></div>';
		}

		if ($config['render'])
		{
			echo $output;
		}
		else
		{
			return $output;
		}
	}
	
	/**
	 * Empty temporary variables
	 */
	private static function _clear()
	{
		Alpaca_Group::$_group_id = 0;
		Alpaca_Group::$_group_name = NULL;
	}
}


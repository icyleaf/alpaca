<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Topic helper
 *
 * @package Alpaca
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Alpaca_Topic {
	/**
	 * Get topic url
	 *
	 * @param mixed $topic
	 * @param Model_Group $group
	 * @return string
	 */
	public static function the_url($topic, Model_Group $group = NULL)
	{
		$group = $group ? $group : $topic->group;
		return Route::url('topic', array(
			'group_id' => Alpaca_Group::the_uri($group),
			'id' => $topic->id
		));
	}
}


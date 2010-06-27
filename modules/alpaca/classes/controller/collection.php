<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Collection Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Collection extends Controller_Alpaca {
	
	/**
	 * Add a collection
	 *
	 * @param int $topic_id 
	 * @return boolean
	 */
	public function action_topic($topic_id)
	{
		$collection = ORM::factory('collection');
		if (Request::$is_ajax)
		{
			// Disable auto render
			$this->auto_render = FALSE;
			
			// the default render message
			$result = 'FALSE';
			if ($user = $this->auth->get_user())
			{
				if ( ! $collection->is_collected($topic_id, $user->id))
				{
					$this->_saved();

					$result = 'CREATED';
				}
				else
				{
					$result = 'EXIST';
				}
			}
			else
			{
				$result = 'NO_AUTH';
			}
			
			echo $result;
		}
		else
		{
			// Check login status else redirect to login page
			Alpaca::logged_in();
			
			$result = 'FALSE';
			$user = $this->auth->get_user();
				
			$collection = ORM::factory('collection');
			if ( ! $collection->is_collected($topic_id, $user->id))
			{
				$this->_saved();

				$result = '创建成功！';
				$this->request->redirect(Alpaca_Topic::url($topic));
			}
			else
			{
				$result = '已经创建';
			}
			
			$this->request->response = $result;
		}
	}

	/**
	 * Added relation between user and topic
	 *
	 * @param  $collection
	 * @param  $user_id
	 * @param  $topic_id
	 * @return void
	 */
	private function _saved($collection, $user_id, $topic_id)
	{
		$collection->user_id = $user_id;
		$collection->topic_id = $topic_id;
		$collection->save();

		// update topic collections count
		$topic = ORM::factory('topic', $topic_id);
		$topic->collections += 1;
		$topic->save();
	}
}


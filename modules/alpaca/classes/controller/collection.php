<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Collection Entry
 *
 * @package controller
 * @author icyleaf
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
		if (Request::$is_ajax)
		{
			// Disable auto render
			$this->auto_render = FALSE;
			
			// the default render message
			$result = 'FALSE';
			if ($this->auth->logged_in())
			{
				$user = $this->auth->get_user();
				
				$collection = ORM::factory('collection');
				$result = $collection->where('user_id', '=', $user->id)
					->and_where('topic_id', '=', $topic_id)
					->find()
					->loaded();
					
				if ( ! $result)
				{
					$collection->user_id = $user->id;
					$collection->topic_id = $topic_id;
					$collection->save();
					
					// update topic collections count
					$topic = ORM::factory('topic', $topic_id);
					$topic->collections += 1;
					$topic->save();

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
			if ( ! $this->auth->logged_in())
			{
				$current_uri = URL::query(array('redir' => $this->request->uri));
				$this->request->redirect(Route::get('login')->uri().$current_uri);
			}
			
			$result = 'FALSE';
			$user = $this->auth->get_user();
				
			$collection = ORM::factory('collection');
			$result = $collection->where('user_id', '=', $user->id)
				->and_where('topic_id', '=', $topic_id)
				->find()
				->loaded();
				
			if ( ! $result)
			{
				$collection->user_id = $user->id;
				$collection->topic_id = $topic_id;
				$collection->save();
				
				// update topic collections count
				$topic = ORM::factory('topic', $topic_id);
				$topic->collections += 1;
				$topic->save();

				$result = '创建成功！';
			}
			else
			{
				$result = '已经创建';
			}
			
			$this->request->response = $result;
		}
	}
}


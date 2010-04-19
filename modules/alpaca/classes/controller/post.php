<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Post Entry
 *
 * @package controller
 * @author icyleaf
 */
class Controller_Post extends Controller_Alpaca {
	
	public function before()
	{
		parent::before();
		
		if ( ! $this->auth->logged_in())
		{
			$current_uri = URL::query(array('redir' => $this->request->uri));
			$this->request->redirect(Route::get('login')->uri().$current_uri);
		}
		
		// add auto resize to textarea
		$this->header->javascript->append_file('media/js/jquery/autoresize.js', '1.04');
		
		$this->header->title->set($this->config->title);
	}
	
	/**
	 * Created a new post
	 */
	public function action_add() 
	{
		$this->auto_render = FALSE;
		if ($_POST AND empty($_POST['email']) AND empty($_POST['website']))
		{	
			// Create the new post
			unset($_POST['email'], $_POST['website']);
			$post = ORM::factory('post')->values($_POST);
			
			if ($post->check())
			{
				$post->save();
				
				// Updated topic last touched time and post count
				$topic = $post->topic;
				$topic->touched = time();
				$topic->count += 1;
				$topic->save();
				
				$this->request->redirect(Route::get('topic')->uri(array('id' => $topic->id)));
			}
			else
			{
				echo Kohana::debug($post->validate()->errors('validate'));
			}
		}
	}
	
	/**
	 * Edit post
	 *
	 * @param int $post_id 
	 * @return void
	 */
	public function action_edit($post_id)
	{
		$post = ORM::factory('post', $post_id);
		if ($_POST AND $post->loaded())
		{
			$post->values($_POST);
			if ($post->check())
			{
				$post->save();
			
				$this->request->redirect(Route::get('topic')->uri(array('id' => $post->topic->id)));
			}
			else
			{
				echo Kohana::debug($post->validate()->errors('validate'));
			}
		}
		
		$this->template->content = View::factory('template/general')
			->bind('title', $title)
			->bind('content', $content);
			
		if ($post->loaded())
		{
			$title = __("Edit Reply");
			
			$auth_user = $this->auth->get_user();
			$has_role = $auth_user->has('roles', ORM::factory('role', array('name' => 'admin')));
			if (($auth_user->id == $post->author->id) OR $has_role)
			{
				$this->template->content = View::factory('post/edit')
					->bind('post', $post);
				
				$group = $post->topic->group;
				//TODO: Change the sidebar
				$sidebar = '<div style="margin-bottom:10px">'.
					html::anchor(Route::get('group')->uri(array('id' => Alpaca_Group::the_uri($group))),
						Alpaca_Group::image($group, TRUE)).'</div>';
				$sidebar .= html::anchor(Route::get('group')->uri(array('id' => Alpaca_Group::the_uri($group))), 
				'返回'.$group->name.'小组');
			
				$this->template->sidebar = $sidebar;
			}
			else
			{
				$content = __('Not enough permission to perform this operation.');
			}
		}
		else
		{
			$title = __('Ooops');
			$content = __('Not found this reply!');
		}
		
		$this->header->title->prepend($title);
	}
	
	/**
	 * Delete post
	 *
	 * @param int $post_id 
	 * @return void
	 */
	public function action_delete($post_id)
	{
		$this->template->content = View::factory('template/general')
			->bind('title', $title)
			->bind('content', $content);
			
		$post = ORM::factory('post', $post_id);
		if ($post->loaded())
		{
			$title = __('Delete Reply');
			
			$auth_user = $this->auth->get_user();
			$has_role = $auth_user->has('roles', ORM::factory('role', array('name' => 'admin')));
			if (($auth_user->id == $post->author->id) OR $has_role)
			{
				// Updated post count
				$topic = $post->topic;
				$topic->count -= 1;
				$topic->save();
				
				// Delete the post
				$post->delete();
				
				$this->request->redirect(Route::get('topic')->uri(array('id' => $post->topic->id)));
			}
			else
			{
				$content = __('Not enough permission to perform this operation.');
			}
				
		}
		else
		{
			$title = __('Ooops');
			$content = __('Not found this reply!');
		}
		
		$this->header->title->prepend($title);
	}
	
}


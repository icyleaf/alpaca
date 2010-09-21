<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Post Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Post extends Controller_Template_Alpaca {
	
	public function before()
	{
		parent::before();
		
		// Check login status else redirect to login page
		Alpaca::logged_in();
		
		// add auto resize to textarea
		$this->head->javascript->append_file('media/js/jquery/autoresize.js', '1.04');
		$this->head->title->set($this->config->title);
	}
	
	/**
	 * Created a new post
	 */
	public function action_create() 
	{
		$this->auto_render = FALSE;
		if ($_POST)
		{	
			// Create the new post
			$post = ORM::factory('post')->values($_POST);
			
			if ($post->check())
			{
				$post->save();
				
				// Updated topic last touched time and post count
				$topic = $post->topic;
				$topic->touched = time();
				$topic->count += 1;
				$topic->save();
				
				$this->request->redirect(Alpaca_Topic::url($topic));
			}
			else
			{
				// TODO: debug code, sub
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
			
				$this->request->redirect(Alpaca_Topic::url($post->topic));
			}
			else
			{
				$errors = $post->validate()->errors('validate');
			}
		}
		
		$this->template->content = Alpaca::error_page($title, $content);
			
		if ($post->loaded())
		{
			$title = __("Edit Reply");
			
			$auth_user = $this->auth->get_user();
			if (($auth_user->id == $post->author->id) OR $auth_user->has_role('admin'))
			{
				$this->template->content = View::factory('post/edit')
					->bind('errors', $errors)
					->bind('post', $post);
				
				$group = $post->topic->group;
				//TODO: Change the sidebar
				$sidebar = '<div style="margin-bottom:10px">'.
					HTML::anchor(Route::url('group', array('id' => Alpaca_Group::uri($group))),
						Alpaca_Group::image($group, TRUE)).
					'</div>'.
					HTML::anchor(Route::url('group', array('id' => Alpaca_Group::uri($group))),
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
		
		$this->head->title->prepend($title);
	}
	
	/**
	 * Delete post
	 *
	 * @param int $post_id 
	 * @return void
	 */
	public function action_delete($post_id)
	{
		$this->template->content = Alpaca::error_page($title, $content);

		$post = ORM::factory('post', $post_id);
		if ($post->loaded())
		{
			$title = __('Delete Reply');
			
			$auth_user = $this->auth->get_user();
			if (($auth_user->id == $post->author->id) OR $auth_user->has_role('admin'))
			{
				// Updated post count
				$topic = $post->topic;
				$topic->count -= 1;
				$topic->save();
				
				// Delete the post
				$post->delete();
				
				$this->request->redirect(Alpaca_Topic::url($post->topic));
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
		
		$this->head->title->prepend($title);
	}
	
}


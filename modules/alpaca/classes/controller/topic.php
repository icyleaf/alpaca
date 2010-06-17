<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Topic Entry
 *
 * @package controller
 * @author icyleaf
 */
class Controller_Topic extends Controller_Alpaca {
	
	public function before()
	{
		parent::before();
		
		// add auto resize to textarea
		$this->header->javascript->append_file('media/js/jquery/autoresize.js', '1.04');
		$this->header->title->set($this->config->title);
	}
	
	/**
	 * View a topic
	 *
	 * @param int $topic_id 
	 */
	public function action_view($topic_id)
	{
		$topic = ORM::factory('topic', $topic_id);
		if ($topic->loaded())
		{
			$title = $topic->title;
			$topic->hits += 1;
			$topic->save();
			
			$pagination = Pagination::factory(array(
				'view'				=> 'pagination/digg',
				'total_items' 		=> $topic->count,
				'items_per_page'	=> $this->config->post['per_page'],
				));
			
			$this->template->content = View::factory('topic/view')
				->bind('topic', $topic)
				->bind('topic_posts', $topic_posts)
				->bind('write_post', $write_post);
				
			$topic_posts = View::factory('post/list')
				->set('post_count', $topic->posts->find_all()->count())
				->bind('topic', $topic)
				->bind('posts', $posts)
				->bind('pagination', $pagination);
				
			$posts = $topic->posts
				->where('reply_id', '=', 0)
				->limit($pagination->items_per_page)
				->offset($pagination->offset)
				->find_all();
			
			$write_post = View::factory('post/write')
				->bind('topic', $topic);
	
			$this->template->sidebar = View::factory('sidebar/topic')
                                ->bind('title', $title)
				->set('topic', $topic);
		}
		else
		{
			$this->template->content = View::factory('template/general')
				->bind('title', $title)
				->bind('content', $content);
				
			$title = __('Ooops');
			$content = __('Not found this topic!');
		}
		
		$this->header->title->prepend($title);
	}
	
	/**
	 * Create a new topic
	 *
	 * @param int $group_id 
	 * @return void
	 */
	public function action_add($group_id) 
	{
		if ( ! $this->auth->logged_in())
		{
			$current_uri = URL::query(array('redir' => $this->request->uri));
			$this->request->redirect(Route::get('login')->uri().$current_uri);
		}
		
		if (is_numeric($group_id))
		{
			$group = ORM::factory('group', $group_id);
		}
		else
		{
			$group = ORM::factory('group')->where('uri', '=', $group_id)->find();
		}
		
		$this->template->content = View::factory('template/general')
			->bind('title', $title)
			->bind('content', $content);
					
		$title = __('Create new topic');
		if ($group->loaded())
		{
			if ($group->level == 1)
			{
				$this->template->content = View::factory('topic/add')
					->bind('group', $group)
					->bind('errors', $errors);
					
				if ($_POST)
				{
					// Check the topic if it exist in database
					if ( ! $this->config->topic_repeat)
					{
						$topic = ORM::factory('topic')
							->where('user_id', '=', $_POST['user_id'])
							->and_where('content', '=', trim($_POST['content']))
							->find();
							
						if ($topic->loaded())
						{
							$this->request->redirect(Route::get('topic')->uri(array('id' => $topic->id)));
						}
					}

					// Create the new topic
					$topic = ORM::factory('topic')->values($_POST);
					if ($topic->check())
					{
						$topic->group_id = $group->id;
						$topic->save();
						
						// Updated group's topic count
						$group->count += 1;
						$group->save();
						
						$this->request->redirect(Route::get('topic')->uri(array('id' => $topic->id)));
					}
					else
					{
						$errors = $topic->validate()->errors('validate');
					}
				}
				// TODO: Change the sidebar
				$sidebar = '<div style="margin-bottom:10px">'.
					HTML::anchor(Route::get('group')->uri(array('id' => Alpaca_Group::the_uri($group))),
						Alpaca_Group::image($group, TRUE)).'</div>';
				$sidebar .= HTML::anchor(Route::get('group')->uri(array('id' => Alpaca_Group::the_uri($group))),
					'返回'.$group->name.'小组');
				
				$this->template->sidebar = $sidebar;
			}
			else
			{
				$content = 'This is CATEGORY which could not create topics!';
			}
		}
		else
		{
			$content = __('Not found this group!');
		}
		
		$this->header->title->prepend($title);
	}
	
	/**
	 * Edit topic
	 *
	 * @param string $topic_id 
	 * @return void
	 */
	public function action_edit($topic_id)
	{
		if ( ! $this->auth->logged_in())
		{
			$current_uri = URL::query(array('redir' => $this->request->uri));
			$this->request->redirect(Route::get('login')->uri().$current_uri);
		}
		
		$topic = ORM::factory('topic', $topic_id);
		if ($_POST AND $topic->loaded())
		{
			$topic->values($_POST);
			if ($topic->check())
			{
				// Upate
				$topic->save();

				$this->request->redirect(Route::get('topic')->uri(array('id' => $topic_id)));
			}
			else
			{
				echo Kohana::debug($topic->validate()->errors('validate'));
			}
		}

		$this->template->content = View::factory('template/general')
			->bind('title', $title)
			->bind('content', $content);
			
		$title = __('Ooops');		
		if ($topic->loaded())
		{
			$title = __('Edit ":title" topic', array(':title' => $topic->title));
			
			$auth_user = $this->auth->get_user();
			$has_role = $auth_user->has('roles', ORM::factory('role', array('name' => 'admin')));
			if (($auth_user->id == $topic->author->id) OR $has_role)
			{
				$this->template->content = View::factory('topic/edit')
					->bind('topic', $topic);
				
				$group = $topic->group;
				// TODO: change the sidebar
				$sidebar = '<div style="margin-bottom:10px">'.
				HTML::anchor(Route::get('group')->uri(array('id' => Alpaca_Group::the_uri($group))),
					Alpaca_Group::image($group, TRUE)).'</div>';
				$sidebar .= HTML::anchor(Route::get('group')->uri(array('id' => Alpaca_Group::the_uri($group))),
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
			$content = __('Not found this topic!');
		}
		
		$this->header->title->prepend($title);
	}
	
	/**
	 * Delete topic
	 *
	 * @param int $topic_id 
	 * @return void
	 */
	public function action_delete($topic_id)
	{
		if ( ! $this->auth->logged_in())
		{
			$current_uri = URL::query(array('redir' => $this->request->uri));
			$this->request->redirect(Route::get('login')->uri().$current_uri);
		}
		
		$this->template->content = View::factory('template/general')
			->bind('title', $title)
			->bind('content', $content);
			
		$topic = ORM::factory('topic', $topic_id);
		if ($topic->loaded())
		{
			$title = __('Delete ":title" topic', array(':title' => $topic->title));
			$auth_user = $this->auth->get_user();
			$has_role = $auth_user->has('roles', ORM::factory('role', array('name' => 'admin')));
			if (($auth_user->id == $topic->author->id) OR $has_role)
			{
				$topic->posts->delete_all();
				
				// Updated group's topic count
				$group = $topic->group;
				$group->count -= 1;
				$group->save();
				
				$topic->delete();
				
				$this->request->redirect(Route::get('group')->uri(array('id' => $group->id)));
			}
			else
			{
				$content = __('Not enough permission to perform this operation.');
			}
		}
		else
		{
			$title = __('Ooops');	
			$content = __('Not found this topic!');
		}
		
		$this->header->title->prepend($title);
	}
	
	/**
	 * Move topic from current group to other group
	 *
	 * @param int $topic_id 
	 * @param int $group_id 
	 * @return void
	 */
	public function action_move($topic_id, $group_id = NULL)
	{
		if ( ! $this->auth->logged_in())
		{
			$current_uri = URL::query(array('redir' => $this->request->uri));
			$this->request->redirect(Route::get('login')->uri().$current_uri);
		}
		
		$this->template->content = View::factory('template/general')
			->bind('title', $title)
			->bind('content', $content);
			
		$topic = ORM::factory('topic', $topic_id);
		if ($topic->loaded())
		{
			if ( ! empty($group_id) AND is_numeric($group_id))
			{
				$topic->group_id = $group_id;
				$topic->save();
				$this->request->redirect(Route::get('topic')->uri(array('id' => $topic_id)));
			}
			else
			{
				$title = __('Move ":title" topic', array(':title' => $topic->title));
		
				$auth_user = $this->auth->get_user();
				$has_role = $auth_user->has('roles', ORM::factory('role', array('name' => 'admin')));
				if (($auth_user->id == $topic->author->id) OR $has_role)
				{
					
					$this->template->content = View::factory('topic/move')
						->bind('topic', $topic);
				}
				else
				{
					$content = __('Not enough permission to perform this operation.');
				}
			}
		}
		else
		{
			$title = __('Ooops');
			$content = __('Not found this topic!');
		}
		
		$this->header->title->prepend($title);
	}
	
	public function action_collectors($topic_id)
	{
		$topic = ORM::factory('topic', $topic_id);
		if ($topic->loaded())
		{
			$title = __('Who collected ":title" topic', array(':title' => $topic->title));
			$this->template->content = View::factory('user/list')
				->bind('title', $title)
				->bind('collections', $collections);
				
			$this->template->sidebar = View::factory('sidebar/topic_detail')
				->bind('topic', $topic);
				
			$collections = ORM::factory('collection')
				->where('topic_id', '=', $topic_id)
				->find_all();
		}
		else
		{
			$this->template->content = View::factory('template/general')
				->bind('title', $title)
				->bind('content', $content);
				
			$title = __('Ooops');
			$content = __('Not found this topic!');
		}
		
		$this->header->title->prepend($title);
	}
	
}


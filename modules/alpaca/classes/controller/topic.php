<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Topic Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
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
	public function action_view($group_id = NULL, $topic_id)
	{
		$topic = ORM::factory('topic', $topic_id);
		if ($topic->loaded())
		{
			if (preg_match('/^topic\/(\d+)/', $this->request->uri))
			{
				// redirect to page with group uri
				$this->request->redirect(Route::url('topic', array(
					'group_id' => Alpaca_Group::the_uri($topic->group),
					'id' => $topic->id
				)), 301);
			}

			$title = $topic->title;
			// TODO: ONLY store once for per user
			$topic->hits += 1;
			$topic->save();

			$pagination = Pagination::factory(array(
				'view'				=> 'pagination/digg',
				'total_items' 		=> $topic->count,
				'items_per_page'	=> $this->config->post['per_page'],
				));

			$post_count = $topic->posts->find_all()->count();
			$posts = $topic->posts
				->where('reply_id', '=', 0)
				->limit($pagination->items_per_page)
				->offset($pagination->offset)
				->find_all();

			$auth_user = $this->auth->get_user();
			$author = $topic->author;
			if ($auth_user)
			{
				$actions = array();
				$has_admin_role = $auth_user->has_role('admin');
				if (($auth_user->id == $author->id) OR $has_admin_role)
				{
					// Topic Edit Anchor
					$actions[] = HTML::anchor('topic/edit/' . $topic->id, __('Edit'), array(
						'class' => 'edit',
						'title' => __('Edit Topic'),
					));
					// Topic Delete Anchor
					$actions[] = HTML::anchor('topic/delete/' . $topic->id, __('Delete'), array(
						'class' => 'delete',
						'title' => __('Delete this topic include all the replies'),
						'rel' => __('[NOT UNDO] Do you really want to delete this topic include all the replies?'),
					));
				}
				// ONLY Admin can MOVE topic
				if ($has_admin_role)
				{
					// Topic Move Anchor
					$actions[] = HTML::anchor('topic/move/' . $topic->id, __('Move'), array(
						'title' => __('Move to other group')
					));
				}
			}
			
			$user_avatar = Gravatar::instance($author->email, array(
				'default' => URL::site('media/images/user-default.jpg')
			));

			$author_link = HTML::anchor(Route::url('user', array(
					'id' => Alpaca_User::the_uri($author)
				)), $author->nickname);
			$details = array(
				'id'			=> $topic->id,
				'title'		=> $topic->title,
				'user_avatar'	=> HTML::image($user_avatar),
				'author_link'	=> $author_link,
				'content'		=> Alpaca::format_html($topic->content),
				'created'		=> date($this->config->date_format, $topic->created),
			);
			$details = (object) $details;

			$this->template->content = View::factory('topic/view')
				->bind('topic', $details)
				->bind('topic_actions', $actions)
				->bind('post_count', $post_count)
				->bind('topic_posts', $topic_posts)
				->bind('write_post', $write_post);

			$topic_posts = View::factory('post/list')
				->bind('post_count', $post_count)
				->bind('topic', $topic)
				->bind('posts', $posts)
				->bind('pagination', $pagination);

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
			$this->request->redirect(Route::url('login').$current_uri);
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
			$author = $this->auth->get_user();
			if ($group->level == 1)
			{
				$this->template->content = View::factory('topic/add')
					->set('author', $author)
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
							$this->request->redirect(Route::url('topic', array(
								'group_id' => Alpaca_Group::the_uri($topic->group),
								'id' => $topic->id
							)));
						}
					}

					$_POST['group_id'] = $group->id;
					$_POST['user_id'] = $author->id;
					// Create the new topic
					$topic = ORM::factory('topic')->values($_POST);
					if ($topic->check())
					{
						$topic->group_id = $group->id;
						$topic->save();

						// Updated group's topic count
						$group->count += 1;
						$group->save();

						$this->request->redirect(Route::url('topic', array(
							'group_id' => Alpaca_Group::the_uri($topic->group),
							'id' => $topic->id
						)));
					}
					else
					{
						$errors = $topic->validate()->errors('validate');
					}
				}
				// TODO: Change the sidebar
				$sidebar = '<div style="margin-bottom:10px">'.
					HTML::anchor(Route::url('group', array('id' => Alpaca_Group::the_uri($group))),
						Alpaca_Group::image($group, TRUE)).'</div>'.
					HTML::anchor(Route::url('group', array('id' => Alpaca_Group::the_uri($group))),
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
			$this->request->redirect(Route::url('login').$current_uri);
		}
		
		$topic = ORM::factory('topic', $topic_id);
		if ($_POST AND $topic->loaded())
		{
			$topic->values($_POST);
			if ($topic->check())
			{
				// Upate
				$topic->save();

				$this->request->redirect(Route::url('topic', array(
					'group_id' => Alpaca_Group::the_uri($topic->group),
					'id' => $topic->id
				)));
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
					HTML::anchor(Route::url('group', array('id' => Alpaca_Group::the_uri($group))),
						Alpaca_Group::image($group, TRUE)).'</div>'.
					HTML::anchor(Route::url('group', array('id' => Alpaca_Group::the_uri($group))),
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
			$this->request->redirect(Route::url('login').$current_uri);
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
				
				$this->request->redirect(Route::url('group', array('id' => $group->id)));
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
			$this->request->redirect(Route::url('login').$current_uri);
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
				$this->request->redirect(Route::url('topic', array(
					'group_id' => Alpaca_Group::the_uri($topic->group),
					'id' => $topic->id
				)));
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


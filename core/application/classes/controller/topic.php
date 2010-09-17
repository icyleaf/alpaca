<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Topic Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Topic extends Controller_Template_Alpaca {
	
	public function before()
	{
		parent::before();
		
		// add auto resize to textarea
		$this->head->javascript->append_file('media/js/jquery/autoresize.js', '1.04');
		$this->head->title->set($this->config->title);
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
				$this->request->redirect(Alpaca_Topic::url($topic), 301);
			}

			$title = $topic->title;
			if (!Request::user_agent('robot'))
			{
				// TODO: ONLY store once for per user
				// fliter robots/spider to increase hits count
				$topic->hits += 1;
				$topic->save();
			}

			// Pagiation
			$pagination = Pagination::factory(array(
				'view'				=> 'pagination/digg',
				'total_items' 		=> $topic->count,
				'items_per_page'	=> $this->config->post['per_page'],
				));

			/** Topic **/
			$auth_user = $this->auth->get_user();
			$author = $topic->author;
			if ($auth_user)
			{
				$topic_actions = array();
				$has_admin_role = $auth_user->has_role('admin');
				if (($auth_user->id == $author->id) OR $has_admin_role)
				{
					// Topic Edit Anchor
					$topic_actions[] = HTML::anchor('topic/edit/' . $topic->id, __('Edit'), array(
						'class' => 'edit',
						'title' => __('Edit Topic'),
					));
					// Topic Delete Anchor
					$topic_actions[] = HTML::anchor('topic/delete/' . $topic->id, __('Delete'), array(
						'class' => 'delete',
						'title' => __('Delete this topic include all the replies'),
						'rel' => __('[NOT UNDO] Do you really want to delete this topic include all the replies?'),
					));
				}
				// ONLY Admin can MOVE topic
				if ($has_admin_role)
				{
					// Topic Move Anchor
					$topic_actions[] = HTML::anchor('topic/move/' . $topic->id, __('Move'), array(
						'title' => __('Move to other group')
					));
				}
			}

			$topic_details = $topic->topic_detail_array($topic);

			/** Post **/
			$posts = $topic->posts->get_posts(FALSE, $pagination->items_per_page, $pagination->offset);
			$post_details = $topic->posts->post_list_array($topic, $posts, $auth_user);
			$all_post_count = $topic->posts->find_all()->count();

			$group_link = Route::url('group', array('id' => $topic->group));
			$collection_link = Route::url('topic/collectors', array('id' => $topic->id));
			$collect_topic_url = URl::site('collection/topic/'.$topic->id);
			$redirect = $this->request->uri;

			$topic_posts = Twig::factory('post/list')
				->bind('post_count', $all_post_count)
				->bind('topic', $topic)
				->bind('posts', $post_details)
				->bind('pagination', $pagination);

			$write_post = Twig::factory('post/write')
				->set('group_link', $group_link)
				->bind('redir', $redirect)
				->bind('topic', $topic);

			$this->template->content = Twig::factory('topic/view')
				->bind('topic', $topic_details)
				->bind('topic_actions', $topic_actions)
				->set('collect_topic_url', $collect_topic_url)
				->bind('post_count', $all_post_count)
				->bind('topic_posts', $topic_posts)
				->bind('write_post', $write_post);

			$recent_topic_list = Alpaca_Group::get_topics($topic->group->id);
			$this->template->sidebar = Twig::factory('sidebar/topic')
				->bind('title', $title)
				->set('topic', $topic)
				->set('recent_topic_list', $recent_topic_list)
				->set('group_link', $group_link)
				->set('collection_link', $collection_link);
		}
		else
		{
			$title = __('Ooops');
			$content = __('Not found this topic!');
			
			$this->template->content = Alpaca::error_page($title, $content);
		}
		
		$this->head->title->prepend($title);
	}
	
	/**
	 * Create a new topic
	 *
	 * @param int $group_id 
	 * @return void
	 */
	public function action_add($group_id) 
	{
		// Check login status else redirect to login page
		Alpaca::logged_in();
		
		if (is_numeric($group_id))
		{
			$group = ORM::factory('group', $group_id);
		}
		else
		{
			$group = ORM::factory('group')->where('uri', '=', $group_id)->find();
		}
		
		$this->template->content = Alpaca::error_page($title, $content);
					
		$title = __('Start a new topic');
		if ($group->loaded())
		{
			$author = $this->auth->get_user();
			if ($group->level == 1)
			{
				if ($_POST)
				{
					if ( ! $this->config->topic_repeat)
					{
						// Check the topic if it exist in database
						$topic = ORM::factory('topic')->find_topic(array(
							'user_id'	=> $author->id,
							'content'	=> Arr::get($_POST, 'content'),
						));

						if ($topic->loaded())
						{
							$this->request->redirect(Alpaca_Topic::url($topic));
						}
					}

					// Create the new topic
					$_POST['group_id'] = $group->id;
					$_POST['user_id'] = $author->id;
					$topic = ORM::factory('topic')->values($_POST);
					if ($topic->check())
					{
						$topic->save();

						// Updated group's topic count
						$group->count += 1;
						$group->save();

						$this->request->redirect(Alpaca_Topic::url($topic));
					}
					else
					{
						$errors = $topic->validate()->errors('validate');
						echo Kohana::debug($errors);
					}
				}

				$this->template->content = View::factory('topic/add_edit')
					->set('title', $title)
					->set('author', $author)
					->set('topic_title', '')
					->set('topic_content', '')
					->set('submit', __('Post it'))
					->bind('group', $group)
					->bind('errors', $errors);

				// TODO: Change the sidebar
				$sidebar = '<div style="margin-bottom:10px">'.
					HTML::anchor(Route::url('group', array('id' => Alpaca_Group::uri($group))),
						Alpaca_Group::image($group, TRUE)).'</div>'.
					HTML::anchor(Route::url('group', array('id' => Alpaca_Group::uri($group))),
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
		
		$this->head->title->prepend($title);
	}
	
	/**
	 * Edit topic
	 *
	 * @param string $topic_id 
	 * @return void
	 */
	public function action_edit($topic_id)
	{
		// Check login status else redirect to login page 
		Alpaca::logged_in();
		
		$topic = ORM::factory('topic', $topic_id);
		if ($_POST AND $topic->loaded())
		{
			$topic->values($_POST);
			if ($topic->check())
			{
				// Upate
				$topic->save();

				$this->request->redirect(Alpaca_Topic::url($topic));
			}
			else
			{
				$errors = $topic->validate()->errors('validate');
			}
		}

		$this->template->content = Alpaca::error_page($title, $content);
			
		$title = __('Ooops');
		if ($topic->loaded())
		{
			$title = __('Edit ":title" topic', array(':title' => $topic->title));
			
			$auth_user = $this->auth->get_user();
			if (($auth_user->id == $topic->author->id) OR $auth_user->has_role('admin'))
			{
				$this->template->content = View::factory('topic/add_edit')
					->set('title', $title)
					->set('author', $topic->author)
					->set('topic_title', $topic->title)
					->set('topic_content', $topic->content)
					->set('submit', __('Update'))
					->bind('group', $group)
					->bind('errors', $errors);
					
				$group = $topic->group;
				// TODO: change the sidebar
				$sidebar = '<div style="margin-bottom:10px">'.
					HTML::anchor(Route::url('group', array('id' => Alpaca_Group::uri($group))),
						Alpaca_Group::image($group, TRUE)).'</div>'.
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
			$content = __('Not found this topic!');
		}
		
		$this->head->title->prepend($title);
	}
	
	/**
	 * Delete topic
	 *
	 * @param int $topic_id 
	 * @return void
	 */
	public function action_delete($topic_id)
	{
		// Check login status else redirect to login page
		Alpaca::logged_in();
		
		$this->template->content = Alpaca::error_page($title, $content);
			
		$topic = ORM::factory('topic', $topic_id);
		if ($topic->loaded())
		{
			$title = __('Delete ":title" topic', array(':title' => $topic->title));
			$auth_user = $this->auth->get_user();
			if (($auth_user->id == $topic->author->id) OR $auth_user->has_role('admin'))
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
		
		$this->head->title->prepend($title);
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
		// Check login status else redirect to login page
		Alpaca::logged_in();
		
		$this->template->content = Alpaca::error_page($title, $content);
		
		$topic = ORM::factory('topic', $topic_id);
		if ($topic->loaded())
		{
			if ( ! empty($group_id) AND is_numeric($group_id))
			{
				$topic->group_id = $group_id;
				$topic->save();
				$this->request->redirect(Alpaca_Topic::url($topic));
			}
			else
			{
				$title = __('Move ":title" topic', array(':title' => $topic->title));
		
				$auth_user = $this->auth->get_user();
				if (($auth_user->id == $topic->author->id) OR $auth_user->has_role('admin'))
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
		
		$this->head->title->prepend($title);
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
			$this->template->content = Alpaca::error_page($title, $content);
			
			$title = __('Ooops');
			$content = __('Not found this topic!');
		}
		
		$this->head->title->prepend($title);
	}
	
}


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
			$author = $topic->author;
			if ($auth_user = $this->auth->get_user())
			{
				$has_admin_role = $auth_user->has_role('admin');

				$topic_actions = array();
				if (($auth_user->id == $author->id) OR $has_admin_role)
				{
					$topic_actions = array(
						'edit-topic'    => array(
							'link'  => 'topic/edit/' . $topic->id,
							'title' => __('Edit Topic'),
							'attr'  => array(
								'class' => 'edit',
							),
						),
						'delete-topic'    => array(
							'link'  => 'topic/delete/' . $topic->id,
							'title' => __('Delete Topic'),
							'attr'  => array(
								'class' => 'delete',
								'rel'   => __('[NOT UNDO] Do you really want to delete this topic include all the replies?'),
							),
						),
					);
				}
				// ONLY Admin can MOVE topic
				if ($has_admin_role)
				{
					$topic_actions['move-topic'] = array(
						'link'  => 'topic/move/' . $topic->id,
						'title' => __('Move Topic'),
					);
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

		$title = __('Start a new topic');
		$this->template->content = Alpaca::error_page($title, $content);

		$group = ORM::factory('group')->get_group($group_id);
		if ($group->loaded())
		{
			$author = $this->auth->get_user();
			if ($group->level == 1)
			{
				$topic = ORM::factory('topic');
				if ($_POST)
				{
					// redirect to the topic if topic repeat
					$this->_check_topic_repeat($topic, TRUE);

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
					}
				}

				$author_avatar = Alpaca_User::avatar($author, NULL, TRUE, TRUE);
				$author = $author->as_array();
				$author['avatar'] = $author_avatar;
				$submit_text = __('Post it!');
				$group_link = Route::url('group', array('id' => $group));

				$this->template->content = Twig::factory('topic/create')
					->set('title', $title)
					->set('author', $author)
					->set('topic', $_POST)
					->set('submit_text', $submit_text)
					->set('group_link', $group_link)
					->bind('errors', $errors);

				$this->template->sidebar = Twig::factory('sidebar/return_group')
					->set('group', $group)
					->set('group_link', $group_link);
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
				$group_link = Route::url('group', array('id' => $topic->group));
				$submit_text = __('Update');
				$this->template->content = Twig::factory('topic/create')
					->set('title', $title)
					->set('topic', $topic)
					->set('group_link', $group_link)
					->set('submit_text', $submit_text)
					->bind('errors', $errors);

				$this->template->sidebar = Twig::factory('sidebar/return_group')
					->set('group', $topic->group)
					->set('group_link', $group_link);
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

			$collectors = ORM::factory('collection')->get_collectors($topic->id);
			$this->template->content = Twig::factory('user/list')
				->set('title', $title)
				->set('collectors', $collectors);

			$group_link = Route::url('group', array('id' => $topic->group));
			$this->template->sidebar = Twig::factory('sidebar/return_group')
				->set('group', $topic->group)
				->set('group_link', $group_link);
		}
		else
		{
			$title = __('Ooops');
			$content = __('Not found this topic!');
			$this->template->content = Alpaca::error_page($title, $content);
		}

		$this->head->title->prepend($title);
	}

	private function _check_topic_repeat($topic, $redirect = FALSE)
	{
		if ( ! $this->config->topic_repeat)
		{
			// Check the topic if it exist in database
			$author = $this->auth->get_user();
			$content = Arr::get($_POST, 'content');
			if ($topic->topic_repeat($author->id, $content))
			{
				$this->request->redirect(Alpaca_Topic::url($topic));
			}
		}
	}
}


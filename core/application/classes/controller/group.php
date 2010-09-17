<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Group Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Group extends Controller_Template_Alpaca {
	
	public function before()
	{
		parent::before();
		
		// add auto resize to textarea
		$this->head->title->set($this->config->title);
	}
	
	/**
	 * View group's topics
	 *
	 * @param mixed $group_id 
	 * @return void
	 */
	public function action_view($group_id)
	{
		if (is_numeric($group_id))
		{
			$group = ORM::factory('group', $group_id);
		}
		else
		{
			$group = ORM::factory('group')
				->where('uri', '=', $group_id)
				->find();
		}
		
		if ($group->loaded())
		{
			$title = Alpaca::beautify_str($group->name, FALSE, TRUE);
			if ($group->level == 0)
			{
				// Categories
				$this->_list_category_topics($group);
			}
			else
			{
				// Groups
				$this->_list_group_topics($group);
			}
		}
		else
		{
			$this->template->content = Alpaca::error_page($title, $content);

			$title = __('Ooops');
			$content = __('Not found this group!');
		}

		$this->head->title->prepend($title);
	}
	
	/**
	 * Create a new group
	 */
	public function action_create() 
	{
		// Check login status else redirect to login page
		Alpaca::logged_in();
		
		$title = __('Create Category/Group');
		$this->head->title->prepend($title);
		$this->template->content = View::factory('group/create')
			->bind('title', $title)
			->set('groups', ORM::factory('group')->where('level', '=', 0)->find_all())
			->bind('errors', $errors);

		$auth_user = $this->auth->get_user();
		if ($auth_user->has_role('admin'))
		{
			if ($_POST)
			{
				if ($_POST['level'] != '0')
				{
					$_POST['parent_id'] = $_POST['level'];
					$_POST['level'] = 1;
				}
				
				$group = ORM::factory('group')->values($_POST);
				
				if ($group->check())
				{
					$group->save();
					
					echo 'Save successful!';
				}
				else
				{
					$errors =$group->validate()->errors('validate');
				}
			}
		}
		else
		{
			$this->template->content = Alpaca::error_page($title, $content);
			
			$content = __('Not enough permission to perform this operation.');
		}
	}
	
	/**
	 * Edit group
	 *
	 * @param mixed $group_id 
	 * @return void
	 */
	public function action_edit($group_id)
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
		
		if ($group->loaded())
		{
			$auth_user = $this->auth->get_user();
			if ($auth_user->has_role('admin'))
			{
				$this->template->content = View::factory('group/edit')
					->bind('title', $title)
					->bind('group', $group)
					->bind('errors', $errors);
				
				$title = __('Edit ":group" Category/Group', array(':group' => $group->name));
				if ($_POST)
				{
					$group->values($_POST);
					if ($group->check())
					{
						$group->save();
		
						$this->request->redirect(Route::url('group', array('id' => Alpaca_Group::uri($group))));
					}
					else
					{
						$errors = $group->validate()->errors('validate');
					}
				}
			}
			else
			{
				$this->template->content = Alpaca::error_page($title, $content);
			 	
				$content = __('Not enough permission to perform this operation.');
			}
		}
		else	
		{
			$this->template->content = Alpaca::error_page($title, $content);

			$title = __('Ooops');
			$content = __('Not found this group!');
		}
	}
	
	/**
	 * Delete group
	 *
	 * @param mixed $group_id 
	 * @return void
	 */
	public function action_delete($group_id)
	{
		// Check login status else redirect to login page
		Alpaca::logged_in();
		
		$group = ORM::factory('group', $group_id);
		if ($group->loaded())
		{
			$auth_user = $this->auth->get_user();
			if ($auth_user->has_role('admin'))
			{
				if ($group->level == 1)
				{
					$topics = $group->topics->find_all();
					if ($topics->count() > 0)
					{
						foreach ($topics as $topic)
						{
							$topic->posts->find_all()->delete_all();
						}
						
						$topics->delete_all();
					}
					
					$parent = $group->parent;
					$parent->count -= 1;
					$parent->save();
				}
				else
				{
					$children = $group->children->find_all();
					if ($children->count() > 0)
					{
						foreach ($children as $child)
						{
							$topics = $child->topics->find_all();
							if ($topics->count() > 0)
							{
								foreach ($topics as $topic)
								{
									$topic->posts->find_all()->delete_all();
								}
								
								$topics->delete_all();
							}
						}
						
						$children->delete_all();
					}
				}
	
				// Delete the post
				$group->delete();
			
				$this->request->redirect(URL::base(FALSE));
			}
			else
			{
				
			}
		}
	}

	private function _list_group_topics($group)
	{
		$title = __('Group');

		// Pagination
		$pagination = Pagination::factory(array(
			'view'				=> 'pagination/digg',
			'total_items' 		=> $group->count,
			'items_per_page'	=> $this->config->topic['per_page'],
			));

		$topics = $group->topics
			->limit($pagination->items_per_page)
			->offset($pagination->offset)
			->order_by('sticky', 'DESC')
			->order_by('touched', 'DESC')
			->find_all();
		$topics_array = $group->topics->topics_list_array($topics);
		$new_topic_link = Route::url('topic/add', array('id' => Alpaca_Group::uri($group)));

		$list_topics = Twig::factory('topic/list')
			->set('post_new_topic', $new_topic_link)
			->bind('group', $group)
			->bind('topics', $topics_array)
			->bind('pagination', $pagination);

		$this->template->content = Twig::factory('group/list')
			->set('group', $group)
			->bind('list_topics', $list_topics);

		$group_topic_total = $group->topics->cached(60)->find_all()->count();
		$this->template->sidebar = Twig::factory('sidebar/group')
			->set('group', $group)
			->set('topic_total', $group_topic_total);
	}

	private function _list_category_topics($group)
	{
		$title = __('Category');
		$this->template->content = View::factory('topic/list')
			->bind('group', $group)
			->bind('topics', $topics)
			->bind('pagination', $pagination);

		$children = $group->children->find_all();
		$children_count = $children->count();
		$children = $children->as_array();
		$topics = ORM::factory('topic');
		for ($i = 0; $i < $children_count; $i++)
		{
			if ($i == 0)
			{
				$topics->where('group_id', '=', $children[$i]->id);
			}
			else
			{
				$topics->or_where('group_id', '=', $children[$i]->id);
			}
		}

		// Pagination
		$pagination = Pagination::factory(array(
			'view'				=> 'pagination/digg',
			'total_items' 		=> $topics->find_all()->count(),
			'items_per_page'	=> $this->config->topic['per_page'],
			));

		$topics = $topics
			->limit($pagination->items_per_page)
			->offset($pagination->offset)
			->order_by('sticky', 'DESC')
			->order_by('touched', 'DESC')
			->find_all();
	}

}


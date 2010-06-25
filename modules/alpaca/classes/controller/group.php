<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Group Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Group extends Controller_Alpaca {
	
	public function before()
	{
		parent::before();
		
		// add auto resize to textarea
		$this->header->title->set($this->config->title);
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
		
		if ( ! $group->loaded())
		{
			$this->request->status = 404;
			$this->request->redirect('404');
		}
		
		// Pagination
		$pagination = Pagination::factory();
		$per_page = $this->config->topic['per_page'];
		
		$title = Alpaca::beautify_str($group->name, FALSE, TRUE);
		if ($group->level == 0)
		{
			// Categories
			$title .= __('Category');
			$this->template->content = View::factory('topic/list')
				->bind('group', $group)
				->bind('topics', $topics)
				->bind('pagination', $pagination);

			$children = $group->children->find_all();
			$children_count = $children->count();
			$children = $children->as_array();
			$topics = ORM::factory('topic');
			for ($i = 0;$i < $children_count;$i++)
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
			
			$pagination->setup(array(
				'view'				=> 'pagination/digg',
				'total_items' 		=> $topics->find_all()->count(),
				'items_per_page'	=> $per_page,
				));
				
			$topics = $topics
				->limit($pagination->items_per_page)
				->offset($pagination->offset)
				->order_by('sticky', 'DESC')
				->order_by('touched', 'DESC')
				->find_all();
		}
		else
		{
			// Groups
			$title .= __('Group');
			$this->template->content = View::factory('group/list/single')
				->bind('group', $group)
				->bind('list_topics', $list_topics);
				
			$list_topics = View::factory('topic/list')
				->bind('group', $group)
				->bind('topics', $topics)
				->set('hide_group', TRUE)
				->bind('pagination', $pagination);
				
			$pagination->setup(array(
				'view'				=> 'pagination/digg',
				'total_items' 		=> $group->count,
				'items_per_page'	=> $per_page,
				));
				
			$topics = $group->topics
				->limit($pagination->items_per_page)
				->offset($pagination->offset)
				->order_by('sticky', 'DESC')
				->order_by('touched', 'DESC')
				->find_all();
				
			$this->template->sidebar = View::factory('sidebar/group')
				->bind('group', $group);
		}
		
		$this->header->title->prepend($title);
	}
	
	/**
	 * Create a new group
	 */
	public function action_add() 
	{
		if ( ! $this->auth->logged_in())
		{
			$current_uri = URL::query(array('redir' => $this->request->uri));
			$this->request->redirect(Route::get('login')->uri().$current_uri);
		}
		
		$title = __('Create Category/Group');
		$this->header->title->prepend($title);
		$this->template->content = View::factory('group/add')
			->bind('title', $title)
			->set('groups', ORM::factory('group')->where('level', '=', 0)->find_all())
			->bind('errors', $errors);

		$auth_user = $this->auth->get_user();
		if ($auth_user->has('roles', ORM::factory('role', array('name'=>'admin'))))
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
			$this->template->content = View::factory('template/general')
						->bind('title', $title)
						->bind('content', $content);
			
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
		
		if ( ! $group->loaded())
		{
			$this->request->status = 404;
			$this->request->redirect('404');
		}
		
		$auth_user = $this->auth->get_user();
		if ($auth_user->has('roles', ORM::factory('role', array('name' => 'admin'))))
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
	
					$this->request->redirect(Route::get('group')->uri(array('id' => $group_id)));
				}
				else
				{
					$errors = $group->validate()->errors('validate');
				}
			}
		}
		else
		{
			$this->template->content = View::factory('template/general')
		 		->bind('title', $title)
		 		->bind('content', $content);
		 	
			$content = __('Not enough permission to perform this operation.');
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
		if ( ! $this->auth->logged_in())
		{
			$current_uri = URL::query(array('redir' => $this->request->uri));
			$this->request->redirect(Route::get('login')->uri().$current_uri);
		}
		
		$group = ORM::factory('group', $group_id);
		if ($group->loaded())
		{
			$auth_user = $this->auth->get_user();
			if ($auth_user->has('roles', ORM::factory('role', array('name' => 'admin'))))
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
	
}


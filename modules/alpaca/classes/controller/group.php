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

		$this->header->title->prepend($title);
	}
	
	/**
	 * Create a new group
	 */
	public function action_create() 
	{
		// Check login status else redirect to login page
		Alpaca::logged_in();
		
		$title = __('Create Category/Group');
		$this->header->title->prepend($title);
		$this->template->content = View::factory('group/add')
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
		
		if ( ! $group->loaded())
		{
			$this->request->status = 404;
			$this->request->redirect('404');
		}
		
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
	
					$this->request->redirect(Route::url('group', array('id' => $group_id)));
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
		$this->template->content = View::factory('group/list/single')
			->bind('group', $group)
			->bind('list_topics', $list_topics);

		$list_topics = View::factory('topic/list')
			->bind('group', $group)
			->bind('topics', $topics_array)
			->set('hide_group', TRUE)
			->bind('pagination', $pagination);

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

		$group_array = array(
			'id'		=> $group->id,
			'name'		=> $group->name,
			'link'		=> Route::url('group', array('id' => Alpaca_Group::uri($group))),
		);
		$group_array = (object) $group_array;
		
		$topics_array = array();
		if ($topics->count() > 0)
		{
			foreach ($topics as $i => $topic)
			{
				$author = $topic->author;
				$author_array = array(
					'id'		=> $author->id,
					'avatar'	=> Alpaca_User::avatar($author, array('size' => 30), array('class' => 'avatar'), TRUE),
					'nickname'	=> $author->nickname,
					'link'		=> Alpaca_User::url('user', $author)
				);
				$author_array = (object) $author_array;

				$collected = ORM::factory('collection')->is_collected($topic->id, $author->id);
				$topics_array[$i] = array(
					'id'			=> $topic->id,
					'title'		=> $topic->title,
					'link'			=> Alpaca_Topic::url($topic, $group),
					'author'		=> $author_array,
					'group'		=> $group_array,
					'collections'	=> $topic->collections,
					'comments'		=> $topic->count,
					'hits'			=> $topic->hits,
					'collected'	=> $collected,
					'content'		=> Alpaca::format_html($topic->content),
					'created'		=> date($this->config->date_format, $topic->created),
					'time_ago'		=> Alpaca::time_ago($topic->created),
					'updated'		=> Alpaca::time_ago($topic->updated),
				);

				$topics_array[$i] = (object) $topics_array[$i];
			}
		}

		$this->template->sidebar = View::factory('sidebar/group')
			->bind('group', $group);
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


<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Forum Entry
 *
 * @package controller
 * @author icyleaf <icyleaf.cn@gmail.com>
 */
class Controller_Forum extends Controller_Template_Alpaca {
	
	public function before()
	{
		if ($this->request->action === 'media')
		{
			// Do not template media files
			$this->auto_render = FALSE;
		}
		else
		{
			parent::before();
		}
	}
	
	/**
	 * Forum Entry
	 */
	public function action_index($type = NULL)
	{
		$this->head->link->append(URL::site('feed'), __('RSS 2.0'));

		$topics = ORM::factory('topic');
		// Content
		switch ($type)
		{
			case 'hot':
				$title = __('Top hit topics');
				$type = 'hits';
				break;
			case 'top':
				$title = __('Top collection topics');
				$type = 'collections';
				break;
			default:
			case 'new':
				$title = __('Latest topics');
				$type = 'touched';
				break;
		}

		$topics = $topics->get_topics($type, $this->config->topic['per_page']);
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

				$group = $topic->group;
				$group_array = array(
					'id'		=> $group->id,
					'name'		=> $group->name,
					'link'		=> Route::url('group', array('id' => Alpaca_Group::uri($group))),
				);
				$group_array = (object) $group_array;

				$collected = ORM::factory('collection')->is_collected($topic->id, $author->id);
				$topics_array[$i] = array(
					'id'			=> $topic->id,
					'title'			=> $topic->title,
					'link'			=> Alpaca_Topic::url($topic, $group),
					'author'		=> $author_array,
					'group'			=> $group_array,
					'collections'	=> $topic->collections,
					'comments'		=> $topic->count,
					'hits'			=> $topic->hits,
					'collected'		=> $collected,
					'content'		=> Alpaca::format_html($topic->content),
					'created'		=> date($this->config->date_format, $topic->created),
					'time_ago'		=> Alpaca::time_ago($topic->created),
					'updated'		=> Alpaca::time_ago($topic->updated),
				);

				$topics_array[$i] = (object) $topics_array[$i];
			}
		}

		$head = array(
			'title' => $title,
			'class' => $type,
		);

		// hide 'touched' anchor on index page
		$topic_sort = array();
		if ( ! in_array($this->request->uri , array('', '/', 'latest')))
		{
			$topic_sort['new'] = __('Latest');
		}
		$topic_sort['hot'] = __('Top hits');
		$topic_sort['top'] = __('Top collections');

		// broadcast
		$broadcast = NULL;
		if ( ! empty($this->config->broadcast))
		{
			$broadcast = '<div id="broadcast">'.$this->config->broadcast.'</div>';
		}

		$this->template->content = Twig::factory('topic/list')
			->bind('head', $head)
			->bind('topic_sort', $topic_sort)
			->bind('topics', $topics_array);

		// Sidebar
		$this->template->sidebar = $broadcast.
			Twig::factory('sidebar/about').
			Twig::factory('sidebar/members');
	}
	
	/**
	 * Get media file form alpaca module in media directory
	 */
	public function action_media()
	{
		if (IN_PRODUCTION)
		{
			// Generate and check the ETag for this file
			$this->request->check_cache(sha1($this->request->uri));
		}

		// Get the file path from the request
		$file = $this->request->param('file');

		// Find the file extension
		$ext = pathinfo($file, PATHINFO_EXTENSION);

		// Remove the extension from the filename
		$file = substr($file, 0, -(strlen($ext) + 1));

		if ($file = Kohana::find_file('media', $file, $ext))
		{
			// Send the file content as the response
			$this->request->response = file_get_contents($file);
		}
		else
		{
			// Return a 404 status
			$this->request->status = 404;
		}
		
		// Set the content type for this extension
		$this->request->headers['Content-Type'] = File::mime_by_ext($ext);
		$this->request->headers['Content-Length'] = filesize($file);
		$this->request->headers['Last-Modified'] = date('r', filemtime($file));
	}
}


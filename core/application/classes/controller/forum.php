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
		if ($this->request->action() === 'media')
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

		$topics_array = $topics->get_topics($type, $this->config->topic['per_page']);
		$topics_array = $topics->topics_list_array($topics_array);

		$topic_header = array(
			'title' => $title,
			'class' => $type,
		);

		$topic_sort = array(
			'new'   => __('Latest'),
			'hot'   => __('Top hits'),
			'top'   => __('Top collections'),
		);

		$this->template->content = Twig::factory('topic/list')
			->bind('topic_header', $topic_header)
			->bind('topic_sort', $topic_sort)
			->bind('topics', $topics_array);

		// broadcast
		$broadcast_content = $this->config->broadcast;
		$broadcast = Twig::factory('sidebar/broadcast')
			->bind('broadcast', $broadcast_content);

		// about
		$about_content = $this->config->about;
		$about = Twig::factory('sidebar/about')
			->set('stats', $this->_generate_stats())
			->bind('about', $about_content);

		// members
		$members = Twig::factory('sidebar/members')
			->set('random_members', Alpaca_User::random())
			->set('new_members', Alpaca_User::new_members());

		// Sidebar
		$this->template->sidebar = $broadcast.$about.$members;
	}
	
	/**
	 * Get media file form alpaca module in media directory
	 */
	public function action_media()
	{
		if (IN_PRODUCTION)
		{
			// Generate and check the ETag for this file
			$this->request->check_cache(sha1($this->request->uri()));
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


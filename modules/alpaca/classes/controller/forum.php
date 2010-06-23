<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Alpaca Forum Entry
 *
 * @package controller
 * @author icyleaf
 */
class Controller_Forum extends Controller_Alpaca {
	
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
		$this->header->link->append(URL::site('feed'), __('RSS 2.0'));
		
		$topics = ORM::factory('topic');
		// Content
		switch ($type)
		{
			case 'hits':
				$title = __('Top hit topics');
				break;
			case 'collections':
				$title = __('Top collection topics');
				break;
			default:
			case 'touched':
				$title = __('Latest topics');
				$type = 'touched';
				break;
		}

		$topics = $topics->get_topics($type, $this->config->topic['per_page']);
		$head = array(
			'title' => $title,
			'class' => $type,
		);

		// hide 'touched' anchor on index page
		$topic_sort = array();
		if ( ! in_array($this->request->uri , array('', '/', 'touched')))
		{
			$topic_sort['touched'] = __('Latest');
		}
		$topic_sort['hits'] = __('Top hits');
		$topic_sort['collections'] = __('Top collections');

		// broadcast
		$broadcast = NULL;
		if ( ! empty($this->config->broadcast))
		{
			$broadcast = '<div id="broadcast">'.$this->config->broadcast.'</div>';
		}

		$this->template->content = View::factory('topic/list')
			->set('head', $head)
			->set('topic_sort', $topic_sort)
			->set('topics', $topics);

		// Sidebar
		$this->template->sidebar = $broadcast.
			View::factory('sidebar/about').
			View::factory('sidebar/members');
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


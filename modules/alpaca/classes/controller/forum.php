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
	public function action_index()
	{
		$this->header->link->append(URL::site('feed'), __('RSS 2.0'));
		
		$topics = ORM::factory('topic');
		// Content
		$broadcast = NULL;
		if ( ! empty($this->config->broadcast))
		{
			$broadcast = '<div id="broadcast">'.$this->config->broadcast.'</div>';
		}

		// Recent topics
		$recent_topics = View::factory('topic/list')
			->set('head', array('title'=>__('Latest topics'), 'class'=>'recent'))
			->set('topics', $topics->get_topics('touched', $this->config->topic['per_page']));
		// Highest hits topics
		$hits_topics = View::factory('topic/list')
			->set('head', array('title'=>__('Top hit topics'), 'class'=>'groups'))
			->set('topics', $topics->get_topics('hits', $this->config->topic['per_page']));
		// Hottest collections topics
		$hot_topics = View::factory('topic/list')
			->set('head', array('title'=>__('Top fav topics'), 'class'=>'hot'))
			->set('topics', $topics->get_topics('collections', $this->config->topic['per_page']));
			
		$this->template->content = $broadcast.
			$recent_topics.
			$hits_topics.
			$hot_topics;
			
		// Sidebar
		$this->template->sidebar = 
			View::factory('sidebar/about').
			View::factory('sidebar/login').
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


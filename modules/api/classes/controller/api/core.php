<?php defined('SYSPATH') or die('No direct script access.');

class Controller_API_Core extends Controller_REST {

	protected $_alt = 'json';

	public function action_index()
	{
		$this->request->response = 'Alpaca API Development';
	}

	/**
	 * Render the response message with status and content-type
	 * @param sting $content
	 * @param int $status
	 * @param boolean $format
	 * @return void
	 */
	protected function _render($content, $status = 200, $format = TRUE)
	{
		$headers = array();
		if ($format)
		{
			switch($this->_alt)
			{
				case 'xml':
					// TODO: format $content into xml and assign itself
					$headers['Content-Type'] = 'text/xml; charset=utf-8';
					break;
				default:
				case 'json':
					$headers['Content-Type'] = 'application/json; charset=utf-8';
					$content = json_encode($content);
					break;
			}
		}
		else
		{
			$content = Kohana::debug($content);
		}

		$this->request->headers = $headers;
		$this->request->status = $status;
		$this->request->response = $content;
	}
}

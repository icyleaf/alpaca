<?php
/**
 * img.ly class for Kohana v3
 *
 * @author icyleaf <icyleaf.cn@gmail.com>
 * @link http://icyleaf.com
 * @version 0.1
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
class imgly {
	// single instance
	private static $_instance = array();
	// twitter account
	private $twitter = array();
	// throw errors
	private $errors = array();

	/**
	 * Instance
	 * @param string $username - twitter username
	 * @param string $password - twitter password
	 * @return object imgly
	 */
	public static function instance($username = NULL, $password = NULL)
	{
		$twitter = array(
			'username'	=> $username,
			'password'	=> $password,
			);

		$checksum = md5(serialize($twitter));
		if ( ! isset(imgly::$_instance[$checksum]))
		{
			imgly::$_instance[$checksum] = new imgly($twitter);
		}

		return imgly::$_instance[$checksum];
	}

	/**
	 * Init
	 * @param array $twitter - twitter account
	 * @return object imgly
	 */
	private function  __construct(Array $twitter = NULL)
	{
		$this->twitter = $twitter;
		
		return $this;
	}

	/**
	 * Setup Twitter account
	 * @param string $username - twitter username
	 * @param string $password - twitter password
	 * @return object imgly
	 */
	public function setup($username, $password)
	{
		$this->twitter = array(
			'username'	=> $username,
			'password'	=> $password,
			);

		return $this;
	}

	/**
	 * Update Photo with send optional message to twitter
	 * @param binary $media_data
	 * @param string $message - default is NULL
	 */
	public function upload($media_data, $message = NULL)
	{
		$data = array(
			'media'		=> '@'.$media_data,
			'message'	=> stripslashes($message),
		);

		if (empty($message))
		{
			$upload_url = 'http://img.ly/api/upload';
		}
		else
		{
			$upload_url = 'http://img.ly/api/uploadAndPost';
		}
		
		$data = array_merge($data, $this->twitter);
		$result = $this->_http_post($upload_url, $data);
		$element = new SimpleXMLElement($result);
		$status = (string) $element['stat'];
		if ($status == 'ok')
		{
			return $element->mediaurl;
		}
		else
		{
			$error = $element->err;
			$this->errors = array(
				'code'	=> (string) $error['code'],
				'msg'	=> (string) $error['msg'],
				);

			return FALSE;
		}
	}

	/**
	 * Show image with img.ly url
	 * @param string url - iml.ly url
	 * @param string $format - available in mini|thumb|medium|large|full, default is thumb
	 * @return string image url with HTML entry
	 */
	public function show($url, $format = 'thumb', $anchor = FALSE)
	{
		$original = 'http://img.ly/';
		$imgly_id = substr($url, strlen($original));
		$image_url = 'http://img.ly/show/'.$format.'/'.$imgly_id;

		$output = HTML::image($image_url);
		if ($anchor)
		{
			$output = HTML::anchor($url, $output);
		}

		return $output;
	}

	/**
	 * Response Errors Message
	 * @return array response errors
	 */
	public function errors()
	{
		return $this->errors;
	}

	/**
	 * HTTP Post Method
	 * @param string $url
	 * @param array $data
	 * @return string response content
	 * @throws Exception
	 */
	private function _http_post($url, Array $data = array())
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$content = curl_exec($ch);

		//Wrap the error reporting in an exception
		if($content === FALSE)
		{
			throw new Exception("Curl Error: ".curl_error($ch));
		}
		curl_close($ch);

		return $content;
	}
}
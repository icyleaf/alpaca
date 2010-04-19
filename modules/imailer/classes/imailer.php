<?php defined('SYSPATH') or die('No direct script access.');
/**
 * iMailer class
 *
 * @package core
 * @author icyleaf
 */
class iMailer {
	/**
	 * Sets the CharSet of the message.
	 * @var string
	 */
	public $charset           	= 'utf-8';
	
	/**
	 * Sets the Content-type of the message.
	 * @var string
	 */
	public $content_type      	= 'text/plain';
	
	/**
	 * Sets the Encoding of the message. Options for this are
	 *  "8bit", "7bit", "binary", "base64", and "quoted-printable".
	 * @var string
     */
	public $encoding          	= '8bit';
	
	/**
	 * SMTP
	 */
	public $smtp				= array();
	
	/**
	 * Holds the most recent mailer error message.
	 * @var string
	 */	
	public $errors 				= NULL;
	
	protected $phpmailer 		= NULL;
	
	private static $_instances 	= NULL;
	
	/**
	 * Instance
	 *
	 * @return object
	 */
	public static function instance()
	{
		if ( ! isset(iMailer::$_instances))
		{
			iMailer::$_instances = new iMailer();
		}

		return iMailer::$_instances;
	}
	
	public function __construct()
	{
		require_once Kohana::find_file('vendor', 'class.phpmailer');
		
		$this->phpmailer = new PHPMailer;
	}
	
	/**
	 * Set mail subject
	 *
	 * @param string $subject 
	 * @return object
	 */
	public function subject($subject)
	{
		$this->phpmailer->Subject = $subject;
		
		return $this;
	}
	
	/**
	 * Set mail content
	 *
	 * @param string $content 
	 * @return object
	 */
	public function content($content)
	{
		$this->phpmailer->MsgHTML($content);
		
		return $this;
	}
	
	/**
	 *  Set send to email address
	 *
	 * @param string $email 
	 * @param string $name 
	 * @return object
	 */
	public function to_address($email, $name = NULL)
	{
		$this->phpmailer->AddAddress($email, $name);
		
		return $this;
	}
	
	/**
	 * Set send bcc email address
	 *
	 * @param string $email 
	 * @param string $name 
	 * @return object
	 */
	public function bcc_address($email, $name = NULL)
	{
		$this->phpmailer->AddBCC($email, $name);
		
		return $this;
	}
	
	/**
	 * Set send from email address
	 *
	 * @param string $email 
	 * @param string $name 
	 * @return object
	 */
	public function from_address($email, $name = NULL)
	{
		$this->phpmailer->SetFrom($email, $name);

		return $this;
	}
	
	/**
	 * Set reply email address
	 *
	 * @param string $email 
	 * @param string $name 
	 * @return object
	 */
	public function reply_address($email, $name = NULL)
	{
		$this->phpmailer->AddReplyTo($email, $name);
		
		return $this;
	}
	
	/**
	 * Set SMTP server configuration
	 *
	 * @param Array $config 
	 * @return object
	 */
	public function smtp(Array $config)
	{
		$this->smtp = $config;
		
		return $this;
	}
	
	/**
	 * Mail send
	 *
	 * @return boolean
	 */
	public function send()
	{
		if ( ! empty($this->smtp))
		{
			$this->phpmailer->IsSMTP(); // telling the class to use SMTP
			if (strpos($this->smtp['host'], 'gmail') === FALSE)
			{
				// Normal SMTP server
				$this->phpmailer->Host = $this->smtp['host'];
				$this->phpmailer->Port = $this->smtp['port'];
			}
			else
			{
				// Gmail SMTP server
				$this->phpmailer->SMTPSecure = 'ssl'; 
				$this->phpmailer->Host = $this->smtp['host'];
				$this->phpmailer->Port = 465;
			}
			// Auth
			if (array_key_exists('username', $this->smtp) AND array_key_exists('password', $this->smtp))
			{
				$this->phpmailer->SMTPAuth = TRUE;  // enable SMTP authentication
				$this->phpmailer->Username = $this->smtp['username']; // SMTP account username
				$this->phpmailer->Password = $this->smtp['password']; // SMTP account password
			}
			// SMTP Debug
			if (array_key_exists('debug', $this->smtp) AND is_numeric($this->smtp['debug']))
			{
				// 1 = errors and messages
                // 2 = messages only
				$this->phpmailer->SMTPDebug = $this->smtp['debug'];
			}
		}
		$this->phpmailer->CharSet = $this->charset;
		$this->phpmailer->ContentType = $this->content_type;
		$this->phpmailer->Encoding = $this->encoding;
		
		if( ! $this->phpmailer->Send()) {
			$this->errors = $this->phpmailer->ErrorInfo;
			return FALSE;
		} 
		else 
		{
		 	return TRUE;
		}
	}
	
	/**
	 * Display mail error messages
	 *
	 * @return mixed
	 */
	public function errors()
	{
		return $this->errors;
	}
	
}


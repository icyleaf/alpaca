<?php defined('SYSPATH') or die('No direct script access.');

class Controller_iMailer extends Controller {
	
	public function action_index()
	{
		$mailer = iMailer::instance()
			->to_address('icyleaf.cn@gmail.com', 'icyleaf')
			->from_address('noreply@kohana.cn', 'Kohana 中文')
			->subject('TEST2')
			->content('ddddddddddddd')
			->smtp(array(
				'host'		=> 'mail.kohana.cn',
				'port'		=> 25,
				'username'	=> 'noreply@kohana.cn',
				'password'	=> 'zxasqw12',
			));
		
		if ( ! $mailer->send())
		{
			echo Kohana::debug($mailer->errors());
		}

		echo Kohana::debug($mailer);
	}
}


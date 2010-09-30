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
				'host'		=> 'maile.server.com',
				'port'		=> 25,
				'username'	=> 'username',
				'password'	=> 'p@assword',
			));
		
		if ( ! $mailer->send())
		{
			echo Kohana::debug($mailer->errors());
		}

		echo Kohana::debug($mailer);
	}
}


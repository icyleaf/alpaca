<?php echo $header_body; ?>
<div id="wrap">
	<div id="header">
		<div class="block">
		<?php 
			if ($user = $auth->get_user())
			{
				$auth_links = array
				(
					Route::get('user')->uri(array('id' => Alpaca_User::the_uri($user))) => array(
						'title' => $user->nickname, 
						'attr' => array('class' => 'user')
						),
					'settings'	=> array('title' => __('Settings')),
					'logout'	=> array('title' => __('Log out')),
				);
			}
			else
			{
				$auth_links = array
				(
					'register'	=> array('title' => __('Sign up'), 'attr' => array('style' => 'color: #7F2D20')),
					'login'		=> array('title' => __('Log in')),
				);
			}
			$auth_menu = array();
			foreach ($auth_links as $link => $item)
			{
				$attr = isset($item['attr'])?$item['attr']:array();
				if (empty($link))
				{
					$auth_menu[] = $item['title']; 
				}
				else if (preg_match('/^<(\w+)>$/i', $link, $match))
				{
					$auth_menu[] = '<'.$match[1].html::attributes($attr).'>'.$item['title'].'</'.$match[1].'>'; 
				}
				else
				{
					$auth_menu[] = html::anchor($link, $item['title'], $attr); 
				}
			}
			echo '<div class="auth">'.join('|', $auth_menu).'</div>';
		?>
			<div class="search">
			<form action="<?php echo URL::site('search'); ?>">
				<input type="text" name="q" id="header_search_query" value="<?php echo __('Search Topic'); ?>"/><input type="submit" value="<?php echo __('Go'); ?>" />
			</form>
			</div>
		</div>
	
		<div id="website">
		<?php 
			echo html::anchor(
				url::base(), 
				html::image($config->logo), array('alt' => $config->title), array('id' => 'logo')
				);
		?>
		</div><!-- /website -->
		<div class="clear"></div>
		
		<div id="cpanel">
			<?php echo $menu; ?>
			<div class="clear"></div>
		</div><!-- /cpanel -->
	</div><!-- /header -->
	<div id="container">
		<!--[if lt IE 7]>
		<div style='border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 75px; position: relative;margin-bottom: 20px;'>
		<div style='position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;'><a href='#' onclick='javascript:this.parentNode.parentNode.style.display="none"; return false;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-cornerx.jpg' style='border: none;' alt='Close this notice'/></a></div>
		<div style='width: 640px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;'>
		  <div style='width: 75px; float: left;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg' alt='Warning!'/></div>
		  <div style='width: 275px; float: left; font-family: Arial, sans-serif;'>
			<div style='font-size: 14px; font-weight: bold; margin-top: 12px;'>You are using an outdated browser</div>
			<div style='font-size: 12px; margin-top: 6px; line-height: 12px;'>For a better experience using this site, please upgrade to a modern web browser.</div>
		  </div>
		  <div style='width: 75px; float: left;'><a href='http://www.firefox.com' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-firefox.jpg' style='border: none;' alt='Get Firefox 3.5'/></a></div>
		  <div style='width: 75px; float: left;'><a href='http://www.browserforthebetter.com/download.html' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-ie8.jpg' style='border: none;' alt='Get Internet Explorer 8'/></a></div>
		  <div style='width: 73px; float: left;'><a href='http://www.apple.com/safari/download/' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-safari.jpg' style='border: none;' alt='Get Safari 4'/></a></div>
		  <div style='float: left;'><a href='http://www.google.com/chrome' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-chrome.jpg' style='border: none;' alt='Get Google Chrome'/></a></div>
		</div>
		</div>
		<![endif]-->
		
		<?php if (isset($sidebar)): ?>
	 	<div id="sidebar">
			<?php if (isset($sidebar)) echo $sidebar; ?>
		</div><!-- /sidebar -->
		<div id="content" class="right_column">
		<?php else: ?>
		<div id="content">
		<?php endif; ?>			
			<?php if (isset($content)) echo $content; ?>
		</div><!-- /content -->
		
		<div class="clear"></div>
	</div><!-- /container -->
	
	<div id="footer">
		<div class="left">
			<?php echo Alpaca::copyright(Kohana::config('alpaca.copyright_year')); ?>
		</div>
		<div id="right">
			<?php if ($config->execution_time): ?>
				Rendered in {execution_time}. 
			<?php endif ?>
			Powered by <?php echo html::anchor($config->project['url'], $config->project['name']); ?>. 
		</div>
		<div class="clear"></div>
	</div>
</div>
<?php echo $footer_body; ?>
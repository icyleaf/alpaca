<div id="profile">
	<div class="profile-header">
		<div id="actions" class="right">
			<?php // echo HTML::anchor('#mail/send/'.$user->id, __('Message'), array('class' => 'button'))?>
			<?php // echo HTML::anchor('#user/follow/'.$user->id, __('Follow'), array('class' => 'button'))?>
		</div>
		<div class="left">
			<?php echo Alpaca_User::avatar($user, NULL, array('class' => 'avatar'));?>
			<span class="nickname"><?php echo $user->nickname; ?></span>
		</div> 
		<div class="clear"></div>
	</div> 
	
	<div class="profile-content">
		<div class="span-8 vcard">
		<?php foreach ($user_profiles as $key => $value): ?>
		<dl>
			<dt><?php echo $key;?></dt>
			<dd><?php echo $value;?></dd>
		</dl>
		<?php endforeach; ?>
		</div>
		<div class="right">
			<div class="statistics">
				<div class="span-3">
					<div class="sides">
						<div class="header"><?php echo __('Topics'); ?>:</div>
						<?php
							$link = Route::url('user', array(
								'id'	=> Alpaca_User::the_uri($user),
								'type'	=> 'topics',
								));
							$count = '<div class="count">'.$topics->count().'</div>';
							echo ($topics->count() > 0) ? HTML::anchor($link, $count) : $count;
						?>
					</div>
				</div>
				<div class="span-3">
					<div class="sides">
						<div class="header"><?php echo __('Replies'); ?>:</div>
						<?php
							$link = Route::url('user', array(
								'id'	=> Alpaca_User::the_uri($user),
								'type'	=> 'posts',
								));
							$count = '<div class="count">'.$replies->count().'</div>';
							echo ($replies->count() > 0) ? HTML::anchor($link, $count) : $count;
						?>
					</div>
				</div>
				<div class="span-3">
					<div class="sides last">
						<div class="header"><?php echo __('Collections'); ?>:</div>
						<?php
							$link = Route::url('user', array(
								'id'	=> Alpaca_User::the_uri($user),
								'type'	=> 'collections',
								));
							$count = '<div class="count">'.$collections_count.'</div>';
							echo ($collections_count > 0) ? HTML::anchor($link, $count) : $count;
						?>
					</div>
				</div>
				<!--
				<div class="span-3">
					<div class="sides last">
						<div class="header"><?php //echo __('Groups'); ?>:</div>
						<?php
//							$link = Route::url('user', array(
//								'id'	=> Alpaca_User::the_uri($user),
//								'type'	=> 'groups',
//								));
//							$count = '<div class="count">'.$groups->count().'</div>';
//							echo ($groups->count() > 0) ? HTML::anchor($link, $count) : $count;
						?>
					</div>
				</div>
				<div class="span-3">
					<div class="sides">
						<div class="header"><?php //echo __('Followings'); ?>:</div>
						<?php
//							$link = Route::url('user', array(
//								'id'	=> Alpaca_User::the_uri($user),
//								'type'	=> 'followings',
//								));
//							echo '<div class="count">'.$following_count.'</div>';
						?>
					</div>
				</div>
				<div class="span-3">
					<div class="sides last">
						<div class="header"><?php //echo __('Friends'); ?>:</div>
						<?php
//							$link = Route::url('user', array(
//								'id'	=> Alpaca_User::the_uri($user),
//								'type'	=> 'followers',
//								));
//							echo '<div class="count">'.$follower_count.'</div>';
						?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			-->
			
			<div class="user-record">
			<?php 
				echo __('Registered at :created. Last signed in at :last. :logins logins.', array(
					':user' 	=> Alpaca::beautify_str($user->nickname, FALSE, TRUE),
					':created'	=> Alpaca::time_ago($user->created),
					':last'		=> Alpaca::time_ago($user->last_login),
					':logins'	=> $user->logins,
					)); 
			?>
			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>

<div class="span-15">
	<h4 class="rss">
	<?php 
		if (I18n::$lang == 'zh-cn')
		{
			$user_link = Alpaca::beautify_str($user->nickname, TRUE, TRUE);
		}
		else
		{
			$user_link = $user->nickname;
		}
		
		echo HTML::anchor(Route::url('user/feed', array('id' => Alpaca_User::the_uri($user))),
			__('Subscribe the latest updates @:user...', array(':user' => $user_link)));
	?>
	</h4>
</div>
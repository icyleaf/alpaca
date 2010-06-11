<?php
$username = Arr::get($_POST, 'username', $user->username);
$nickname = Arr::get($_POST, 'nickname', $user->nickname);
$location = Arr::get($_POST, 'location', $user->location);
$website = Arr::get($_POST, 'website', $user->website);
$qq = Arr::get($_POST, 'qq', $user->qq);
$msn = Arr::get($_POST, 'msn', $user->msn);
$gtalk = Arr::get($_POST, 'gtalk', $user->gtalk);
$skype = Arr::get($_POST, 'skype', $user->skype);

$error_username = Arr::get($errors, 'username');
$error_nickname = Arr::get($errors, 'nickname');
$error_location = Arr::get($errors, 'location');
$error_website = Arr::get($errors, 'website');
$error_qq = Arr::get($errors, 'qq');
$error_msn = Arr::get($errors, 'msn');
$error_gtalk = Arr::get($errors, 'gtalk');
$error_skype = Arr::get($errors, 'skype');
?>
<h3><?php echo __('Change Avatar'); ?></h3>
<div class="avatar">
	<?php echo HTML::image(Alpaca_User::avatar($user), array(
			'width' => 48,
			'height' => 48,
			'alt' => __('avatar')
		)); ?>
	<p>
		<strong>
			<?php echo __('Change your avatar at :grvartar', array(
				':website'	=> $config->title,
				':grvartar'	=> HTML::anchor('http://gravatar.com', 'Gravatar.com'),
				)); ?>
		</strong>
		<?php echo __('We’re using :email', array(':email' => $user->email)); ?>
	</p>
	<div class="clear"></div>
</div>

<h3><?php echo __('Account Profile'); ?></h3>
<dl>
	<dt><label><?php echo __('Email'); ?></label></dt>
	<dd>
		<input class="field readonly" type="text" value="<?php echo $user->email; ?>" readonly />
	</dd>
</dl>
<dl>
	<dt><label class="tips">username</label></dt>
	<dd>
		<?php $uname_attr = empty($username) ? '' : ' readonly'; ?>
		<input class="field<?php echo $uname_attr; ?>" type="text"
		      name="username" value="<?php echo $username; ?>" <?php echo $uname_attr; ?> />
		<br /><span class="error"><?php echo $error_username; ?></span>
	</dd>
</dl>
<dl>
	<dt><label><?php echo __('Nickname'); ?></label></dt>
	<dd>
		<input class="field" type="text" name="nickname" value="<?php echo $nickname; ?>"/>
		<br /><span class="error"><?php echo $error_nickname; ?></span>
	</dd>
</dl>
<dl>
	<dt><label><?php echo __('Location'); ?></label></dt>
	<dd>
		<input class="field" type="text" name="location" value="<?php echo $location; ?>"/>
		<br /><span class="error"><?php echo $error_location; ?></span>
	</dd>
</dl>
<div class="submit txt_right">
	<input class="button" type="submit" value="<?php echo __('Update Profile'); ?>" />
</div>

<h3><?php echo __('Contacts'); ?></h3>
<dl>
	<dt><label><?php echo __('Website'); ?></label></dt>
	<dd>
		<input class="field" type="text" name="website" value="<?php echo $website; ?>"/>
		<br /><span class="error"><?php echo $error_website; ?></span>
	</dd>
</dl>
<dl>
	<dt><label><?php echo __('QQ'); ?></label></dt>
	<dd>
		<input class="field" type="text" name="qq" value="<?php echo $qq; ?>"/>
		<br /><span class="error"><?php echo $error_qq; ?></span>
	</dd>
</dl>
<dl>
	<dt><label><?php echo __('MSN'); ?></label></dt>
	<dd>
		<input class="field" type="text" name="msn" value="<?php echo $msn; ?>"/>
		<br /><span class="error"><?php echo $error_msn; ?></span>
	</dd>
</dl>
<dl>
	<dt><label><?php echo __('Gtalk'); ?></label></dt>
	<dd>
		<input class="field" type="text" name="gtalk" value="<?php echo $gtalk; ?>"/>
		<br /><span class="error"><?php echo $error_gtalk; ?></span>
	</dd>
</dl>
<dl>
	<dt><label><?php echo __('Skype'); ?></label></dt>
	<dd>
		<input class="field" type="text" name="skype" value="<?php echo $skype; ?>"/>
		<br /><span class="error"><?php echo $error_skype; ?></span>
	</dd>
</dl>

<div class="submit txt_right">
	<input class="button" type="submit" value="<?php echo __('Update Profile'); ?>" />
</div>
<input type="hidden" name="id" value="<?php echo $user->id; ?>" />
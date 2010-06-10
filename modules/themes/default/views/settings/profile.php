<?php
$username = isset($_POST['username']) ? $_POST['username'] : $user->username;
$nickname = isset($_POST['nickname']) ? $_POST['nickname'] : $user->nickname;
$location = isset($_POST['location']) ? $_POST['location'] : $user->location;
$website = isset($_POST['website']) ? $_POST['website'] : $user->website;
$qq = isset($_POST['qq']) ? $_POST['qq'] : $user->qq;
$msn = isset($_POST['msn']) ? $_POST['msn'] : $user->msn;
$gtalk = isset($_POST['gtalk']) ? $_POST['gtalk'] : $user->gtalk;
$skype = isset($_POST['skype']) ? $_POST['skype'] : $user->skype;

$error_username = isset($errors['username']) ? $errors['username'] : '';
$error_nickname = isset($errors['nickname']) ? $errors['nickname'] : '';
$error_location = isset($errors['location']) ? $errors['location'] : '';
$error_website = isset($errors['website']) ? $errors['website'] : '';
$error_qq = isset($errors['qq']) ? $errors['qq'] : '';
$error_msn = isset($errors['msn']) ? $errors['msn'] : '';
$error_gtalk = isset($errors['gtalk']) ? $errors['gtalk'] : '';
$error_skype = isset($errors['skype']) ? $errors['skype'] : '';
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
		<?php if (empty($username)): ?>
			<input class="field" type="text" name="username" value="<?php echo $username; ?>" />
		<?php else: ?>
			<input class="field readonly" type="text" value="<?php echo $username; ?>" readonly/>
		<?php endif; ?>
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
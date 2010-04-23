<?php
$error_current_password = isset($errors['current_password']) ? $errors['current_password'] : '';
$error_password = isset($errors['password']) ? $errors['password'] : '';
$error_password_confirm = isset($errors['password_confirm']) ? $errors['password_confirm'] : '';
?>

<h3><?php echo __('Change Password'); ?></h3>
<dl>
	<dt><label><?php echo __('Current Password'); ?></label></dt>
	<dd>
		<input class="field" type="password" name="current_password"/>
		<br /><span class="error"><?php echo $error_current_password; ?></span>
	</dd>
</dl>
<dl>
	<dt><label><?php echo __('New Password'); ?></label></dt>
	<dd>
		<input class="field" type="password" name="password" />
		<br /><span class="error"><?php echo $error_password; ?></span>
	</dd>
</dl>
<dl>
	<dt><label><?php echo __('Password Confirm'); ?></label></dt>
	<dd>
		<input class="field" type="password" name="password_confirm" />
		<br /><span class="error"><?php echo $error_password_confirm; ?></span>
	</dd>
</dl>

<div class="submit txt_right">
	<input class="button" type="submit" value="<?php echo __('Reset'); ?>" />
</div>
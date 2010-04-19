<?php
$email = isset($_POST['email']) ? $_POST['email'] : $user->email;
$nickname = isset($_POST['nickname']) ? $_POST['nickname'] : $user->nickname;

$error_pwd = isset($errors['password']) ? $errors['password'] : '';
$error_pwd_con = isset($errors['password_confirm']) ? $errors['password_confirm'] : '';
?>
<div id="authform">
	<h2><?php echo $title; ?></h2>
	<form method="post">
	<div id="authform-body">
		<p>
			<label><?php echo __('email'); ?>:</label><br />
			<input readonly id="email" class="readonly" name="email" type="text" value="<?php echo $email; ?>" readonly/>
		</p>
		<p>
			<label><?php echo __('nickname'); ?>:</label><br />
			<input readonly id="nickname" class="readonly" name="nickname" type="text" value="<?php echo $nickname; ?>" readonly/>
		</p>
		<p>
			<label><?php echo __('password'); ?>:</label><br />
			<input id="password" name="password" type="password" tabindex="10" />
			<br /><span class="error"><?php echo $error_pwd; ?></span>
		</p>
		<p>
			<label><?php echo __('password_confirm'); ?>:</label><br />
			<input id="password" name="password_confirm" type="password" tabindex="20" />
			<br /><span class="error"><?php echo $error_pwd_con; ?></span>
		</p>
		<p class="submit">
			<input class="button" id="submit" type="submit" tabindex="100" value="<?php echo __('Reset Password'); ?>" />
		</p>
		<div class="clear"></div>
	</div>
	</form>
</div>
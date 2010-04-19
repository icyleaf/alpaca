<?php
$email = isset($_POST['email']) ? $_POST['email'] : '';
$nickname = isset($_POST['nickname']) ? $_POST['nickname'] : '';

$error_email = isset($errors['email']) ? $errors['email'] : '';
$error_pwd = isset($errors['password']) ? $errors['password'] : '';
$error_pwd_conf = isset($errors['password_confirm'] ) ? $errors['password_confirm'] : '';
$error_nickname = isset($errors['nickname']) ? $errors['nickname'] : '';
?>
<div id="authform">
	<h2>
		<?php echo $title; ?>
		<small><?php echo html::anchor(Route::get('login')->uri(), '('.__('Log in').')'); ?></small>
	</h2>
	<form method="post">
	<div id="authform-body">
		<p>
			<label><label><?php echo __('Your Email'); ?>:</label><br />
			<input id="email" name="email" type="text" tabindex="10" value="<?php echo $email; ?>" />
			<br /><span class="error"><?php echo $error_email; ?></span>
		</p>
		<p>
			<label><label><?php echo __('New Password'); ?>:</label><br />
			<input id="password" name="password" type="password" tabindex="20" />
			<br /><span class="error"><?php echo $error_pwd; ?></span>
		</p>
		<p>
			<label><label><?php echo __('Pick a nickname'); ?>:</label><br />
			<input id="nickname" name="nickname" type="text" tabindex="40" value="<?php echo $nickname; ?>" />
			<br /><span class="error"><?php echo $error_nickname; ?></span>
		</p>
		<p class="submit">
			<input name="random" type="hidden" value="<?php echo time(); ?>" />
			<input id="nospam" name="nospam" type="hidden" value="0" />
			<input class="button" id="submit" type="submit" tabindex="100" value="<?php echo __('Quick Register'); ?>" />
		</p>
		<div class="clear"></div>
	</div>
	</form>
</div>
<script type="text/javascript" src="<?php echo URL::site('media/js/nospam.js'); ?>"></script>
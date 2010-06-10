<?php
$email = isset($_POST['email']) ? $_POST['email'] : '';

$error_email = isset($errors['email']) ? $errors['email'] : '';
$error_pwd = isset($errors['password']) ? $errors['password'] : '';

$redir = empty($_SERVER['HTTP_REFERER']) ? url::base() : $_SERVER['HTTP_REFERER'];
?>
<div id="authform">
	<h2>
		<?php echo $title; ?>
		<small><?php echo HTML::anchor(Route::get('register')->uri(), '('.__('Sign up').')'); ?></small>
	</h2>
	<form method="post">
	<input type="hidden" name="redir" value="<?php echo $redir; ?>" />
	<div id="authform-body">
		<p>
			<label><?php echo __('Email'); ?>:</label><br />
			<input id="email" name="email" type="text" tabindex="10" value="<?php echo $email; ?>" />
			<br /><span class="error"><?php echo $error_email; ?></span>
		</p>
		<p>
			<label><?php echo __('Password'); ?>:</label>
			<?php echo HTML::anchor('lostpassword', '('.__('Lost Password').')'); ?><br />
			<input id="password" name="password" type="password" tabindex="20" />
			<br /><span class="error"><?php echo $error_pwd; ?></span>
		</p>
		<p>
			<input id="remember" type="checkbox" tabindex="90" value="true" name="remember"/>
			<span class="tips"><?php echo __('Remember me'); ?></span>
		</p>
		<p class="submit">
			<input class="button" id="submit" type="submit" tabindex="100" value="<?php echo __('Log in'); ?>" />
		</p>
		<div class="clear"></div>
	</div>
	</form>
</div>
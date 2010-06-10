<?php
$email = isset($_POST['email']) ? $_POST['email'] : '';
$error_email = isset($errors['email']) ? $errors['email'] : '';
?>
<div id="authform">
	<h2>
		<?php echo $title; ?>
		<small><?php echo HTML::anchor(Route::get('login')->uri(), '('.__('Log in').')'); ?></small>
	</h2>
	<form method="post">
	<div id="authform-body">
		<p>
			<label><?php echo __('Email'); ?>:</label><br />
			<input id="email" name="email" type="text" tabindex="10" value="<?php echo $email; ?>" />
			<br /><span class="error"><?php echo $error_email; ?></span>
		</p>

		<p class="submit">
			<input class="button" id="submit" type="submit" tabindex="100" value="<?php echo __('Reset Password'); ?>" />
		</p>
		<div class="clear"></div>
	</div>
	</form>
</div>
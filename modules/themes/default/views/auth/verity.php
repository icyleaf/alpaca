<?php
$hash_code = isset($_POST['hash_code']) ? $_POST['hash_code'] : '';
$error_hash_code = isset($errors['hash_code']) ? $errors['hash_code'] : '';

if ( ! empty($action) AND $action == 'resend'):
	$email = isset($email) ? $email : (isset($_POST['email']) ? $_POST['email'] : '');
	$error_email = isset($errors['email']) ? $errors['email'] : '';
?>
<div id="authform">	
	<h2><?php echo __('Send me again'); ?></h2>
	<form method="post">
	<div id="authform-body">
		<p>
			<label><?php echo __('Email'); ?>:</label><br />
			<input id="email" name="email" type="text" tabindex="100" value="<?php echo $email; ?>" />
			<br /><span class="wrong"><?php echo $error_email; ?></span>
		</p>
		<p class="submit">
			<input id="submit" class="button" type="submit" tabindex="200" value="<?php echo __('Send'); ?>" />
		</p>
		<div class="clear"></div>
	</div>
	</form>
</div>
<?php else: ?>
<div id="authform">
	<h2><?php echo $title; ?></h2>
	<form method="post">
	<div id="authform-body">
		<p>
			<label><?php echo __('Validation Code'); ?>:</label>
			<?php if ( ! empty($email)): ?>
			(<?php echo HTML::anchor('auth/verity?action=resend&email='.$email, '重新发送'); ?>)
			<?php endif ?>
			<br />
			<input id="email" name="hash_code" type="text" tabindex="10" value="<?php echo $hash_code; ?>" />
			<br /><span class="wrong"><?php echo $error_hash_code; ?></span>
		</p>
		<p class="submit">
			<input id="submit" class="button" type="submit" tabindex="50" value="<?php echo __('Verity'); ?>" />
		</p>
		
		<div class="clear"></div>
	</div>
	</form>
</div>
<?php endif; ?>
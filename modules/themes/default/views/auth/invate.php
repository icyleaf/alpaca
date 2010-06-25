<div id="authform">
	<h2>
		<?php echo $title; ?>
		<small><?php echo HTML::anchor('login', '('.__('Log in').')'); ?></small>
	</h2>
	<form method="post">
	<div id="authform-body">
		<p>
			<label><?php echo __('email'); ?>:</label><br />
			<input id="email" name="email" type="text" tabindex="10" value="<?php echo Arr::get($_POST, 'email'); ?>" />
			<br /><span class="wrong"><?php echo Arr::get($errors, 'email'); ?></span>
		</p>
		<p class="submit">
			<input id="submit" type="submit" tabindex="100" value="<?php echo __('Request Invite'); ?>" />
		</p>
		
		<div class="clear"></div>
	</div>
	</form>
</div>
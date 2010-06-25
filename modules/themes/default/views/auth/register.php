<div id="authform">
	<h2>
		<?php echo $title; ?>
		<small><?php echo HTML::anchor(Route::get('login')->uri(), '('.__('Log in').')'); ?></small>
	</h2>
	<form method="post">
	<div id="authform-body">
		<p>
			<label><?php echo __('Your Email'); ?>:</label><br />
			<input id="email" name="email" type="text" tabindex="10" value="<?php echo Arr::get($_POST, 'email'); ?>" />
			<br /><span class="error"><?php echo Arr::get($errors, 'email'); ?></span>
		</p>
		<p>
			<label><?php echo __('New Password'); ?>:</label><br />
			<input id="password" name="password" type="password" tabindex="20" />
			<br /><span class="error"><?php echo Arr::get($errors, 'password'); ?></span>
		</p>
		<p>
			<label><?php echo __('Pick a nickname'); ?>:</label><br />
			<input id="nickname" name="nickname" type="text" tabindex="40" value="<?php echo Arr::get($_POST, 'nickname'); ?>" />
			<br /><span class="error"><?php echo Arr::get($errors, 'password_confirm'); ?></span>
		</p>
		<p class="submit">
			<input id="random" name="random" type="hidden" value="<?php echo time(); ?>" />
			<input id="nospam" name="nospam" type="hidden" value="0" />
			<input class="button" id="submit" type="submit" tabindex="100" value="<?php echo __('Quick Register'); ?>" />
		</p>
		<div class="clear"></div>
	</div>
	</form>
</div>
<div id="authform">
	<h2>
		<?php echo $title; ?>
		<small><?php echo HTML::anchor(Route::url('register'), '('.__('Sign up').')'); ?></small>
	</h2>
	<form method="post">
	<div id="authform-body">
		<p>
			<label><?php echo __('Email'); ?>:</label><br />
			<input id="email" name="username" type="text" tabindex="10" value="<?php echo Arr::get($_POST, 'email'); ?>" />
			<br /><span class="error"><?php echo Arr::get($errors, 'email'); ?></span>
		</p>
		<p>
			<label><?php echo __('Password'); ?>:</label>
			<?php echo HTML::anchor('lostpassword', '('.__('Lost Password').')'); ?><br />
			<input id="password" name="password" type="password" tabindex="20" />
			<br /><span class="error"><?php echo Arr::get($errors, 'password'); ?></span>
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
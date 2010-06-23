<?php if ( ! $auth->logged_in()): ?>
<div id="login_form" class="widget">
	<h3>
		<?php echo __('User Login'); ?>
		<small><?php echo HTML::anchor(Route::get('register')->uri(), '('.__('Sign up').')')?></small>
	</h3>
	<form action="<?php echo Route::url('login'); ?>" method="POST">
	<input type="hidden" name="redir" value="<?php echo URL::base(); ?>" />
	<dl>
		<dd>
			<label><?php echo __('Email'); ?></label>
			<input type="text" name="email" />
		</dd>
		<dd>
			<label><?php echo __('Password'); ?></label>
			<input type="password" name="password" />
		</dd>
		<dd>
			<span id="lostpassword"><?php echo HTML::anchor('lostpassword', __('Lost Password')); ?></span>
			<input id="submit" type="submit" class="button" value="<?php echo __('Log in'); ?>" />
		</dd>
	</dl>
	</form>
</div>	
<?php endif ?>
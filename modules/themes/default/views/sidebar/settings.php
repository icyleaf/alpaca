<?php
	$user = $auth->get_user();
?>
<div class="widget">
	<div class="content">
		<label><?php echo __('Privacy'); ?></label>
		<p>
		<?php echo __('Keep in mind that we will never display your email address to the public. '.
			'It will only be used to notify you of updates.'); ?>
		</p>
		
		<label><?php echo __('Notifications'); ?></label>
		<p>
		<?php echo __('Want to be notified of issues as they come in? Check the :notifications section.', array(
			':notifications' => HTML::anchor('settings/notification', __('Notifications'))
			)); ?>
		</p>
		
		<label><?php echo __('View your public profile'); ?></label>
		<p>
		<?php echo __(':click_here to view your public profile.', array(
			':click_here' => HTML::anchor(Alpaca_User::the_url('user', $user), __('Click here'))
			)); ?>
		</p>
	</div>
</div>

<?php if (isset($view)) {echo $view;} ?>
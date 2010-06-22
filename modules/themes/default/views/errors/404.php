<div id="errors">
	<h2>
		<?php if (isset($code)): ?>
		<span><?php echo $code; ?></span>
		<?php endif; ?>
		<?php echo $title; ?>
	</h2>
	<div>
		<h3><?php echo __('The page you have requested has flown the coop.'); ?></h3>
		<h5><?php echo __('Perhaps you are here because:'); ?></h5>
		<ul>
			<li><?php echo __('The page has moved'); ?></li>
			<li><?php echo __('The page no longer exists'); ?></li>
			<li><?php echo __('You were looking for your puppy and got lost'); ?></li>
			<li><?php echo __('You like 404 page'); ?></li>
		</ul>
	</div>
</div>
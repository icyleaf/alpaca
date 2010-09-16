<div class="widget">
	<h3><?php echo __('forum memebers'); ?></h3>
	<div class="content">
		<div id="star_member">
			<h4><?php echo __('random memeber'); ?></h4>
			<?php echo Alpaca_User::random(); ?>
		</div>
		<div id="novices">
			<h4><?php echo __('new members'); ?></h4>
			<?php echo Alpaca_User::new_members(); ?>
		</div>
	</div>
</div>a
<div class="widget">
	<h3><?php echo __('社区成员'); ?></h3>
	<div class="content">
		<div id="star_member">
			<h4><?php echo __('随机成员'); ?></h4>
			<?php echo Alpaca_User::get_random(); ?>
		</div>
		<div id="novices">
			<h4><?php echo __('最新加入成员'); ?></h4>
			<?php echo Alpaca_User::get_recruits(); ?>
		</div>
	</div>
</div>
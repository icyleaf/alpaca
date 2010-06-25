<?php if (isset($topic_actions) > 0): ?>
<div class="admin-panel">
	<h4><?php echo __('Topic actions'); ?></h4>
	<ul class="actions">
		<?php foreach ($topic_actions as $item): ?>
		<li><?php echo $item; ?></li>
		<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>

<div class="topic" id="topic-<?php echo $topic->id; ?>">
	<h2>
		<?php //TODO: echo HTML::image('media/images/star_empty.png'); ?>
		<?php echo $topic->title; ?>
	</h2>
	
	<div class="owner">
		<div class="details">
			<span class="avatar"><?php echo $topic->user_avatar; ?></span>
			<span class="author"><?php echo $topic->author_link; ?></span>
			<span class="date"><?php echo $topic->created; ?></span>
			<div class="clear"></div>
		</div>
		<div class="topic-content"><?php echo $topic->content; ?></div>
	</div>
</div><!-- /topic -->

<div class="options line txt_right">
	<a href="javascript:window.scrollTo(0,0);"><?php echo __('Top Back'); ?></a>
	 | 
	<a href="#reply"><?php echo __('Reply Topic'); ?></a>
</div><!-- /options -->

<!-- topic's replies -->
<?php echo $topic_posts; ?>
<!-- /topic's replies -->

<!-- reply form -->
<?php echo $write_post;?>
<!-- /reply form -->
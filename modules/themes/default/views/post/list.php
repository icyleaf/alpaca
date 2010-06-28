<?php if ($post_count > 0): ?>
<?php foreach ($posts as $post): ?>
<div id="post-<?php echo $post->id ?>" class="post">
	<div class="meta <?php echo $post->role; ?>">
		<?php if (count($post->actions) > 0): ?>
			<ul class="actions right">
			<?php foreach ($post->actions as $item): ?>
				<li><?php echo $item; ?></li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<ul id="details-<?php echo $post->id ?>" class="details left">
			<li class="author"><?php echo $post->avatar.$post->author; ?></li>
			<li class="date">
			<?php echo HTML::anchor('#post-'.$post->id, $post->time_ago, array(
				'name'	=> 'post-'.$post->id,
				'title'	=> $post->created,
			)); ?>
			</li>
		</ul>
		<div class="clear"></div>
	</div>

	<div class="post-content">
		<?php echo $post->content; ?>
	</div>
</div>
<?php endforeach; ?>
<?php echo $pagination->render(); ?>
<?php endif; ?>
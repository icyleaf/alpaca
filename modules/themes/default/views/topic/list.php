<?php if (isset($head)): ?>
<h3 id="topic-title" class="<?php echo $head['class']; ?>">
	<?php if (isset($topic_sort)): ?>
	<ul class="right">
	<?php foreach ($topic_sort as $uri => $title): ?>
	<li>
		<?php if ($uri != Request::current()->uri): ?>
		<?php echo HTML::anchor($uri, $title); ?>
		<?php endif; ?>
	</li>
	<?php endforeach; ?>
	</ul>
	<?php endif ?>
	<div class="left"><?php echo $head['title']; ?></div>
	<div class="clear"></div>
</h3>
<?php endif ?>

<?php if (count($topics) > 0): ?>
<ul id="topic-list" class="list">
<?php foreach ($topics as $topic): ?>
	<li class="clearfix topic_<?php echo $topic->id ?>">
		<?php echo $topic->author->avatar; ?>
		<div class="collection">
			<div class="collection_inset">
				<?php if ( ! isset($hide_group)): ?>
				<?php echo HTML::anchor($topic->group->link, $topic->group->name, array('class' => 'groups')); ?>
				<?php endif; ?>
			</div>
		</div>

		<div class="topic_details">
			<?php echo HTML::anchor($topic->link, $topic->title, array('class' => 'subject'));?>
			<div class="meta">
				<?php echo HTML::anchor($topic->author->link, $topic->author->nickname, array('class' => 'author')); ?>
				<span class="divider">•</span>
				<?php if ($topic->comments > 1): ?>
				<?php echo __(':number replies', array(':number' => $topic->comments)) ?>
				<?php else: ?>
				<?php echo __(':number reply', array(':number' => $topic->comments)) ?>
				<?php endif ?>
				<span class="divider">•</span>
				<?php if ($topic->hits > 1): ?>
				<?php echo __(':number hits', array(':number' => $topic->hits)) ?>
				<?php else: ?>
				<?php echo __(':number hit', array(':number' => $topic->hits)) ?>
				<?php endif ?>
				<span class="divider">•</span>
				<?php if ($topic->collections > 1): ?>
				<?php echo __(':number collections', array(':number' => $topic->collections)) ?>
				<?php else: ?>
				<?php echo __(':number collection', array(':number' => $topic->collections)) ?>
				<?php endif ?>
				<span class="divider">•</span>
				<?php echo $topic->updated; ?>
			</div>
		</div>
	</li>
<?php endforeach; ?>
</ul>
<?php elseif (isset($group)): ?>
	<?php echo __('Nothing here, :post.', array(
		':post' => HTML::anchor(Route::url('topic/add', array('id' => $group->id)), __('post a new topic'))
		)); ?>
<?php else: ?>
<ul class="list"><?php echo __('Nothing here'); ?></ul>
<?php endif; ?>

<?php if (isset($pagination)): ?>
<?php echo $pagination->render(); ?>
<?php endif; ?>
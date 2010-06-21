<?php if (isset($head)): ?>
<h3 class="<?php echo $head['class']; ?>"><?php echo $head['title']; ?></h3>
<?php endif ?>

<?php $count = is_array($topics) ? count($topics) : $topics->count();
if ($count > 0): ?>
<ul class="list">
<?php foreach ($topics as $topic):
	// Topics by user posted comments is just a Object or not Model.
	$author = isset($topic->author) ? $topic->author : ORM::factory('user', $topic->user_id);
	$group = isset($topic->group) ? $topic->group : ORM::factory('group', $topic->group_id);
?>
	<li class="clearfix topic_<?php echo $topic->id ?>">
		<?php echo Alpaca_User::avatar($author, array('size' => 30), array('class' => 'avatar'), TRUE);?>
		<div class="collection">
			<div class="collection_inset">
				<?php
				if ( ! isset($hide_group)):
					echo HTML::anchor(Route::get('group')->uri(array('id' => Alpaca_Group::the_uri($group))),
						$group->name,
						array('class' => 'groups')
						);
				endif; ?>
				<div class="collection_action">
					<?php
					$tips_1 = __(':number people collected this!', array(':number' => $topic->collections));
					$tips_2 = __('view who collected this!');
					$colletion_url = Route::get('topic/collectors')->uri(array('topic_id' => $topic->id));
					$style = 'empty_star';
					$collection = 'false';
					if ($user = $auth->get_user())
					{
						if (ORM::factory('collection')->is_collected($topic->id, $user->id))
						{
							$style = 'star';
						}
						else
						{
							$collection = 'true';
							$colletion_url = URL::site('collection/topic/'.$topic->id);
							$tips_2 = __('Click :image to add your collection! ', array(
								':image' => HTML::image('media/images/mini_star.png', array('alt'=>'*'))
								));
						}
					}
					?>
					<div class="collection_tips hidden">
						<strong><?php echo $tips_1; ?></strong>
						<?php echo $tips_2; ?>
					</div>
					<?php
					echo HTML::anchor(
						$colletion_url,
						HTML::image('media/images/sprite_screen.png', array(
							'class' => $style,
							'alt'=>'*'
							)), array(
							'id'    => $topic->id,
							'class' => 'collection_link',
							'rel'   => $collection
							)
						); ?>
				</div>
			</div>
		</div>

		<div class="topic_details">
			<?php echo HTML::anchor(Route::get('topic')->uri(array('id' => $topic->id)),
				$topic->title, array('class' => 'subject')); ?>
			<div class="meta">
			<?php echo HTML::anchor(Route::get('user')->uri(array('id' => Alpaca_User::the_uri($author))),
				$author->nickname, array('class'=>'author')); ?>
				<span class="divider">•</span>
				<?php if ($topic->count > 1): ?>
				<?php echo __(':number replies', array(':number' => $topic->count)) ?>
				<?php else: ?>
				<?php echo __(':number reply', array(':number' => $topic->count)) ?>
				<?php endif ?>
				<span class="divider">•</span>
				<?php if ($topic->hits > 1): ?>
				<?php echo __(':number hits', array(':number' => $topic->hits)) ?>
				<?php else: ?>
				<?php echo __(':number hit', array(':number' => $topic->hits)) ?>
				<?php endif ?>
				<span class="divider">•</span>
				<?php echo Alpaca::time_ago($topic->updated); ?>
			</div>
		</div>
	</li>
<?php endforeach; ?>
</ul>
<?php elseif (isset($group)): ?>
	<?php echo __('Nothing here, :post.', array(
		':post' => HTML::anchor(Route::get('topic/add')->uri(array('id' => $group->id)), __('post a new topic'))
		)); ?>
<?php else: ?>
<ul class="list"><?php echo __('Nothing here'); ?></ul>
<?php endif; ?>
<?php if (isset($pagination)){ echo $pagination->render();} ?>
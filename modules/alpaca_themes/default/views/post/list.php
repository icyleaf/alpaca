<?php 
	$post_num = $pagination->offset + 1;
	$auth_user = $auth->get_user();
?>
<?php if ($post_count > 0): ?>
	<h3 class="blue">
	<?php if ($post_count > 1): ?>
	<?php echo __('(:number) replies', array(':number' => $post_count)) ?>
	<?php else: ?>
	<?php echo __('(:number) reply', array(':number' => $post_count)) ?>
	<?php endif ?>
</h3>
<?php foreach ($posts as $post): ?>
<div id="post-<?php echo $post->id ?>" class="post">
	<div class="function right">
		<?php $author = $post->author;
		if ($auth_user AND ($auth_user->id == $author->id OR 
			$auth_user->has('roles', ORM::factory('role', array('name' => 'admin'))))):
		?>
		<span class="action">
			<?php 
			echo html::anchor('post/delete/'.$post->id, __('Delete'), array(
				'class'	=> 'delete', 
				'title'	=> __('Delete Reply'),
				'rel'	=> __('Do you really want to delete this reply?'),
				));
			echo html::anchor('post/edit/'.$post->id, __('Edit'), array(
				'class'	=> 'edit', 
				'title'	=> __('Edit Reply'),
				)); 
			?>
		</span>
		<?php endif; ?>
		<?php
			/*
			echo html::anchor('post/reply/'.$post->topic->id.'/'.$post->id, __('Reply'), array(
				'class'	=> 'edit', 
				'title'	=> __('Reply'),
				));
			*/	
		?>
		<?php echo '<a href="#post-'.$post->id.'" name="post-'.$post->id.'">#'.$post_num.'</a>'; ?>
	</div>
	<ul id="details-<?php echo $post->id ?>" class="details">
		<li class="author">
			<?php
				$avatar_config = array
				(
					'default'	=> URL::site('media/images/user-default-small.jpg'),
					'size'		=> 30
				);
				// Display author avatar
				echo Alpaca_User::avatar($author, $avatar_config, array('id' => 'avatar-'.$post->id, 'class' => 'avatar', TRUE));
				
				// Author name with anchor
				$style = ($auth_user AND $auth_user->id == $author->id) ? 'owner' : 'poster';
				echo html::anchor(Route::get('user')->uri(array(
					'id' => Alpaca_User::the_uri($author))), 
					$author->nickname, array('class' => $style)); 
			?>
		</li>
		<li class="date"><?php echo date($config->date_format, $post->created); ?></li>
	</ul>
	<div class="clear"></div>
	<div class="post-content">
		<?php echo Alpaca::format_html($post->content); ?>
	</div>
	<div class="action txt_right">

	</div>
	<?php 
	$replies = $post->replies->find_all(); 
	if ($replies->count() > 0)
	{
		echo '<ul class="post-comment">';
		foreach($replies as $reply)
		{
			echo '<li>';
			echo html::anchor('#', $reply->author->nickname) . ': ';
			echo $reply->content . ' (' . Alpaca::time_ago($reply->created) . ')';
			echo '</li>';
		}
		echo '</ul>';
	}
	?>
</div>
<?php $post_num++; ?>
<?php endforeach; ?>
<?php echo $pagination->render(); ?>
<?php endif; ?>
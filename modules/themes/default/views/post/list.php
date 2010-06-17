<?php 
	$post_num = $pagination->offset + 1;
	$auth_user = $auth->get_user();
	$topic_user = $topic->author;
?>
<?php if ($post_count > 0): ?>
<?php foreach ($posts as $post): ?>
<div id="post-<?php echo $post->id ?>" class="post">
	<?php
		$author = $post->author;
		// Author name with anchor
		$style = ($topic_user->id == $author->id) ? ' owner' : ' poster';
	?>

	<div class="meta<?php echo $style; ?>">
	<?php if ($auth_user AND ($auth_user->id == $author->id OR
		$auth_user->has('roles', ORM::factory('role', array('name' => 'admin'))))):
	?>
	<ul class="actions right">
		<li><?php echo HTML::anchor('post/delete/'.$post->id, __('Delete'), array(
			'class'	=> 'delete',
			'title'	=> __('Delete Reply'),
			'rel'	=> __('Do you really want to delete this reply?'),
			)); ?>
		</li>
		<li><?php echo HTML::anchor('post/edit/'.$post->id, __('Edit'), array(
			'class'	=> 'edit',
			'title'	=> __('Edit Reply'),
			)); ?>
		</li>
	</ul>
	<?php endif; ?>
		
	<ul id="details-<?php echo $post->id ?>" class="details left">
		<li class="author">
			<?php
				$avatar_config = array
				(
					'default'	=> URL::site('media/images/user-default-small.jpg'),
					'size'		=> 30
				);
				// Display author avatar
				echo Alpaca_User::avatar($author, $avatar_config, array('id' => 'avatar-'.$post->id, 'class' => 'avatar', TRUE));
				
				echo HTML::anchor(
					Route::get('user')->uri(array('id' => Alpaca_User::the_uri($author))),
					$author->nickname); 
			?>
		</li>
		<li class="date">
			<?php echo '<a href="#post-'.$post->id.'" name="post-'.$post->id.'">'.
					date($config->date_format, $post->created).'</a>'; ?>
		</li>
	</ul>
	<div class="clear"></div>
	</div>
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
			echo HTML::anchor('#', $reply->author->nickname) . ': ';
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
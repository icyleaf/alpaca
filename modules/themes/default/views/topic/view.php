<?php
$auth_user = $auth->get_user();
$author = $topic->author;
?>
<?php if ($auth_user AND $auth_user->has('roles', ORM::factory('role', array('name'=>'admin')))): ?>
<div class="admin-panel">
	<h4><?php echo __('Admin functions'); ?></h4>
	<ul class="actions">
		<li>
			<?php echo HTML::anchor('topic/edit/'.$topic->id, __('Edit'), array(
				'class'	=> 'edit', 
				'title'	=> __('Edit Topic'),
				)); ?>
		</li>
		<li>
			<?php echo HTML::anchor('topic/delete/'.$topic->id, __('Delete'), array(
				'class'	=> 'delete', 
				'title'	=> __('Delete this topic include all the replies'),
				'rel'	=> __('[NOT UNDO] Do you really want to delete this topic include all the replies?'),
				));?>
		</li>
		<li>
			<?php echo HTML::anchor('topic/move/'.$topic->id, __('Move'), array('title'	=> __('Move to other group'),));?>
		</li>
	</ul>
</div>
<?php endif; ?>

<div class="topic">
	<h2>
		<?php echo HTML::image('media/images/star_empty.png'); ?>
		<?php echo $topic->title; ?>
	</h2>
	
	<div class="owner">
		<div class="details">
		<?php 
			if ($author->loaded() AND ! empty($author->email))
			{
				$avatar = Gravatar::instance($author->email, array(
					'default' => URL::base().'media/images/user-default.jpg'
				));
			}
			else
			{
				$avatar = 'media/images/user-default.jpg';
			}
			
			echo '<span class="avatar">'.HTML::image($avatar).'</span>';
			echo '<span class="author">'.
				HTML::anchor(Route::get('user')->uri(array('id'=>Alpaca_User::the_uri($author))),
				$author->nickname).'</span>';
			echo '<span class="date">'.date($config->date_format, $topic->created).'</span>';
		?>
		<div class="clear"></div>
		</div>
		
		<div class="clear"></div>
		<div class="topic-content">
			<?php echo Alpaca::format_html($topic->content); ?>
		</div>
		
		<div class="action txt_right">
		<?php 
		if ($auth_user AND ($auth_user->id == $author->id) AND ! $auth_user->has('roles', ORM::factory('role', array('name'=>'admin'))))
		{
			echo HTML::anchor('topic/delete/'.$topic->id, __('Delete'), array(
				'class'	=> 'button delete', 
				'title'	=> __('Delete this topic include all the replies'),
				'rel'	=> __('[NOT UNDO] Do you really want to delete this topic include all the replies?'),
				));
			echo HTML::anchor('topic/edit/'.$topic->id, __('Edit'), array(
				'class'	=> 'button edit', 
				'title'	=> __('Edit Topic'),
				));	
		}	
		?>
		</div>
	</div>
</div><!-- topic -->

<div class="options line txt_right">
	<a href="javascript:window.scrollTo(0,0);"><?php echo __('Top Back'); ?></a>
	 | 
	<a href="#reply"><?php echo __('Reply Topic'); ?></a>
</div>

<?php echo $topic_posts; ?>
<div class="options">
<?php echo __('Wanna say something?'); ?>
</div>
<?php echo $write_post; ?>
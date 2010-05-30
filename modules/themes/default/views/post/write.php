<?php
$author = $auth->get_user();
?>
<a name="reply"></a>
<h3 class="blue"><?php echo __('Post new reply'); ?></h3>
<?php if ($auth->logged_in()): ?>
<form method="post" action="<?php echo Route::get('forum')->uri(array('controller' => 'post', 'action' => 'add')); ?>">
<div id="write_post">
	<input type="hidden" name="topic_id" value="<?php echo $topic->id; ?>" />
	<input type="hidden" name="user_id" value="<?php echo $author->id; ?>" />
	<textarea class="content" name="content" rows="5"></textarea>
</div>
<div class="txt_right">
	<input id="enable_sogou" type="checkbox" tabindex="90" value="true"/>
	<span class="tips"><?php echo __('Enable Sogou Cloud IME'); ?></span>
	<input class="button_submit" type="submit" value="<?php echo __('Reply it'); ?>" />
</div>
</form>
<?php else: ?>
<form method="post" action="<?php echo URL::site(Route::get('login')->uri()); ?>">
<div class="tips"><?php echo __('Post new reply after log in'); ?></div>
<input type="hidden" name="redir" value="<?php echo URL::site(Request::current()->uri); ?>" />
<table>
	<tr>
		<td class="txt_right"><?php echo __('Email'); ?></td>
		<td><input type="text" name="email" value="" /></td>
		<td rowspan="2"><input class="button_auth" type="submit" value="<?php echo __('Log in'); ?>" /></td>
	</tr>
	<tr>
		<td class="txt_right"><?php echo __('Password'); ?></td>
		<td><input type="password" name="password" value="" /></td>
	</tr>
</table>
</form>
<?php endif ?>

<div class="options line" style="margin-top: 15px">
	<div class="txt_right">
		<?php echo html::anchor(url::base(), __('Home Back')); ?>
		 | 
		<?php echo html::anchor(Route::get('group')->uri(array('id' => $topic->group->id)), $topic->group->name); ?>
		 | 
		<a href="javascript:window.scrollTo(0,0);"><?php echo __('Top Back'); ?></a>
	</div>
</div>

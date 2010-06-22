<?php $author = $auth->get_user(); ?>
<div class="comments">
	<a name="reply"></a>
	<h3><?php echo __('Post new reply'); ?></h3>
	<?php if ($auth->logged_in()): ?>
	<div class="tips" style="padding-left: 7px"><?php echo __('Wanna say something?'); ?></div>
	<form method="post" action="<?php echo Route::get('forum')->uri(array('controller' => 'post', 'action' => 'add')); ?>">
	<input type="hidden" name="topic_id" value="<?php echo $topic->id; ?>" />
	<input type="hidden" name="user_id" value="<?php echo $author->id; ?>" />
	<div class="comments-body">
		<textarea class="content" name="content" rows="5"></textarea>
		<div class="comments-tools">
		<?php 
			echo HTML::anchor('#', __('Formatting help')).
				__(' or ').
				HTML::anchor('#', __('Preview'));
		?>
		</div>
	</div>
	<div class="comments-actions txt_right">
		<input id="enable_sogou" type="checkbox" tabindex="90" value="true"/>
		<span class="tips"><?php echo __('Enable Sogou Cloud IME'); ?></span>
		<input type="submit" value="<?php echo __('Reply it'); ?>" />
	</div>
	</form>
	<?php else: ?>
	<div class="tips" style="padding-left: 7px"><?php echo __('Post new reply after log in'); ?></div>
	<form method="post" action="<?php echo URL::site(Route::get('login')->uri()); ?>">
	<input type="hidden" name="redir" value="<?php echo URL::site(Request::current()->uri); ?>" />
	<div class="comments-body">
	<table>
		<tr>
			<td><label><?php echo __('Email'); ?></label></td>
			<td><input type="text" name="email" value="" tabindex="100" /></td>
		</tr>
		<tr>
			<td><label><?php echo __('Password'); ?></label></td>
			<td><input type="password" name="password" value=""  tabindex="101" /></td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="checkbox" tabindex="102" value="true" name="remember"/>
				<span class="tips"><?php echo __('Remember me'); ?></span>
				(<?php echo HTML::anchor('lostpassword', __('Lost Password')); ?>)
			</td>
		</tr>
	</table>
	</div>
	<div class="comments-actions txt_right">
		<input type="submit" tabindex="103"  value="<?php echo __('Log in'); ?>" />
	</div>
	</form>
	<?php endif ?>
</div>

<div class="options">
	<div class="txt_right">
		<?php echo HTML::anchor(URL::base(), __('Home Back')); ?>
		 | 
		<?php echo HTML::anchor(Route::get('group')->uri(array('id' => $topic->group->id)), $topic->group->name); ?>
		 | 
		<a href="javascript:window.scrollTo(0,0);"><?php echo __('Top Back'); ?></a>
	</div>
</div>

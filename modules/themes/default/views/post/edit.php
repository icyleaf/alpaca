<?php
$content_error = isset($errors['content'])?$errors['content']:'';
?>
<h3 class="blue"><?php echo __('Edit Reply'); ?></h3>
<form method="post" class="form_table">
<table class="table">
	<tr>
		<td class="column"><label><?php echo __('Title'); ?>:</label></td>
		<td>
			<input type="text" id="title" value="<?php echo $post->topic->title; ?>" readonly />
		</td>
	</tr>
	<tr>
		<td class="column"><label><?php echo __('Reply'); ?>:</label></td>
		<td>
			<textarea name="content" class="content" cols="55" rows="40"><?php echo $post->content; ?></textarea>
			<?php echo $content_error; ?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<?php echo (!empty($message))?'<br /><center><b>'.$message.'</b></center><br />':''; ?>
			<span class="right">
				<input id="enable_sogou" type="checkbox" tabindex="90" value="true"/>
				<span class="tips"><?php echo __('Enable Sogou Cloud IME'); ?></span>
				<input class="button_submit" type="submit" value="<?php echo __('Edit Reply'); ?>" />
			</span>
			<span class="left">
				<?php echo HTML::anchor(Route::get('topic')->uri(array(
						'group_id' => Alpaca_Group::the_uri($post->topic->group),
						'id' => $post->topic->id
					)),
					__('Undo'),
					array('class' => 'button'));
				?>
			</span>
		</td>
	</tr>
</table>
</form>
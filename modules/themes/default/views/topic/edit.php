<?php
$author = $auth->get_user();

$title = isset($_POST['title'])?$_POST['title']:$topic->title;
$content = isset($_POST['content'])?$_POST['content']:$topic->content;

$title_error = isset($errors['title'])?$errors['title']:'';
$content_error = isset($errors['content'])?$errors['content']:'';
?>
<h3 class="hot"><?php echo __('Edit Topic'); ?></h3>
<form method="post" class="form_table">
<table class="table">
	<tr>
		<td class="column"><label><?php echo __('Title'); ?>:</label></td>
		<td>
			<input type="text" id="title" name="title" value="<?php echo Arr::get($_POST, 'title', $topic->title); ?>" />
			<span class="error"><?php echo Arr::get($errors, 'title'); ?></span>
		</td>
	</tr>
	<tr>
		<td class="column" valign="top">
			<?php echo Alpaca_User::avatar($author, NULL, TRUE, TRUE); ?>
		</td>
		<td>
			<textarea name="content" class="content" cols="55" rows="40"><?php echo Arr::get($_POST, 'content', $topic->content); ?></textarea>
			<span class="error"><?php echo Arr::get($errors, 'content'); ?></span>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<span class="right">
				<input id="enable_sogou" type="checkbox" tabindex="90" value="true"/>
				<span class="tips"><?php echo __('Enable Sogou Cloud IME'); ?></span>
				<input class="button_submit" type="submit" value="<?php echo __('Update'); ?>" />
			</span>
			<span class="left">
				<?php echo HTML::anchor(Alpaca_Topic::the_url($topic), __('Undo'), array('class' => 'button'));?>
			</span>
		</td>
	</tr>
</table>
</form>
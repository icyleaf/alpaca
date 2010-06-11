<?php
$author = $auth->get_user();

$title = Arr::get($_POST, 'title');
$content = Arr::get($_POST, 'content');

$title_error = Arr::get($errors, 'title');
$content_error = Arr::get($errors, 'content');
?>
<h3 class="hot"><?php echo $group->name.__(' New Topic'); ?></h3>
<form method="post" class="form_table">
<table class="table">
	<tr>
		<td class="column"><label><?php echo __('Title'); ?>:</label></td>
		<td>
			<input type="text" name="title" id="title" tabindex="10" value="<?php echo $title; ?>" />
			<?php echo $title_error; ?>
		</td>
	</tr>
	<tr>
		<td class="column" valign="top">
			<?php echo Alpaca_User::avatar($author, NULL, TRUE, TRUE); ?>
		</td>
		<td>
			<textarea name="content" class="content" cols="55" tabindex="20" rows="40"><?php echo $content; ?></textarea>
			<?php echo $content_error; ?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<?php echo (!empty($message))?'<br /><center><b>'.$message.'</b></center><br />':''; ?>
			<input type="hidden" name="group_id" value="<?php echo $group->id; ?>" />
			<input type="hidden" name="user_id" value="<?php echo $author->id; ?>" />
			<span class="right">
				<input id="enable_sogou" type="checkbox" tabindex="90" value="true"/>
				<span class="tips"><?php echo __('Enable Sogou Cloud IME'); ?></span>
				<input type="submit" class="button_submit" tabindex="30" value="<?php echo __('Post it!'); ?>" />
			</span>
		</td>
	</tr>
</table>
</form>
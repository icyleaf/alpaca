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
			<input type="text" id="title" name="title" value="<?php echo $title; ?>" />
			<?php echo $title_error; ?>
		</td>
	</tr>
	<tr>
		<td class="column" valign="top">
			<?php echo Alpaca_User::avatar($author, NULL, TRUE, TRUE); ?>
		</td>
		<td>
			<textarea name="content" class="content" cols="55" rows="40"><?php echo $content; ?></textarea>
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
				<input class="button_submit" type="submit" value="<?php echo __('Update'); ?>" />
			</span>
			<span class="left">
				<?php echo HTML::anchor(Route::url('topic', array(
						'group_id' => Alpaca_Group::the_uri($topic->group),
						'id' => $topic->id
					)),
					__('Undo'), 
					array('class' => 'button'));
				?>
			</span>
		</td>
	</tr>
</table>
</form>
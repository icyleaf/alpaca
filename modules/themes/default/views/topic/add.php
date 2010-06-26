<h3 class="hot"><?php echo $group->name.__(' New Topic'); ?></h3>
<form method="post" class="form_table">
<table class="table">
	<tr>
		<td class="column"><label><?php echo __('Title'); ?>:</label></td>
		<td>
			<input type="text" name="title" id="title" tabindex="10" value="<?php echo Arr::get($_POST, 'title'); ?>" />
			<span class="error"><?php echo Arr::get($errors, 'title'); ?></span>
		</td>
	</tr>
	<tr>
		<td class="column" valign="top">
			<?php echo Alpaca_User::avatar($author, NULL, TRUE, TRUE); ?>
		</td>
		<td>
			<textarea name="content" class="content" cols="55" tabindex="20" rows="40"><?php echo Arr::get($_POST, 'content'); ?></textarea>
			<span class="error"><?php echo Arr::get($errors, 'content'); ?></span>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<span class="right">
				<input id="enable_sogou" type="checkbox" tabindex="90" value="true"/>
				<span class="tips"><?php echo __('Enable Sogou Cloud IME'); ?></span>
				<input type="submit" class="button_submit" tabindex="30" value="<?php echo __('Post it!'); ?>" />
			</span>
		</td>
	</tr>
</table>
</form>
<?php
	$name = Arr::get($_POST, 'name');
	$uri = Arr::get($_POST,'uri');
	$desc = Arr::get($_POST,'desc');
	
	$name_error = Arr::get($errors, 'name');
	$name_uri = Arr::get($errors, 'uri');
	$desc_error = Arr::get($errors, 'desc');
?>
<h3 class="hot"><?php echo $title; ?></h3>
<form method="post" class="form_table">
<table class="table">
	<tr>
		<td class="column"><label><?php echo __('Name'); ?>:</label></td>
		<td>
			<input id="name" name="name" type="text" tabindex="10" value="<?php echo $name; ?>" />
			<?php echo $name_error; ?>
		</td>
	</tr>
	<tr>
		<td class="column"><label><?php echo __('URI'); ?>:</label></td>
		<td>
			<input id="uri" name="uri" type="text" tabindex="10" value="<?php echo $uri; ?>" />
			<?php echo $name_error; ?>
		</td>
	</tr>
	<tr>
		<td class="column"><label><?php echo __('Category'); ?>:</label></td>
		<td>
			<select name="level">
				<option value="0" selected>None</option>
				<?php
					if ($groups->count() > 0)
					{
						foreach ($groups as $group)
						{
							echo '<option value="'.$group->id.'">'.$group->name.'</option>';
						}
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="column" valign="top">
			<?php echo Alpaca_User::avatar($auth->get_user(), NULL, TRUE, TRUE); ?>
		</td>
		<td>
			<textarea name="desc" class="content" cols="55" tabindex="20" rows="40"><?php echo $desc; ?></textarea>
			<?php echo $desc_error; ?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<?php echo (!empty($message))?'<br /><center><b>'.$message.'</b></center><br />':''; ?>
			<input type="submit" class="button_submit" tabindex="30" value="<?php echo __('Create'); ?>" />
			
			<input id="enable_sogou" type="checkbox" tabindex="90" value="true"/>
			<span class="tips"><?php echo __('Enable Sogou Cloud IME'); ?></span>
		</td>
	</tr>
</table>
</form>


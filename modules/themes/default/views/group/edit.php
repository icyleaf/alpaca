<?php
	$name = isset($_POST['name'])?$_POST['name']:$group->name;
	$desc = isset($_POST['desc'])?$_POST['desc']:$group->desc;
	
	$name_error = isset($errors['name'])?$errors['name']:'';
	$desc_error = isset($errors['desc'])?$errors['desc']:'';
?>
<h3 class="hot"><?php echo $title; ?></h3>
<form method="post">
<table class="table">
	<tr>
		<td class="column"><label><?php echo __('名称'); ?>:</label></td>
		<td>
			<input id="name" name="name" type="text" tabindex="10" value="<?php echo $name; ?>" />
			<?php echo $name_error; ?>
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
			<input type="submit" class="button_submit" tabindex="30" value="<?php echo __('编辑完成'); ?>" />
			
			<input id="enable_sogou" type="checkbox" tabindex="90" value="true"/>
			<span class="tips"><?php echo __('启用搜狗云输入法'); ?></span>
		</td>
	</tr>
</table>
</form>
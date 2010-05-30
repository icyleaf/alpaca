<h2><?php echo $title; ?></h2>

<?php if ($status): ?>
<div id="tips" class="<?php echo $status['type']; ?>">
	<?php echo $status['content']; ?>
	<span class="right"><a href="javascript:void(0);" class="close_tips"><?php echo __('Close'); ?></a></span>
</div>
<?php endif ?>

<ul class="settings-menu txt_right">
<?php
$current_uri = Request::current()->uri;
foreach ($links as $link => $title) {
	if ($link == $current_uri)
	{
		echo '<li class="current">'.$title.'</li>';
	}
	else
	{
		echo '<li>'.html::anchor($link, $title).'</li>';
	}
}
?>
</ul>

<div class="settings-content">
<form method="POST">
	<?php echo $body; ?>
</form>
</div>

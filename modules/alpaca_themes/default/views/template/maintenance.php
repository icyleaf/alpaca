<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $config->title; ?></title>
</head>
<body>
<h1><?php echo __('Maintenance Time!'); ?></h1>
<p>
	<img src="http://tpg.dvdtiefpreise.com/wp-content/uploads/2008/03/403_forbidden.jpg" alt="403" />
</p>
<p>
	<?php echo __(':website now is still maintaining. We\'ll come soon, and with new feature possibly.', array(
		':website' => $config->title
		)); ?>
</p>
<hr />
<?php echo $config->project['version']; ?>,
<?php echo $config->project['codename']; ?>,
{execution_time}, {memory_usage}
<?php if (isset($footer)) echo $footer; ?>
<?php if ($config->ga_account_id): ?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("<?php echo $config->ga_account_id; ?>");
pageTracker._trackPageview();
} catch(err) {}</script>
<?php endif ?>
</body>
</html>
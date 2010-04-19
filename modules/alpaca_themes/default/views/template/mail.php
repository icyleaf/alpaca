<?php
if (I18n::$lang == 'zh-cn')
{
	$website_link = Alpaca::beautify_str($config->title, TRUE, TRUE);
	$user_name = Alpaca::beautify_str($user->nickname);
}
else
{
	$website_link = $config->title;
	$user_name = $user->nickname;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $config->title; ?></title>
</head>
<body>
<?php $config = Kohana::config('alpaca'); ?>
<?php echo __('Dear :user', array(':user' => $user_name)); ?>:
<br /><br />
<?php echo $content; ?>
<br /><br />
<?php
echo __('Thanks for support to :website.', array(':website' => $website_link)); 
?>
<br /><br />
/**<br />
 * <?php echo $config->title; ?><br />
 * <br />
 * @url <?php echo html::anchor(URL::site(), URL::site()); ?><br />
 * @version always beta<br />
 * <br />
 * <?php echo $config->desc; ?><br />
 */
<br /><br />
(<?php echo Alpaca::random_footnote(); ?>)
</body>
</html>
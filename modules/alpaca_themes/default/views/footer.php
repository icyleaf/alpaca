<?php if (isset($footer)) echo $footer; ?>

<?php if ($config->debug): ?>
<div id="kohana-profiler">
<?php echo View::factory('profiler/stats') ?>
</div>
<?php endif ?>

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
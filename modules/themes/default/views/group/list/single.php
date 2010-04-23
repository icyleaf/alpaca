<h2 style="background: #FDF1CE; border-top: 1px solid #F4E1A8; border-bottom: 1px solid #F6E9C4">
<?php
	echo Alpaca_Group::image($group, array('style' => 'vertical-align: middle;'));
	echo '<span style="margin-left: 15px">'.$group->name.'</spoan>'; 
?>
</h2>

<?php echo $list_topics; ?>
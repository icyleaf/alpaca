<ul class="list">
<?php foreach ($groups as $group): ?>
	<li>
	<?php 
		$link = Route::url('group', array('id' => Alpaca_Group::uri($group)));
		echo Alpaca_Group::image($group, TRUE, TRUE);
		echo HTML::anchor($link, $group->name);
	?>
	</li>
<?php endforeach; ?>
</ul>

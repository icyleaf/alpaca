<ul class="list">
<?php foreach ($groups as $group): ?>
	<li>
	<?php 
		$link = Route::get('group')->uri(array('id' => Alpaca_Group::the_uri($group)));
		echo Alpaca_Group::image($group, TRUE, TRUE);
		echo html::anchor($link, $group->name); 
	?>
	</li>
<?php endforeach; ?>
</ul>

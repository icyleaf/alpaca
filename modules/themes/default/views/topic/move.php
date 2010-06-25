<h3>正在从 "<?php echo $topic->group->name; ?>" 移动 "<?php echo $topic->title; ?>" 话题至:</h3>
<form method="post" class="form_table">
<?php
$groups = ORM::factory('group')->where('level', '=', 0)->find_all();

if ($groups->count())
{
	echo '<ul class="parent_groups">';
	foreach ($groups as $group)
	{	
		$children = $group->children->find_all();
		if ($children->count()) {
			echo '<li>'.$group->name.'<ul class="groups">';
			foreach ($children as $child)
			{
				$route = Route::url('topic/move', array(
					'topic_id'	=> $topic->id,
					'group_id'	=> $child->id,
					));
				echo '<li>'.HTML::anchor($route, $child->name).'</li>';
			}
			echo '</ul></li>';
		}
	}
	echo '</ul>';
}
else
{
	echo __('没有发现任何板块 :(');
}
?>
</form>
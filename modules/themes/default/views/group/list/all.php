<h3 class="orange hot"><?php echo __('热门小组'); ?></h3>
<ul>
<?php
	$hot_groups = $groups->hot();
	if ($hot_groups->count() > 0)
	{
		foreach ($hot_groups as $group)
		{
			echo '<li><strong>'.$group->name.'</strong> - [ '.$group->parent->name.' ] - '.$group->desc.' ('.$group->hits.')'.'</li>';
			
			/*
			if ( $group->level==0 )
			{
				echo '<li>['.$group->id.'] '.$group->name.' - '.$group->desc.' ('.date('Y-m-d h:i', $group->created).')'.'</li>';
			
				$children = $group->children->find_all();
				if ( $children->count() > 0 )
				{
					echo  '<ul>';
					foreach( $children as $child )
					{
						echo  '<li>['.$child->id.'] '.$child->name.' - '.$child->desc.' ('.date('Y-m-d h:i', $child->created).')'.'</li>';
					}
					echo  '</ul>';
				}
			}
			*/
		}
		echo  '</ul>';
	}
	else
	{
		echo 'Nothing';
	}

?>
</ul>

<h3 class="blue groups"><?php echo __('所有小组'); ?></h3>
<?php
	$all_groups = $groups->find_all();
	if ($all_groups->count() > 0)
	{
		echo '<ul>';
		foreach ($all_groups as $group)
		{
			if ($group->level == 0)
			{
				echo '<li style="clear:both"><span style="font-size: 1.2em;font-weight: bold">'.$group->name.'</span> | '.$group->desc;
			
				$children = $group->children->find_all();
				if ($children->count() > 0)
				{
					echo  '<ul style="list-style-type:none;margin-left: 0;">';
					foreach( $children as $child )
					{
						echo  '<li style="float: left;padding: 2px 5px;margin-right: 3px; border: 1px solid #333333">'.$child->name.'</li>';
					}
					echo  '</ul>';
				}

				echo '</li>';
			}
		}
		echo  '</ul>';
	}
	else
	{
		echo 'Nothing';
	}
?>
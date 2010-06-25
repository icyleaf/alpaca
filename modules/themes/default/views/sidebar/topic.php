<div class="widget">
	<div class="block group">
	<?php 
		echo HTML::anchor(Route::url('group', array('id'=>$topic->group)),
			__('返回:group小组', array(':group' => $topic->group->name)));
	?>
	</div>
</div>
<div class="widget">
	<h3><?php echo __('话题信息'); ?></h3>
	<div class="hightlight center">
		<?php echo __('本话题发起于 :date', array(
			':date'	=> '<span class="number">'.Alpaca::time_ago($topic->created).'</span>'
			)); ?> 
	</div>
	<div class="stats">
	<table>
		<tbody>
			<tr>
				<th><?php echo __('统计'); ?></th>
				<th class="txt_right"><?php echo __('共计'); ?></th>
			</tr>
			<tr>
				<td><?php echo __('浏览数量'); ?></td>
				<td class="txt_right"><?php echo $topic->hits; ?></td>
			</tr>
			<tr>
				<td><?php echo __('回复数量'); ?></td>
				<td class="txt_right"><?php echo $topic->count; ?></td>
			</tr>
			<tr>
				<td><?php echo __('收藏数量'); ?></td>
				<td class="txt_right"><?php echo $topic->collections; ?></td>
			</tr>
		</tbody>
	</table>
	</div>
</div>

<?php echo Alpaca_Group::get_topics($topic->group->id); ?>

<div class="widget">
	<div class="block collection">
	<?php 
		echo HTML::anchor(Route::url('topic/collectors', array(
			'topic_id' => $topic->id)),__('view who collected this!'));
	?>
	</div>
</div>
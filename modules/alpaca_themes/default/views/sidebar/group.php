<?php 
	$topics_cout = $group->topics->find_all()->count();
?>
<div class="widget">
	<div class="block new_topic">
	<?php echo html::anchor(Route::get('topic/add')->uri(array('id' => $group->id)), __('发布新话题'));?>
	</div>
</div>

<div class="widget">
	<h3><?php echo __('小组描述'); ?></h3>
	<div class="content">
		<p>
		<?php if (empty($group->desc)): ?>
			<?php echo __('暂时没有小组描述。'); ?>
		<?php else: ?>
			<?php echo Alpaca::format_html($group->desc); ?>
		<?php endif; ?>
		</p>
	</div>
	
	<div class="hightlight center">
		<?php echo __('本小组创建于 :date', array(
			':date'	=> '<span class="number">'.date(__('Y年m月d日'), $group->created).'</span>'
			)); ?>
	</div>
	
	<div class="stats">
	<table>
		<tbody>
			<tr>
				<th><?php echo __('小组统计'); ?></th>
				<th class="txt_right">共计</th>
			</tr>
			<tr>
				<td><?php echo __('话题数量'); ?></td>
				<td class="txt_right"><?php echo $topics_cout; ?></td>
			</tr>
		</tbody>
	</table>
	</div>
</div>
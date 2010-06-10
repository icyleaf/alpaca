<?php 
	$topics_cout = ORM::factory('topic')->find_all()->count();
	$users_cout = ORM::factory('user')->find_all()->count();
	$groups_cout = ORM::factory('group')->where('level', '=', 1)->find_all()->count();
?>
<div id="community_about" class="widget">
	<h3>关于 Kohana 中文</h3>
	<div class="content">
		<p>
			Kohana 中文是对纯 PHP5 框架 Kohana 的中文推广而建立的交流平台。它是一款基于
			MVC 模式开发，完全社区驱动，具有高安全性，轻量级代码，迅捷开发，轻松上手的特性！ 
		</p>
		<p>
			<strong>快速导航:</strong>
			<ul>
				<li><a href="http://v2.kohana.cn/download">中文框架下载</a></li>
				<li><?php echo HTML::anchor(Route::get('topic')->uri(array('id' => 1000032)), '中文文档'); ?></li>
				<li><?php echo HTML::anchor(URL::base(FALSE), '社区支持'); ?></li>
				<li><?php echo HTML::anchor(Route::get('topic')->uri(array('id' => 1000030)), '群组讨论'); ?></li>
				<li><?php echo HTML::anchor(Route::get('group')->uri(array('id' => 'RD')), '开发合作'); ?></li>
			</ul>
		</p>
	</div>
	<div class="stats">
	<table>
		<tbody>
			<tr>
				<th><?php echo __('社区统计'); ?></th>
				<th class="txt_right"><?php echo __('共计'); ?></th>
			</tr>
			<tr>
				<td><?php echo __('话题数量'); ?></td>
				<td class="txt_right"><?php echo $topics_cout; ?></td>
			</tr>
			<tr>
				<td><?php echo __('注册用户'); ?></td>
				<td class="txt_right"><?php echo $users_cout; ?></td>
			</tr>
			<tr>
				<td><?php echo __('小组数量'); ?></td>
				<td class="txt_right"><?php echo $groups_cout; ?></td>
			</tr>
		</tbody>
	</table>
	</div>
</div>
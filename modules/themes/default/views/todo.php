<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php echo $header; ?>
</head>
<body id="no-sidebar-page">
	<div id="mainHolder">
		<div id="mainHolderGradient">
			<div id="mainContentHolder">			
				<div id="contentsHolder">
					<h1><?php echo HTML::anchor(url::base(), $info['title']) . ' - ' . $info['desc']; ?></h1>
					<p>当前版本号: <?php echo $info['version']; ?></p>
					<p><i>最后更新时间: <?php echo date('Y-m-d', $list->last_updated()); ?></i></p>
					<div class="post">
						<h2 class="title" style="float: none;">完成列表</h2>
						<ul id="finished">
						<?php 
						$finished_todos = $list->todo_list('done', 0, 'DESC');
						if ( $finished_todos->count()>0 )
						{
							foreach ($finished_todos as $todo) {
							 	echo '<li>';
							 	if ( !empty($todo->type) )
								{
									echo '<i>'.$todo->type.'</i> ';
								}
							 	if ( !empty($todo->url) )
								{
									echo HTML::anchor($todo->url, $todo->title);
								}
								else
								{
									echo $todo->title;
								}
							 	echo '</li>';
							} 				
						}
						?>
						</ul>
					</div>
					<div class="post">
						<h2 class="title" style="float: none;">等待列表</h2>
						<p class="small">
							<i>低优先级</i> 
							可能会跳过初期的发布版本，它会在下一个版本实现（下个发布版本优先考虑）。
							主要为了尽快优化当前版本提前发布。
						</p>
						<ul id="pending">
						<?php 
						$pending_todos = $list->todo_list('progress');
						if ( $pending_todos->count()>0 )
						{
							foreach ($pending_todos as $todo) {
							 	echo '<li>';
							 	if ( !empty($todo->type) )
								{
									switch(trim($todo->type))
									{
										case 'low':
											$type = '<i class="low">低优先级</i>';	
											break;
										case 'release':
											$type = '<i class="release">发布</i>';	
											break;
										default:
											$type = '';
									}
									
									echo $type;
								}
							 	if ( !empty($todo->url) )
								{
									echo HTML::anchor($todo->url, $todo->title);
								}
								else
								{
									echo $todo->title;
								}
							 	echo '</li>';
							} 				
						}
						?>
						</ul>
					</div>
					<br class="reset">
				</div>
			</div>
		</div>
	</div>
</body>
</html>

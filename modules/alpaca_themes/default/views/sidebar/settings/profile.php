<div class="widget">
	<h3>username</h3>
	<div class="content">
		<p>
		你现在在<?php echo $config->title; ?>的个人主页是: <br />
		<?php echo URL::site(Route::get('user')->uri(array('id'=>$user->id))); ?>
		</p>
		<p>
			你可以自选一个 username 代替你现在的数字用户ID(<?php echo $user->id; ?>)。
			这样你可以拥有个性化的URL指向你的在<?php echo $config->title; ?>的个人主页。
			比如，如果你用 "alpaca" 作 username，你的个人主页变为: <br />
			  <?php echo URL::site(Route::get('user')->uri(array('id'=>'alpaca'))); ?><br />
		</p>
		
		<p>
			<strong>username只可以设一次，以后不可更改。</strong>这是为了避免别人联接到你的主页时产生坏链接。
			如果现在你不是百分之百的确定，可以暂时留为空，等以后再说。
		</p>
		<p>
			username最长15个字符，可以包含英文字母、数字、和三个符号(. - _)。
			第一个字符必须为英文字母。username不区分大小写。 
		</p>
	</div>
</div>
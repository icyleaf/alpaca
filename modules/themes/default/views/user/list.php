<?php if (isset($title)): ?>
<h3><?php echo $title; ?></h3>
<?php endif ?>
<ul class="list_users">
<?php if ($collections->count() > 0):
foreach ($collections as $collection):
	$user = $collection->user;
	$avatar = Alpaca_User::avatar($user, array('size' => 48), array('class' => 'avatar'), TRUE);
	$nickname = (strlen($user->nickname) > 24) ? substr($user->nickname, 0, 24).'...' : $user->nickname;
	
	echo '<li class="user_item"><div>'.$avatar.'</div>'.
		HTML::anchor(Alpaca_User::the_url('user', $user), $nickname, array('title' => $user->nickname)).'</li>';
endforeach;
endif;?>
</ul>
<div class="clear"></div>
<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * WordPress Pagination Nav Plugin style
 * 
 * @preview  Page 1 of 10 « First Previous 1 2 3 4 5 6 7 8 9 10  Next Last»
 */
?>
<p class="pagination">
<?php
	echo '<span class="pages">'.
		__('Page :current of :total', array(':current'=>$current_page, ':total'=>$total_pages)).
		'</span>';

	if ($first_page !== FALSE)
	{
		echo HTML::anchor($page->url($first_page), __('&laquo; 首页'), array('class'=>'first'));
	}

	if ($previous_page !== FALSE)
	{
		echo HTML::anchor($page->url($previous_page), __('前页'), array('class'=>'previous'));
	}
	
	for ($i = 1; $i <= $total_pages; $i++)
	{
		if ($i == $current_page)
		{
			echo '<span class="current">'.$i.'</span>';
		}
		else
		{
			echo HTML::anchor($page->url($i), $i, array('class'=>'page'));
		}
	}

	if ($next_page !== FALSE)
	{
		echo HTML::anchor($page->url($next_page), __('后页'), array('class'=>'next'));
	}	

	if ($last_page !== FALSE)
	{
		echo HTML::anchor($page->url($last_page), __('末页 &raquo;'), array('class'=>'last'));
	}
?>
</p><!-- /pagination -->
<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Digg pagination style
 * 
 * @preview  « Previous  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  Next »
 */
?>
<div class="pagination">
<?php
	if ($previous_page !== FALSE)
	{
		echo HTML::anchor($page->url($previous_page), __('&laquo;&nbsp;前页'), array('class'=>'previous'));
	}
	
	if ($total_pages < 13)
	{
		/* « Previous  1 2 3 4 5 6 7 8 9 10 11 12  Next » */
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
	}
	elseif ($current_page < 9)
	{
		/* « Previous  1 2 3 4 5 6 7 8 9 10 … 25 26  Next » */
		for ($i = 1; $i <= 10; $i++)
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
		echo '<span class="page">&hellip;</span>';
		echo HTML::anchor($page->url($last_page -1 ), ($last_page - 1), array('class'=>'page'));
		echo HTML::anchor($page->url($last_page), $last_page, array('class'=>'page'));
	}
	elseif ($current_page > $total_pages - 8)
	{
		/* « Previous  1 2 … 17 18 19 20 21 22 23 24 25 26  Next » */
		echo HTML::anchor($page->url(1), 1, array('class'=>'page'));
		echo HTML::anchor($page->url(2), 2, array('class'=>'page'));
		echo '<span class="page">&hellip;</span>';
		for ($i = $total_pages - 9; $i <= $total_pages; $i++)
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
	}
	else
	{
		/* « Previous  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  Next » */
		echo HTML::anchor($page->url(1), 1, array('class'=>'page'));
		echo HTML::anchor($page->url(2), 2, array('class'=>'page'));
		echo '<span class="page">&hellip;</span>';
		for ($i = $current_page - 5; $i <= $current_page + 5; $i++)
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
		echo '<span class="page">&hellip;</span>';
		echo HTML::anchor($page->url($last_page -1 ), ($last_page - 1), array('class'=>'page'));
		echo HTML::anchor($page->url($last_page), $last_page, array('class'=>'page'));
	}
	
	if ($next_page !== FALSE)
	{
		echo HTML::anchor($page->url($next_page), __('后页&nbsp;&raquo;'), array('class'=>'next'));
	}
?>
</div>
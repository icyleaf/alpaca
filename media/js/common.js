$(document).ready(function(){
	var query_tip = $('#header_search_query').val();
	// search
	$('#header_search_query')
	.focus(function(){
		if ($(this).val() == query_tip)
		{
			$(this).val('');
		}
	})
	.blur(function(){
		if ($(this).val() == '')
		{
			$(this).val(query_tip);
		}
	});

	// get focus after nothing to search
	$('#header_search_submit').click(function(){
		var query = $('#header_search_query');
		if (query.val() == query_tip)
		{
			query.val('');
			query.focus();

			return false;
		}
	});
	
	// hidden action functions from per post.
	$('.post .action a').addClass('hidden');
	
	// close tips message
	$('.close_tips').click(function(){
		$('#tips').addClass('hidden');
	});
	
	// Show message before delete
	$('.delete').click(function(){
		if ( ! confirm($(this).attr('rel')))
			return false; 
	});
	
	// Hover effect when mouse over every poster
	$('.post').mouseover(function(){
		post_id = $(this).attr('id').substring($(this).attr('class').length+1);
		$('#avatar-'+post_id).addClass('avatar-hover');
		$('#post-'+post_id+' .action a').removeClass('hidden');
	})
	.mouseout(function(){
		$('#avatar-'+post_id).removeClass('avatar-hover');
		$('#post-'+post_id+' .action a').addClass('hidden');
	});
	
	$('#enable_sogou').click(function(){
		var e=document.createElement('script');
		e.setAttribute('src','http://web.pinyin.sogou.com/web_ime/init.js');
		document.body.appendChild(e);
	});
	
	alpaca.init();
});
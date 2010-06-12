/**
 * Alpaca
 *
 * @author icyleaf <icyleaf.cn@gmail.com>
 * @version 0.1
 */
var alpaca = {
	
	/**
	 * Alpaca init
	 *
	 * @return void
	 */
	init: function()
	{
		// auto resize for textarea
		alpaca.autoresize('textarea.content');
		
		// collections
		alpaca.collection_tips('.collection_action');
		alpaca.add_collection('.collection_link');
	},
	
	/**
	 * Auto resize for textarea
	 *
	 * @param element
	 * @return void
	 */
	autoresize: function(element)
	{
		var e = $(element);
		if (e.length)
		{
			e.autoResize({
				// On resize:
				onResize : function() {
					$(this).css({opacity:0.8});
				},
				// After resize:
				animateCallback : function() {
					$(this).css({opacity:1});
				},
				// Quite slow animation:
				animateDuration : 300
			});
		}
	},
	
	/**
	 * Display collection tips
	 *
	 * @param element 
	 * @return void
	 */
	collection_tips: function(element)
	{
		var e = $(element);
		if (e.length)
		{
			var mover_start;
			e.hover(
				function () {
					clearTimeout(mover_start);
					$(this).find('.collection_tips').fadeIn();
				},
				function () {
					mover_start = setTimeout(function(){
						$(element).find('.collection_tips').fadeOut('slow');
					}, 200);
				}
			);
		}
	},

	/**
	 * Add a topic collection
	 *
	 * @param element 
	 * @return mixed
	 */
	add_collection: function(element)
	{
		var e = $(element);
		if (e.length)
		{
			e.click(function(){
				var e = $(this);
				var id = $(this).attr('id');
				$.ajax({
					type: 'POST',
					url: BASH_URL+'collection/topic/'+id,
					success: function(msg){
						switch(msg)
						{
							case 'CREATED':
								var count = e.find('strong').html();
								count++;
								
								e.find('img').removeClass('empty_star').addClass('star');
								e.find('strong').html(count);
								//alert('创建成功');
								window.location.reload();
								break;
							case 'EXIST':
								alert('已经收藏');
								break;
							case 'NO_AUTH':
								var current_url = window.location;
								var url = BASH_URL+'/login?redir='+current_url;    
								$(location).attr('href',url);
								break;
						}
					}
				}); 
				
				return false;
			});
		}
	},

	/**
	 * Anti SPAM
	 * @param source
	 * @param target
	 * @return void
	 */
	anti_spam: function(source, target)
	{
		$(document).ready(function(){
			var s_input = $('#'+source);
			var t_input = $('#'+target);

			$('form').bind('event', function(){
				t_input.val(s_input.val());
			});
		});
	}

};


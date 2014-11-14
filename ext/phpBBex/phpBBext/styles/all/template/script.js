jQuery(function($) {
	// Global back to top code
	if ($('#back-to-top').length)
	{
		var is_visible = false;
		$(window).scroll(function()
		{
			if ($(this).scrollTop() > 150)
			{
				if (is_visible) return;
				is_visible = true;
				$('#back-to-top').stop(true, true).fadeIn();
			}
			else
			{
				if (!is_visible) return;
				is_visible = false;
				$('#back-to-top').stop(true, true).fadeOut();
			}
		});
		$(window).scroll();

		var is_tower = false;
		$(window).resize(function()
		{
			if ($(document).width() - $('#wrap').width() > 120)
			{
				if (is_tower) return;
				is_tower = true;
				$('#back-to-top').addClass('tower');
			}
			else
			{
				if (!is_tower) return;
				is_tower = false;
				$('#back-to-top').removeClass('tower');
			}
		});
		$(window).resize();

		$('#back-to-top').click(function()
		{
			$('body:not(:animated),html:not(:animated)').animate({ scrollTop: 0 }, 400);
			return false;
		});
	}
});

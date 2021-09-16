(function($)
{
	let $select = $('.wt-select');
	
	if ($select.length && $.fn.select2)
	{
		let isMobile = Modernizr.touchevents || (/android|iphone|ipad|ipod|blackberry|iemobile/i.test(navigator.userAgent.toLowerCase()));
		
		if (!isMobile)
		{
			$select.select2({
				minimumResultsForSearch: 10
			});
		}
	}
	
	
	
	let $checkbox = $('.wt-checkbox');
	
	if ($checkbox.length && Modernizr.appearance)
	{
		$checkbox.after('<i>✓</i>');
	}
	
})(jQuery);

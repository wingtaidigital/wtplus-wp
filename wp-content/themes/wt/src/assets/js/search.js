(function($)
{
	let $titleBar = $('.title-bar');
	let $menu = $('#main-menu');
	let $searchForm = $('#header-search');
	
	$searchForm.on('on.zf.toggler', function()
	{
		$('.icon-search.wt-toggle').removeClass('wt-close');
	});
	
	$searchForm.on('off.zf.toggler', function()
	{
		$('.icon-search.wt-toggle').addClass('wt-close');
		
		document.getElementById('s').focus();
		
		if ($titleBar.is(':visible'))
			$menu.hide();
	});
	
	$titleBar.on('toggled.zf.responsiveToggle', function()
	{
		if ($menu.is(':visible'))
			$searchForm.addClass('hide');
	});
	
})(jQuery);

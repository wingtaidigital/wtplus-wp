(function($)
{
	let $anchors = $('[data-anchor]');
	
	if ($anchors.length)
	{
		let $titles = $('.tabs-title a');
		
		$titles.mouseenter(function()
		{
			let $focus = $titles.filter(':focus');
			
			if ($focus.length)
			{
				// console.log($focus);
				$focus[0].blur();
			}
			
			$(this).trigger('click');
			
			$anchors.removeClass('wt-show');
			$('[data-anchor=' + $(this).attr('href').substring(1) + ']').addClass('wt-show');
		});
		
		$anchors.mouseenter(function()
		{
			$anchors.removeClass('wt-show');
			$('#trigger-' + $(this).data('anchor')).trigger('click');
		});
	}
	
	
	
	let $image = $('#image');
	
	if (!$image.length)
		return;
	
	let imageWidth = $image.data('width');
	
	if (imageWidth === undefined)
		return;
	
	let $markers = $('.wt-marker');
	
	if (!$markers.length)
		return;
	
	function resize()
	{
		Foundation.onImagesLoaded($image, function()
		{
			let width = $image.width();
			
			if (!width)
				return;
			
			if (width == imageWidth)
			{
				$markers.css('transform', 'none');
				return;
			}
			// console.log(width, imageWidth);
			let scale = width / imageWidth;
			
			$markers.css('transform', 'scale(' + scale + ')');
		});
	}
	
	resize();
	
	$image.on('resizeme.zf.trigger', function()
	{
		resize();
	});
	
})(jQuery);

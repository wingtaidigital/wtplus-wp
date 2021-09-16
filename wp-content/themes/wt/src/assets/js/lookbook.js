(function($)
{
	let $images = $('.wt-lookbook');
	
	if (!$images.length)
		return;
	
	function resize()
	{
		Foundation.onImagesLoaded($images, function()
		{
			$images.each(function()
			{
				let $image = $(this);
				let $parent = $image.closest('article');
				
				if (!$parent.length)
					return;
				
				let $products = $parent.find('.wt-absolute > div');
				
				if (!$products.length)
					return;
				
				// if (!Foundation.MediaQuery.atLeast('large'))
				// {
				// 	$products.css('transform', 'none');
				// 	return;
				// }
				
				let width = $image.width();
				
				if (!width)
					return;
				
				let actualWidth = $image.data('width');
				
				if (actualWidth === undefined)
					return;
				
				if (width == actualWidth)
				{
					$products.css('transform', 'none');
					return;
				}
				// console.log(width, actualWidth);
				let scale = width / actualWidth;
				
				$products.css('transform', 'scale(' + scale + ')');
				
				// let height = 0;
				
				
				
				$parent.find('.wt-absolute').css('height', $products.outerHeight());
			});
			
			
			
		});
	}
	
	resize();
	
	$images.on('resizeme.zf.trigger', function()
	{
		resize();
	});
	
})(jQuery);

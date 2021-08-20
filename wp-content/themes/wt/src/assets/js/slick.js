(function($)
{
	if (!$.fn.slick)
		return;
	
	
	
	let $slider = $('.wt-slick');
	
	if ($slider.length)
	{
		function setBackground(slick, slide)
		{
			let $slide = slick.$slides.eq(slide);
			let image = $slide.data('image');
			
			if (image === undefined)
				return;
			
			if (!Foundation.MediaQuery.atLeast('large'))
			{
				let portraitImage = $slide.data('portrait-image');
				
				if (portraitImage !== undefined && portraitImage.length)
					image = portraitImage;
			}
			
			$slider.fadeOut(500, function()
			{
				$slider.css('background-image', 'url(' + image + ')');
				$slider.fadeIn(500);
			});
		}
		
		$slider.on('init', function(event, slick)
		{
			setBackground(slick, 0);
		});
		
		$slider.slick({
			arrows: false,
			dots: true,
			fade: true,
			speed: 2000
		});
		
		$slider.on('beforeChange', function(event, slick, currentSlide, nextSlide)
		{
			setBackground(slick, nextSlide);
		});
		
		$(window).on('changed.zf.mediaquery', function(event, newSize, oldSize)
		{
			setBackground($slider.slick('getSlick'), $slider.slick('slickCurrentSlide'));
		});
	}
	
	
	
	$('.wt-slick-arrows').slick(
	{
		mobileFirst: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		responsive: [
			{
				breakpoint: 640,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 2,
				}
			},
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 4,
					slidesToScroll: 4,
				}
			}
		]
	});
	
	
	
	$('.wt-slick-arrows-single').slick();
	
	
	
	let $pair = $('.wt-slick-left, .wt-slick-right');
	
	if ($pair.length === 2)
	{
		let $left = $('.wt-slick-left');
		let $right = $('.wt-slick-right');
		
		function sync($currentSlider, direction)
		{
			let $slider;
			
			if ($currentSlider.hasClass('wt-slick-left'))
				$slider = $right;
			else
				$slider = $left;
			
			if (direction === 'left')
				$slider.slick('slickNext');
			else
				$slider.slick('slickPrev');
		}
		
		$pair.slick();
		
		$pair.on('swipe', function(event, slick, direction)
		{
			sync(slick.$slider, direction);
		});
		
		$pair.find('.slick-arrow').click(function()
		{
			let direction = 'left';
			
			if ($(this).hasClass('slick-prev'))
				direction = 'right';
			
			sync($(this).closest('.slick-slider'), direction);
		});
	}
	
	// let $left = $('.wt-slick-left');
	//
	// if ($left.length)
	// {
	// 	// $left.slick(
	// 	// {
	// 	// 	asNavFor: '.wt-slick-right'
	// 	// });
	// 	//
	// 	// $('.wt-slick-right').slick(
	// 	// {
	// 	// 	asNavFor: '.wt-slick-left'
	// 	// });
	// }
	
})(jQuery);

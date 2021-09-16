// (function($)
// {
// 	$('.wt-instagram').each(function()
// 	{
// 		let url = $(this).data('url');
//
// 		if (url === undefined)
// 			return true;
//
// 		$.ajax(
// 		{
// 			url: url + 'media/',
// 			type: 'GET',
// 			dataType: 'jsonp',
// 			jsonp: 'callback',
// 			jsonpCallback: 'cb',
// 			crossDomain: true,
// 		})
// 		// .done(function(response)
// 		// {
// 		// 	console.log(response);
// 		// })
// 	});
//
// })(jQuery);

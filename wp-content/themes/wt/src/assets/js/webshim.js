(function($)
{
	if (typeof webshim === 'undefined')
		return;
	
	
	
	webshim.formcfg = {
		en: {
			patterns: {
				d: "dd-mm-yy"
			}
		}
	};
	
	webshim.activeLang("en");
	
	webshim.setOptions({
		// debug: true,
		// enhanceAuto: true,
		// loadStyles: false,
		// 'forms': {
		// 	overrideMessages: true
		// },
		"forms-ext": {
			replaceUI: {date: 'auto'},
			types: "date number",
			date: {
				startView: 2,
				openOnFocus: true,
			}
		}
	});
	
	webshim.polyfill('forms forms-ext');
	
	
	
	// if (Modernizr.inputtypes.date)
	// {
	// 	let $dateFields = $('.wt-date');
	//
	// 	if ($dateFields.length)
	// 	{
	// 		let isMobile = Modernizr.touchevents || (/android|iphone|ipad|ipod|blackberry|iemobile/i.test(navigator.userAgent.toLowerCase()));
	//
	// 		if (isMobile)
	// 		{
	// 			$dateFields.attr('type', 'text');
	//
	// 			$dateFields.focus(function()
	// 			{
	// 				this.type = 'date';
	// 			})
	// 			.blur(function()
	// 			{
	// 				if (this.value == '')
	// 					this.type = 'text';
	// 			});
	// 		}
	// 	}
	// }
	
	
	
		// webshim.ready('form-validation', function ()
		// {
		// 	webshims.validityAlert.hideDelay = false;
		// });
})(jQuery);

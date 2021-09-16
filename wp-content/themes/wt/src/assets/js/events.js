(function($)
{
	let $trigger = $('.wt-event [data-id]');
	
	if (!$trigger.length)
		return;
	
	let id = 0;
	
	$trigger.click(function()
	{
		id = $(this).data('id');
	});
	
	$('#showcase-login-modal').on('open.zf.reveal', function(e)
	{
		let $field = $(this).find('[name="anchor"]');
		
		if ($field.length)
			$field.val('event-' + id);
		
		
		let $link = $(this).find('.wt-registration-url');
		
		if ($link.length)
			$link.attr('href', $link.attr('href') + '?event=' + id);
	});
	
})(jQuery);

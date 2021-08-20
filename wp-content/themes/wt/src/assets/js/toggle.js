(function($)
{
	$('.wt-toggle-check').change(function()
	{
		if (this.checked)
			$(this).closest('.row').find('[type="checkbox"]').prop('checked', true);
		else
			$(this).closest('.row').find('[type="checkbox"]').prop('checked', false);
	});
	
	$('.wt-toggle-disabled').change(function()
	{
		let $parent = $(this).closest('fieldset');
		let $field1 = $parent.next();
		let $field2 = $field1.next();
		
		if ($(this).val() === 'YES')
		{
			$field1.removeClass('hide');
			$field2.addClass('hide');
			$field1.find(':input').prop('disabled', false);
			$field2.find(':input').prop('disabled', true);
		}
		else
		{
			$field1.addClass('hide');
			$field2.removeClass('hide');
			$field1.find(':input').prop('disabled', true);
			$field2.find(':input').prop('disabled', false);
		}
	});
	
	$('.wt-toggle-display').change(function()
	{
		let id = $(this).data('toggle');
		
		if (!id)
			return;
		
		let $ele = $('#' + id);
		
		if (!$ele.length)
			return;
		
		if (this.checked)
			$ele.removeClass('hide');
		else
			$ele.addClass('hide');
	});
	
	
	
	$('.page-profile [name="properties[Children_Age_14]"]').change(function()
	{
		let $children = $('#children');
		
		if ($(this).val() === 'YES')
		{
			$children.removeClass('hide');
			$children.find(':input').prop('disabled', false);
		}
		else
		{
			$children.addClass('hide');
			$children.find(':input').prop('disabled', true);
		}
	});
	
	let $cloneButton = $('.wt-clone');

	if ($cloneButton.length)
	{
		let $template = $('#' + $cloneButton.data('template'));
		let $appendTo = $('#' + $template.data('append-to'));
		let i = $appendTo.find('tr').length;

		$cloneButton.click(function()
		{
			let html = $template.html();
			// console.log(html);
			html = html.replace(/\{\{i\}\}/g, i);
			
			let $clone = $(html);
			$clone.appendTo($appendTo);
			
			if (typeof webshims !== 'undefined')
				$clone.updatePolyfill();

			i++;
		});
	}
	
	$(document.body).on('click', '.wt-remove', function()
	{
		$(this).closest('tr').remove();
	});
	
})(jQuery);

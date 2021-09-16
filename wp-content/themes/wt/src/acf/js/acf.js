(function($)
{
	acf.add_filter('wysiwyg_tinymce_settings', function( mceInit, id, $field )
	{
//				console.log(id);
//				console.log($field);
// 				console.log(mceInit);
		
		// mceInit['block_formats'] = "Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3";
		
		// mceInit['style_formats_merge'] = true;
		// mceInit['style_formats'] = [
		// 	{title: 'Paragraph', block: 'p'},
		// 	{title: 'Heading 2', block: 'h2'},
		// 	{title: 'Heading 3', block: 'h3'},
		// 	{title: 'Heading 4', block: 'h4'},
		// ];
		
		if ($field.hasClass('wt-h1'))
			mceInit['forced_root_block'] = 'h1';
		else if ($field.hasClass('wt-h2'))
			mceInit['forced_root_block'] = 'h2';
//				else if ($field.hasClass('wt-span')) // span hangs browser
//					mceInit['forced_root_block'] = 'span';
		
		return mceInit;
		
	});
	
	acf.add_filter('color_picker_args', function( args, $field )
	{
		// do something to args
		args.palettes = ['#000', '#fff', '#EFEFF3', '#0E14AD', '#FF4178', '#E9D2D1']
		
		// return
		return args;
	});
	
})(jQuery);

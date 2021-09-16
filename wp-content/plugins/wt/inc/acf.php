<?php
defined('ABSPATH') || exit;



add_action('acf/init', function()
{
	$key = get_option('options_wt_google_api_key');
	
	if ($key)
		acf_update_setting('google_api_key', $key);
});



add_filter('acf/fields/google_map/api', function( $api )
{
	
	$api['key'] = get_option('options_wt_google_api_key');
	
	return $api;
	
});



add_filter('acf/load_value/name=wt_gallery_background_color', function($value, $post_id, $field)
{
	if ($value == '#EFEFF3')
		return '';
	
	return $value;
	
}, 11, 3);



add_filter('acf/load_value/name=wt_cta', function($value, $post_id, $field)
{
	if ($value)
		return $value;

	global $post;

	if (!$post || $post->post_type != 'wt_product')
		return $value;

	$brand = get_post_meta($post->ID, 'wt_brand', true);

	if (!$brand)
		return $value;

	$query = new WP_Query([
		'post_type' => 'wt_store',
		'posts_per_page' => 1,
		'fields' => 'ids',
		'meta_key' => 'wt_brand',
		'meta_value' => $brand,
	]);
//	wt_log($query);
	if ($query->have_posts())
		return 'Store Locator';

	return $value;

}, 10, 3);

add_filter('acf/load_value/name=wt_url', function($value, $post_id, $field)
{
	if ($value)
		return $value;
	
	global $post;
	
	if (!$post || $post->post_type != 'wt_product')
		return $value;
	
	$brand = get_post_meta($post->ID, 'wt_brand', true);
	
	if (!$brand)
		return $value;
	
	$brand = get_post($brand);
	
	if (!$brand)
		return $value;
	
	$query = new WP_Query([
		'post_type' => 'wt_store',
		'posts_per_page' => 1,
		'fields' => 'ids',
		'meta_key' => 'wt_brand',
		'meta_value' => $brand->ID,
	]);
//	wt_log($query);
	if ($query->have_posts())
		return add_query_arg('brand', $brand->post_name, get_post_type_archive_link('wt_store'));
	
	return $value;
	
}, 10, 3);



if (is_admin())
{
	if (function_exists('acf_add_options_page'))
	{
		acf_add_options_sub_page(array(
			'page_title' => 'wt+ Settings',
			'menu_title' => 'wt+',
			'parent_slug' => 'options-general.php',
			'capability' => 'manage_options',
		));
		
//		acf_add_options_sub_page(array(
//			'page_title' => 'Authenticate Instagram',
//			'menu_title' => 'Authenticate Instagram',
//			'parent_slug' => 'tools.php',
//		));
	}
	
	
	
	add_filter( 'tiny_mce_before_init', function($mceInit, $editor_id)
	{
//		wt_log($mceInit);
//		wt_log($editor_id);
//		$mceInit['wpautop'] = 0;
//		$mceInit['forced_root_block'] = '';
//		$mceInit['block_formats'] = "Paragraph=p;Heading 1=h1;Heading 2=h2";
		$mceInit['fontsize_formats'] = "13px 14px 15px 16px 18px 30px 44px";
		$default_colours = '
			"000000", "Black",
		    "993300", "Burnt orange",
		    "333300", "Dark olive",
		    "003300", "Dark green",
		    "003366", "Dark azure",
		    "000080", "Navy Blue",
		    "333399", "Indigo",
		    "333333", "Very dark gray",
		    "800000", "Maroon",
		    "FF6600", "Orange",
		    "808000", "Olive",
		    "008000", "Green",
		    "008080", "Teal",
		    "0000FF", "Blue",
		    "666699", "Grayish blue",
		    "808080", "Gray",
		    "FF0000", "Red",
		    "FF9900", "Amber",
		    "99CC00", "Yellow green",
		    "339966", "Sea green",
		    "33CCCC", "Turquoise",
		    "3366FF", "Royal blue",
		    "800080", "Purple",
		    "999999", "Medium gray",
		    "FF00FF", "Magenta",
		    "FFCC00", "Gold",
		    "FFFF00", "Yellow",
		    "00FF00", "Lime",
		    "00FFFF", "Aqua",
		    "00CCFF", "Sky blue",
		    "993366", "Red violet",
		    "FFFFFF", "White",
		    "FF99CC", "Pink",
		    "FFCC99", "Peach",
		    "FFFF99", "Light yellow",
		    "CCFFCC", "Pale green",
		    "CCFFFF", "Pale cyan",
		    "99CCFF", "Light sky blue",
		    "0E14AD", "wt+ primary color",
		    "FF4178", "wt+ secondary color",
	    ';
		//"CC99FF", "Plum",
		$mceInit['textcolor_map'] = '[' . $default_colours . ']';
		
//		$mceInit['style_formats'] = json_encode([
//			[
//				'title' => 'Content Block',
//	            'block' => 'span',
//	            'classes' => 'content-block',
//	            'wrapper' => true,
//			]
//		]);

//		wt_log($mceInit);
		return $mceInit;
	}, 99, 2);
	
	add_filter( 'acf/fields/wysiwyg/toolbars' , function($toolbars)
	{
//		wt_log($toolbars);
		global $post;
		
//		if ($post && $post->post_name == 'home')
//		{
			$toolbars = [];
			$toolbars['Full'][1] = ['formatselect', 'bold' , 'italic' , 'underline', 'alignleft', 'aligncenter', 'alignright', 'forecolor', 'fontsizeselect', 'link', 'unlink', 'removeformat', 'undo', 'redo'];
//		}
		
		return $toolbars;
	});
	
	add_action('acf/input/admin_head', function()
	{
		wp_enqueue_style('wt-acf', get_theme_file_uri('/assets/css/acf.css'), [], '0.0.1');
		wp_enqueue_script('wt-acf', get_theme_file_uri('/assets/js/acf.js'), [], '0.0.1', true);
	});
	
//	add_action('acf/input/admin_footer', function()
//	{
//
//	});
	
	
	
	function wt_hide_field($field)
	{
//		wt_dump($field);
		global $post;
		
		if (!$post)
			return $field;
		
		if ($post->post_name == 'blog')
		{
			if (in_array($field['__name'], ['image', 'portrait_image', 'background_color', 'url', 'align']))
				$field['wrapper']['class'] = 'hidden';
		}
		else if ($post->post_name == 'showcase')
		{
			if (in_array($field['__name'], ['portrait_image', 'align', 'url']))
				$field['wrapper']['class'] = 'hidden';
		}
		else if ($post->post_type == 'wt_lookbook')
		{
			if (isset($field['__name']) && in_array($field['__name'], ['post', 'portrait_image', 'background_color', 'cta', 'url']))
				$field['wrapper']['class'] = 'hidden';
		}
		
		return $field;
	}
	add_filter('acf/prepare_field/name=image', 'wt_hide_field');
	add_filter('acf/prepare_field/name=portrait_image', 'wt_hide_field');
	add_filter('acf/prepare_field/name=align', 'wt_hide_field');
	add_filter('acf/prepare_field/name=url', 'wt_hide_field');
	add_filter('acf/prepare_field/name=post', 'wt_hide_field');
	add_filter('acf/prepare_field/name=background_color', 'wt_hide_field');
	add_filter('acf/prepare_field/name=cta', 'wt_hide_field');
	
	
	
	add_filter('acf/prepare_field/name=background_color', function($field)
	{
		global $post;
		
		if ($post->post_name == 'showcase')
		{
			$field['default_value'] = '#efeff3';
		}
		
		return $field;
	});
	
	
	
//	add_filter('acf/prepare_field/name=content_background_color', function($field)
//	{
//		wt_log($field);
//		$field['menu_order'] = 7;
//
//		return $field;
//	});
	
	
	
	add_filter('acf/fields/post_object/query/name=post', function($args, $field, $post_id)
	{
		$post = get_post($post_id);
		
		if ($post->post_name == 'blog')
		{
			$args['post_type'] = 'post';
		}
		else if ($post->post_name == 'showcase')
		{
			$args['post_type'] = 'wt_event';
		}
		
		return $args;
	}, 10, 3);
	
	
	
//	add_filter('acf/prepare_field/name=wt_registrations', function($field)
//	{
//		foreach ($field['sub_fields'] as $i => $sub_field)
//		{
//			if ($sub_field['name'] == 'user')
//			{
//				$field['sub_fields'][$i]['readonly'] = true;
//			}
//		}
//
//		return $field;
//	});
	
	add_action( 'acf/render_field/type=select', function($field)
	{
		if ($field['_name'] != 'user' || empty($field['value'][0]))
			return;
		?>
		
		<a href="<?php echo get_edit_user_link($field['value'][0]); ?>" target="_blank">Partner Details</a>
		
		<?php
	}, 10, 1 );
	
	add_filter('acf/update_value/name=wt_registrations', function($value, $post_id, $field)
	{
		if (empty($_POST['ID']) || empty($_POST['acf'][$field['key']]) || !is_array($_POST['acf'][$field['key']]) || empty($field['sub_fields']) || !is_array($field['sub_fields']))
			return $value;
		
		$fields = [];
		
		foreach ($field['sub_fields'] as $f)
			$fields[$f['name']] = $f['key'];
		
		foreach ($_POST['acf'][$field['key']] as $registration)
		{
			$user_id = $registration[$fields['user']];
			$registrations = get_user_meta($user_id, 'wt_registrations', true);
			
			if (!is_array($registrations))
				$registrations = [];
			
			$registrations[$_POST['ID']] = (int) $registration[$fields['status']];
			
			update_user_meta($registration[$fields['user']], 'wt_registrations', $registrations);
		}
		
		return $value;
	}, 10, 3);
	
	
	
//	add_action('acf/save_post', function($post_id)
//	{
//		global $post;
//
//		if (!$post)
//			return;
//
//
//
//	}, 20);
}

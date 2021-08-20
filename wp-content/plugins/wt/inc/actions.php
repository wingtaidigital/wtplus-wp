<?php
add_action('init', function()
{
//	register_post_type('wt_slide', array(
//		'labels' => array(
//			'name' => 'Slides',
//			'singular_name' => 'Slide',
//		),
//		'rewrite' => array('slug' => 'slides'),
////		'query_var' => 'brand',
//		'menu_icon' => 'dashicons-slides',
//		'supports' => array('title', 'editor', 'thumbnail', 'revisions'), /* 'comments', 'author', 'custom-fields', 'excerpt', 'page-attributes', */
////		'capability_type' => array($singular, $plural),
////		'capabilities' => array(
////			'delete_posts' => "delete_$plural",
////		),
//		'public' => false,
//		'show_ui' => true,
////		'has_archive' => true,
////		'show_in_rest' => true,
////		'rest_base' => $no_prefix_plural,
////		'delete_with_user' => true
//	));
	
	register_post_type('wt_brand', array(
		'labels' => array(
			'name' => 'Brands',
			'singular_name' => 'Brand',
			'archives' => 'Brands'
		),
		'rewrite' => array(
			'slug' => 'brands',
			'with_front' => false
		),
//		'query_var' => 'brand',
		'menu_icon' => 'dashicons-tag',
		'hierarchical' => true,
		'supports' => array('title', 'editor', 'thumbnail', 'page-attributes', 'revisions', 'custom-fields'), /* 'comments', 'author', 'custom-fields', 'excerpt', 'page-attributes', */
//		'capability_type' => array($singular, $plural),
//		'capabilities' => array(
//			'delete_posts' => "delete_$plural",
//		),
		'public' => true,
//		'show_ui' => true,
//		'has_archive' => true,
		'show_in_rest' => true,
//		'rest_base' => $no_prefix_plural,
//		'delete_with_user' => true
	));
	
	
	
	register_post_type('wt_store', array(
		'labels' => array(
			'name' => 'Stores',
			'singular_name' => 'Store',
			'archives' => 'Store Locator'
		),
		'rewrite' => array(
			'slug' => 'stores',
			'with_front' => false
		),
		'menu_icon' => 'dashicons-store',
		'supports' => array('editor', 'revisions'), /* 'author', 'custom-fields', 'excerpt', 'page-attributes', */
//		'capability_type' => array($singular, $plural),
//		'capabilities' => array(
//			'delete_posts' => "delete_$plural",
//		),
		'public' => true,
//		'show_in_nav_menus' => true,
//		'show_ui' => true,
		'has_archive' => true,
//		'show_in_rest' => true,
//		'rest_base' => $no_prefix_plural,
//		'delete_with_user' => true
	));
	
	register_taxonomy('wt_mall', 'wt_store', [
		'label' => 'Mall',
		'meta_box_cb' => false,
//		'query_var' => 'mall'
//		'rewrite' => [ 'slug' => 'region' ],
	]);
	
	register_taxonomy('wt_region', 'wt_store', [
		'label' => 'Region',
		'query_var' => 'region'
//		'rewrite' => [ 'slug' => 'region' ],
	]);
	
	
	
	register_post_type('wt_product', array(
		'labels' => array(
			'name' => 'Products',
			'singular_name' => 'Product',
		),
		'rewrite' => array(
			'slug' => 'products',
			'with_front' => false
		),
//		'query_var' => 'brand',
		'menu_icon' => 'dashicons-products',
		'supports' => array('title', 'editor', 'thumbnail', 'page-attributes', 'revisions'), /* 'comments',  'author', 'excerpt', */
//		'capability_type' => array($singular, $plural),
//		'capabilities' => array(
//			'delete_posts' => "delete_$plural",
//		),
		'public' => true,
//		'show_ui' => true,
//		'has_archive' => true,
//		'show_in_rest' => true,
//		'rest_base' => $no_prefix_plural,
//		'delete_with_user' => true
	));
	
	
	
	register_post_type('wt_event', array(
		'labels' => array(
			'name' => 'Events',
			'singular_name' => 'Event',
		),
//		'rewrite' => array('slug' => 'brands'),
//		'query_var' => 'brand',
		'menu_icon' => 'dashicons-calendar-alt',
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields'), /* 'comments', 'editor', 'author', 'page-attributes', */
//		'capability_type' => array($singular, $plural),
//		'capabilities' => array(
//			'delete_posts' => "delete_$plural",
//		),
//		'public' => true,
		'show_ui' => true,
//		'has_archive' => true,
//		'show_in_rest' => true,
//		'rest_base' => $no_prefix_plural,
//		'delete_with_user' => true
	));
	
	register_post_type('wt_lookbook', array(
		'labels' => array(
			'name' => 'Lookbook',
			'singular_name' => 'Lookbook',
			'archives' => 'Lookbook'
		),
		'rewrite' => array(
			'slug' => 'lookbook',
			'with_front' => false
		),
		'hierarchical' => true,
		'menu_icon' => 'dashicons-format-gallery',
		'supports' => array('title', 'page-attributes', 'revisions'), /* 'editor', 'author', 'custom-fields', 'excerpt', , */
//		'capability_type' => array($singular, $plural),
//		'capabilities' => array(
//			'delete_posts' => "delete_$plural",
//		),
		'public' => true,
//		'show_ui' => true,
		'has_archive' => true,
//		'show_in_rest' => true,
//		'rest_base' => $no_prefix_plural,
//		'delete_with_user' => true
	));
	
	
	register_post_type('wt_notification', array(
		'labels' => array(
			'name' => 'Notifications',
			'singular_name' => 'Notification',
			'archives' => 'Notifications'
		),
		/*'rewrite' => array(
			'slug' => 'lookbook',
			'with_front' => false
		),
		'hierarchical' => true,*/
		'menu_icon' => 'dashicons-megaphone',
		'supports' => array('title', 'editor', 'revisions'), /* 'page-attributes', 'author', 'custom-fields', 'excerpt', , */
//		'capability_type' => array($singular, $plural),
//		'capabilities' => array(
//			'delete_posts' => "delete_$plural",
//		),
		'public' => false,
		'show_ui' => true,
//		'has_archive' => true,
//		'show_in_rest' => true,
//		'rest_base' => $no_prefix_plural,
//		'delete_with_user' => true
	));
	
	
	
	global $wp_rewrite;
	$wp_rewrite->author_structure = '/' . $wp_rewrite->author_base . '/%author%';
//	wt_log($wp_rewrite);
	
	
	
	if (isset($_GET['loggedin']) && is_user_logged_in())
	{
		$sessions = WP_Session_Tokens::get_instance( get_current_user_id() );
		
//		if ( $user->ID === get_current_user_id() )
			$sessions->destroy_others( wp_get_session_token() );
	}
});



add_action('pre_get_posts', function($query)
{
//	wt_dump($query);
//	if ($query->is_main_query())
	if ($query->get('post_type') == 'wt_store' || $query->get('post_type') == 'wt_product')
	{
		$var = empty($_GET['brand']) ? '' : $_GET['brand'];
		
		if ($var)
		{
			if (!is_numeric($var))
			{
				$post = get_page_by_path($var, OBJECT, 'wt_brand');
				
				if ($post)
					$var = $post->ID;
			}
			
			if ($var)
			{
				$meta_query = $query->get('meta_query');
				
				if (!is_array($meta_query))
					$meta_query = array();
				
				$brands = get_children([
					'post_parent' => $var,
					'fields' => 'ids'
				]);
				$brands[] = $var;
				
				$meta_query[] = array(
					'key' => 'wt_brand',
					'value' => $brands,
					'compare' => 'IN',
				);
//				wt_dump($meta_query);
				$query->set('meta_query', $meta_query);
			}
		}
	}
	
//	if ($query->get('post_type') == 'wt_store')
//	{
//		$query->set('orderby', 'meta_value');
//		$query->set('meta_key', 'wt_brand');
//		$query->set('posts_per_page', -1);
//	}
});



add_action('wp_login', function($user_login, $user)
{
	if (!session_id())
		session_start();
	
//	wt_log('wp_login');
	$_SESSION['wt'] = time();
//	wt_log($_SESSION['wt']);
	$sessions = WP_Session_Tokens::get_instance( $user->ID );
	
	if ( $user->ID === get_current_user_id() )
		$sessions->destroy_others( wp_get_session_token() );
	
}, 10, 2);



add_action('set_current_user', function()
{
	if (!session_id())
		session_start();
	
//	wt_log('set_current_user');
//	wt_log($_SESSION);
	if (isset($_SESSION['wt']))
		return;
	
	if (is_user_logged_in())
		wp_logout();
});

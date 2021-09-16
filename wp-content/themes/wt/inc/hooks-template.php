<?php
defined('ABSPATH') || exit;



//function wt_is_showcase_child()
//{
//	global $post;
//
//	if ($post->post_parent)
//	{
//		$parent = get_post($post->post_parent);
//
//		if ($parent->post_name == 'showcase')
//			return true;
//	}
//
//	return false;
//}



add_action('template_redirect', function()
{
	if (is_attachment())
	{
		wp_redirect( home_url() );
		exit;
	}
	
	if (is_search() && empty(get_search_query()))
	{
		wp_redirect( home_url() );
		exit;
	}
	
	if (is_post_type_archive('wt_lookbook'))
	{
		$brand = 0;
		
		if (!empty($_GET['DeviceID']))
		{
			global $wpdb;
			
			$brand = $wpdb->get_var($wpdb->prepare("
				SELECT ID
				FROM $wpdb->posts JOIN $wpdb->postmeta ON ID = post_id AND meta_value = %s AND meta_key LIKE 'wt_imei_%%_imei'
			", $_GET['DeviceID']));
//			$brand = $wpdb->get_var("
//				SELECT ID
//				FROM $wpdb->posts JOIN $wpdb->postmeta ON ID = post_id AND meta_value = '" . esc_sql($_GET['DeviceID']) . "' AND meta_key LIKE 'wt_imei_%_imei'
//			");
//			wt_log($brand);
		}
		
		if (!$brand && !empty($_GET['LocationCode']))
		{
			$code = sanitize_text_field(substr($_GET['LocationCode'], 0, 2));
			$brands = get_posts([
				'post_type' => 'wt_brand',
				'meta_key' => 'wt_code',
				'meta_value' => $code,
				'posts_per_page' => 1,
				'fields' => 'ids'
			]);
//			wt_log($brands);
			if ($brands)
				$brand = $brands[0];
		}
		
		if ($brand)
		{
			$lookbooks = get_posts([
				'post_type' => 'wt_lookbook',
				'meta_key' => 'wt_brand',
				'meta_value' => $brand,
				'posts_per_page' => 1,
				'fields' => 'ids'
			]);
			
			if ($lookbooks)
			{
				$url = get_permalink($lookbooks[0]);
				$args = ['Timeout', 'ReturnURL'];
				
				foreach ($args as $arg)
				{
					if (!empty($_GET[$arg]))
						$url = add_query_arg($arg, sanitize_text_field($_GET[$arg]), $url);
				}
				
				wp_redirect($url);
				exit;
			}
		}
	}
	
	if (is_page(['wtentertainer/pageone/', 'wtentertainer/pagetwo/', 'wtentertainer/pagethree/', 'wtentertainer/pagefour/', ]))
	{
		$anchor = 'one';
		
		if (is_page('wtentertainer/pagetwo/'))
			$anchor = 'two';
		elseif (is_page('wtentertainer/pagethree/'))
			$anchor = 'three';
		elseif (is_page('wtentertainer/pagefour/'))
			$anchor = 'four';
		
		wp_redirect( home_url('wtentertainer/appoffer/#page' . $anchor) );
		exit;
	}
});



//add_filter('author_template_hierarchy', function($templates)
//{
////	global $wp_query;
////
////	if (isset($wp_query->query_vars['profile']))
////	{
////		array_unshift( $templates, "custom-templates/author/profile.php" );
////	}
//
//	if (isset($_GET['events']))
//
//	return $templates;
//});



add_filter('frontpage_template_hierarchy', function($templates)
{
	if (isset($_GET['fb']))
		array_unshift( $templates, "custom-templates/callback-fb.php" );
	else if (isset($_GET['ig']) || isset($_GET['code'])|| isset($_REQUEST['hub.challenge'])|| isset($_REQUEST['hub_challenge']))
		array_unshift( $templates, "custom-templates/callback-ig.php" );
	
	return $templates;
});



add_filter('page_template_hierarchy', function($templates)
{
	global $post;
	
	if ($post->post_parent)
	{
		$parent = get_post($post->post_parent);

		if ($parent->post_name == 'my-account')
			array_unshift( $templates, "page-{$parent->post_name}.php" );
		else if ($parent->post_name == 'showcase')
			array_unshift( $templates, "custom-templates/{$parent->post_name}/{$post->post_name}.php" );
	}

//	if (wt_is_showcase_child())
//		array_unshift( $templates, "custom-templates/showcase/{$post->post_name}.php" );
//	wt_dump($templates);
	return $templates;
});



add_filter('archive_template_hierarchy', function($templates)
{
	global $post;
	
	if ($post && $post->post_type == 'wt_lookbook')
	{
		array_unshift($templates, "single-{$post->post_type}.php");
	}
	
	return $templates;
});



add_filter('single_template_hierarchy', function($templates)
{
	global $post;
	
	if ($post && $post->post_type == 'wt_lookbook')
	{
		if ($post->post_parent)
			array_unshift($templates, "custom-templates/child-{$post->post_type}.php");
		else
			array_unshift( $templates, "single-{$post->post_type}.php" );
	}
	
	return $templates;
});



add_filter('singular_template_hierarchy', function($templates)
{
	global $post;
//	if (is_singular('wt_store'))
	if ($post && $post->post_type == 'wt_store')
		array_unshift( $templates, "archive-wt_store.php" );

	return $templates;
});



function wt_post_template_hierarchy($templates)
{
//	wt_dump($templates);
	array_unshift( $templates, "home.php" );
	
	return $templates;
}
add_filter('category_template_hierarchy', 'wt_post_template_hierarchy');
add_filter('tag_template_hierarchy', 'wt_post_template_hierarchy');

<?php
//add_filter( 'https_ssl_verify', '__return_false' ); // memberson cert is expired



add_filter( 'http_request_timeout', function($timeout_value)
{
	return 60;
});



/*add_filter('auth_cookie_expiration', function($length, $user_id, $remember) // not by idle time
{
	return HOUR_IN_SECONDS;
}, 99, 3);*/



add_filter('wp_login_errors', function($errors)
{
	if (is_wp_error($errors) && $errors->get_error_data())
		return wt_login_error($errors);
	
	return $errors;
});
//add_filter('login_errors', function()
//{
//	return 'Invalid login.';
//});


//add_filter('comments_open', function($open, $post)
//{
//	if (is_numeric($post))
//		$post = get_post($post);
//
//	if (!$post)
//		return $open;
//
//	if ($post->post_type == 'wt_brand')
//		return true;
//
//	return $open;
//
//}, 10, 2);



add_filter('the_content', function($content)
{
	return wp_kses_post($content);
});



add_filter('get_the_excerpt', function($excerpt)
{
	return sanitize_text_field($excerpt);
});



add_filter('the_title', function($title, $id)
{
//	global $post;
//
//	if ($post && $post->post_type == 'wt_store')
//	{
//		$store = get_post($id);
//
//		if ($store->post_type == 'wt_store')
//		{
//			$brand = get_field('wt_brand', $store->ID);
//
//			if ($brand)
//				$title = $brand->post_title;
//
//			$malls = get_the_terms($post, 'wt_mall');
//
//			if ($malls && is_array($malls))
//			{
//				$title .= ' ' . $malls[0]->name;
//			}
//		}
//	}
	
	return wp_kses_post($title);
}, 10, 2);

<?php
defined('ABSPATH') || exit;



//add_action('init', function()
//{
////	add_rewrite_endpoint( 'profile', EP_AUTHORS );
//	add_rewrite_endpoint( 'events', EP_AUTHORS );
//
////	if (!empty($GLOBALS["super_cache_enabled"]) && (!empty($_SESSION['wt_crm']) || !empty($_SESSION['wt_fb'])))
////	{
////		define( 'DONOTCACHEPAGE', true );
////	}
//
////	$pages = ['profile', 'transactions'];
//
////	foreach ($pages as $page)
////
////	add_rewrite_rule( 'profile', EP_AUTHORS );
//
////	if (isset($_GET['reinit']))
//	//	flush_rewrite_rules();
//});



/*add_action('template_redirect', function()
{
	if (is_user_logged_in())
		return;
	
	if (is_page(['login', 'signup', 'forgot-password']))
	{
		wp_redirect(home_url('wt'));
		exit;
	}
});*/



add_action('pre_get_posts', function($query)
{
	if ($query->is_post_type_archive('wt_lookbook'))
	{
		if ($query->is_main_query())
		{
			$query->set('post_parent', 0);
			$query->set('posts_per_page', 1);
		}
	}
	else if ($query->get('post_type') == 'wt_store'/* && $query->get('fields') != 'ids'*/)
	{
		$query->set('orderby', 'title');
		$query->set('order', 'ASC');
		$query->set('posts_per_page', -1);
	}
});



add_action('wp_enqueue_scripts', function()
{
	global $wp_query;
	
	$deps = ['jquery'];
	
	wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css?family=Catamaran:100,400,500,600,700,800', [], null);
//	wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css?family=Catamaran:400,500,600,700,800|Material+Icons', [], null);
//	wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', [], null);
	
	if (is_front_page() || is_archive('wt_lookbook') || is_singular(['post', 'wt_brand', 'wt_lookbook', 'wt_product']) || is_page_template('page-templates/content-blocks.php'))
	{
		$deps[] = 'slick';
		wp_enqueue_script('slick', '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js', ['jquery'], null, true);
//		wp_enqueue_script('slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.7.1/slick/slick.min.js', ['jquery'], null, true);
	}
	
	if (empty($_SESSION['wt_crm']) || !is_user_logged_in() || is_page(['signup', 'profile', 'redemptions', 'refer-a-friend']) || is_author())
	{
		$deps[] = 'js-webshim';
		wp_enqueue_script('js-webshim', get_theme_file_uri('/assets/vendor/js-webshim/polyfiller.js'), ['jquery'], null, true);
	}
	
	if (!wp_is_mobile() && is_page(['signup', 'profile', 'contact']))
	{
		$deps[] = 'select2';
		wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/select2/4.0.3/css/select2.min.css', [], null);
		wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/select2/4.0.3/js/select2.min.js', ['jquery'], null, true);
	}
	
	if (is_page(['signup', 'profile', 'contact']) || is_author())
	{
		$deps[] = 'libphonenumber';
		wp_enqueue_script('libphonenumber', get_theme_file_uri('/assets/js/vendor/libphonenumber.js'), [], '0.0.2', true);
	}
	
	wp_enqueue_style('wt', get_stylesheet_uri(), [], '0.9.5');
	wp_enqueue_script('wt', get_theme_file_uri('/assets/js/app.js'), $deps, '0.5.4', true);
	
	$data = [
		'root' => esc_url_raw(rest_url()),
		'nonce' => wp_create_nonce('wp_rest')
	];
	
//	if (is_user_logged_in())
//		$data['nonce'] = wp_create_nonce('wp_rest');

//	if (is_category() || is_tag())
//	{
//		$data['arg']['tax_query'] = $wp_query->tax_query->queries;
//	}

	wp_localize_script('wt', 'wpApiSettings', $data);
});

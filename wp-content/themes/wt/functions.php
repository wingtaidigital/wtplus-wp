<?php
defined('ABSPATH') || exit;



if (!session_id())
{
//		require_once(ABSPATH . 'wp-admin/includes/file.php');
//
//		ini_set('session.save_path', get_home_path());
	session_start();
}



if (isset($_GET['logout_fb']))
	unset($_SESSION['wt_fb']);



//if (!empty($GLOBALS['w3tc_config']))
//{
//	add_filter( 'w3tc_can_print_comment', '__return_false', 99 );
//
//	if (!empty($_SESSION['wt_crm']) || !empty($_SESSION['wt_fb']))
//		add_filter( 'w3tc_can_cache', '__return_false' );
//}



function wt_after_switch_theme()
{
	update_option('medium_large_size_w', 640); // default - 768
	update_option('medium_large_size_h', 9999); // default - 768
}
add_action('after_switch_theme', 'wt_after_switch_theme');
//if (isset($_GET['reinit']))
//	wt_after_switch_theme();



add_action('after_setup_theme', function()
{
	add_theme_support( 'title-tag' );
	
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 640, 9999 ); // default - 768

	add_theme_support( 'custom-logo', array(
		'flex-width'  => true,
	) );
	
//	add_theme_support( 'custom-header' );
//
//	add_theme_support( 'custom-background' );

//	add_editor_style( array( 'assets/css/editor-style.css', twentyseventeen_fonts_url() ) );

});



add_action('init', function()
{
	register_nav_menus( array(
		'main' => 'Main Menu',
		'corporate' => 'Corporate',
		'help-desk' => 'Helpdesk',
		'social' => 'Social Links'
	) );
});



//add_filter( 'post_rewrite_rules', function($post_rewrite)
//{
//	wt_log($post_rewrite);
//	return $post_rewrite;
//});

//add_filter( 'register_post_type_args', function( $args, $post_type )
//{
////	wt_log($post_type);
////	wt_log($args);
//
////	if ($post_type == 'post')
////	{
////		$args['rewrite'] = [
////			'slug' => 'blog',
////			'with_front' => true
////		];
////	}
//	if ($post_type == 'page')
//	{
//		$args['rewrite'] = [
//			'with_front' => false
//		];
//	}
//
//	return $args;
//}, 99, 2 );



require_once 'inc/functions-social.php';



if (!is_admin())
{
	require_once 'inc/hooks-remove.php';
	
	require_once 'inc/class-walker-nav-menu.php';
	
	require_once 'inc/functions-public.php';
	require_once 'inc/functions-public-ascentis.php';
	require_once 'inc/functions-redirect.php';
	require_once 'inc/functions-social-public.php';
	
	require_once 'inc/hooks-template.php';
	require_once 'inc/hooks-url.php';
	
	require_once 'inc/actions-public.php';
	
	require_once 'inc/filters-public.php';
	require_once 'inc/filters-menu.php';
}



/*function wt_circular_array($value, $array)
{
	$pos = array_search($value, $array);
	
	if ($pos === false)
		return false;
	
	return array_merge(
		array_slice($array, $pos),
		array_slice($array, 0, $pos)
	);
}*/

function wt_get_weekdays_range($days)
{
	$weekdays = [];
	
	for ($i = 0; $i < 7; $i++)
		$weekdays[] = jddayofweek($i, 1);
	
	$diff = array_diff($weekdays, $days);
	$_days = $weekdays;

	foreach ($diff as $day)
	{
		$i = array_search($day, $_days);
		$_days = array_merge(
			array_slice($_days, $i + 1),
			array_slice($_days, 0, $i)
		);
	}
//	wt_dump($_days);
	
	$range = [];
	$i = 0;
	$format = 'D';
	
	foreach ($_days as $day)
	{
		try
		{
			if (isset($from))
				$date = new DateTime($from->format('Y-m-d') . ' next ' . $day);
			else
				$from = $to = $date = new DateTime('next ' . $day);
		}
		catch (Exception $e)
		{
			return;
		}
		
		$diff = $from->diff($date);
		
		if ($diff->days === $i)
		{
			$to = $date;
			$i++;
			
			continue;
		}
		
		$range[] = [$from->format($format), $to->format($format)];
		$from = $to = $date;
		$i = 1;
	}
	
	if (isset($from))
	{
		$range[] = [$from->format($format), $to->format($format)];
		
		return $range;
	}
//	wt_dump($range);
}



if (!function_exists('wt_dump'))
{
	function wt_dump($message)
	{
		echo '<pre>';
		print_r($message);
		echo '</pre>';
	}
	
	function wt_log($message, $filename = 'wt')
	{
		if (is_array($message) || is_object($message))
			$message = print_r($message, true);
		else
			$message .= "\n";
		
		error_log('['.date("d-M-Y H:i:s e").'] ' . $message, 3, WP_CONTENT_DIR . '/' . $filename . '.log');
	}
}

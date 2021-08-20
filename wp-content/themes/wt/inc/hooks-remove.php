<?php
remove_action('wp_head', 'wp_generator'); // remove wordpress version

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );  // remove emojis
remove_action( 'wp_print_styles', 'print_emoji_styles' );   // remove emojis
add_filter( 'emoji_svg_url', '__return_false' ); // remove <link rel='dns-prefetch' href='//s.w.org'>

remove_action('wp_head', 'wlwmanifest_link'); // remove wlwmanifest.xml (needed to support windows live writer)

remove_action('wp_head', 'rsd_link'); // remove really simple discovery link

remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 ); // remove shortlink
remove_action('template_redirect', 'wp_shortlink_header', 11, 0 );

remove_action('wp_head', 'rel_canonical');

//remove_action('wp_head', 'adjacent_posts_rel_link_wp_head'); // remove the / and previous post links

//remove_action('wp_head', 'feed_links', 2); // remove rss feed links
remove_action('wp_head', 'feed_links_extra', 3); // removes all extra rss feed links

remove_action( 'wp_head', 'rest_output_link_wp_head', 10 ); // remove the REST API link
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' ); // remove oEmbed discovery links
remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 ); // remove the REST API link from HTTP Headers

remove_action( 'wp_head', 'wp_oembed_add_host_js' ); // remove oEmbed-specific javascript from front-end / back-end
//remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10); // don't filter oEmbed results

////include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
////if (!is_plugin_active('debug-bar/debug-bar.php'))
add_filter('show_admin_bar', '__return_false');



add_filter('xmlrpc_enabled', '__return_false');
add_filter('pings_open', '__return_false');
//add_filter('wp_headers', function( $headers )
//{
//	unset( $headers['X-Pingback'] );
//
//	return $headers;
//}, 99);


//remove_action('rest_api_init', 'wp_oembed_register_route'); // remove the oEmbed REST API route
//
//// Filters for WP-API version 2.x
////add_filter('rest_authentication_errors', function()
////{
////	return new WP_Error('no-rest', 'No REST');
////});
//add_filter('rest_jsonp_enabled', '__return_false');
//


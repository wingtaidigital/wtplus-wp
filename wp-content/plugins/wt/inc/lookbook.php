<?php
defined('ABSPATH') || exit;



add_action('acf/save_post', function($post_id)
{
	global $post;
	
	if (!$post || $post->post_type != 'wt_lookbook')
		return;
	
	$brand = get_field('wt_brand', $post_id);
	
	if (!$brand)
		return;
	
	wp_update_post([
		'ID' => $post_id,
		'post_title' => $brand->post_title,
		'post_name' => sanitize_title($brand->post_title)
	]);
}, 20);

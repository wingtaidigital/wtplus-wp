<?php
defined('ABSPATH') || exit;



//function wt_update_store_title($post_id, $post, $update)
function wt_update_store_title($post_id)
{
	global $post;
	
	if (!$post || $post->post_type != 'wt_store')
		return;
	
//	if (wp_is_post_revision( $post_id ))
//		return;
	
	$brand = get_field('wt_brand', $post_id);
	
	if (!$brand)
		return;
	
	$title = $brand->post_title;
	$malls = get_the_terms($post_id, 'wt_mall');
	
	if (!$malls || !is_array($malls))
		return;
	
	$title .= ' ' . $malls[0]->name;
	
//	remove_action('save_post_wt_store', 'wt_update_store_title', 11, 3);
	
	wp_update_post([
		'ID' => $post_id,
		'post_title' => $title
	]);
	
//	add_action('save_post_wt_store', 'wt_update_store_title', 11, 3);
}
add_action('acf/save_post','wt_update_store_title', 20);
//add_action('save_post_wt_store', 'wt_update_store_title', 11, 3);



add_action('set_object_terms', function($object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids)
{
	if ($taxonomy != 'wt_mall')
		return;
//		wt_log($terms);
	
	$regions = [];
	
	foreach ($terms as $term)
	{
		$region = get_term_meta($term, 'wt_region', true);
		
		if ($region)
			$regions[] = (int) $region;
	}
	
	wp_set_object_terms($object_id, $regions, 'wt_region');
	
}, 10, 6);



add_filter('acf/update_value/name=wt_region', function($value, $post_id, $field)
{
	$term_id = substr($post_id, 5);
	
	if (!$term_id)
		return $value;
	
	$value = (int) $value;
	
	$query = new WP_Query([
		'post_type' => 'wt_store',
		'fields' => 'ids',
		'tax_query' => [
			[
				'taxonomy' => 'wt_mall',
				'terms'    => $term_id,
			]
		]
	]);
//	wt_log($query);
	while ($query->have_posts())
	{
		$query->the_post();
		
		wp_set_object_terms(get_the_ID(), $value, 'wt_region');
	}
	
	wp_reset_postdata();
	
	return $value;
}, 10, 3);

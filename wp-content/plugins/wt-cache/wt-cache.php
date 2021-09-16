<?php
/*
Plugin Name: wt+ Cache
*/

defined('ABSPATH') || exit;



if (is_admin() && function_exists('acf_add_options_page'))
{
	acf_add_options_sub_page(array(
		'page_title' => 'Clear wt+ Cache',
		'menu_title' => 'Clear wt+ Cache',
		'parent_slug' => 'tools.php',
		'capability' => 'manage_options',
	));
	
	
	
	add_filter('acf/load_field/name=wt_transients', function($field)
	{
		global $wpdb;
		
		$field['choices'] = [];
		$transients = $wpdb->get_col("
			SELECT option_name
			FROM $wpdb->options
			WHERE option_name LIKE '_transient_wt_%';
		");
		
		foreach ($transients as $transient)
			$field['choices'][$transient] = $transient;
			
		return $field;
	});
	
	
	
	add_filter('acf/update_value/name=wt_transients', function($value, $post_id, $field)
	{
		if (!$value || !is_array($value))
			return $value;
		
		foreach ($value as $transient)
			delete_transient( substr($transient, 11)  );
		
		return $value;
	}, 10, 3);
}

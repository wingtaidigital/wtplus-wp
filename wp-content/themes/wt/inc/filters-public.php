<?php
defined('ABSPATH') || exit;



//add_filter('posts_clauses', function($clauses, $query)
//{
//	if ($query->is_post_type_archive('wt_store'))
//	{
//		global $wpdb;
////		wt_log($clauses);
//		$clauses['join'] .= " JOIN $wpdb->posts brand ON brand.ID = {$wpdb->postmeta}.meta_value ";
//		$clauses['orderby'] = "brand.post_title";
//	}
//
//	return $clauses;
//}, 10, 2);



add_filter( 'body_class', function( $classes )
{
	if ( is_singular( 'page' ) )
	{
		global $post;
		
		$classes[] = 'page-' . $post->post_name;
	}
	else if (!empty($_GET['ReturnURL']) && (is_singular('wt_lookbook') || is_archive('wt_lookbook')))
	{
		$classes[] = 'in-app';
	}
	
//	if (is_page(['signup']))
//	{
//		$classes[] = 'wt-no-history';
//	}
	
	return $classes;
} );



add_filter('get_custom_logo', function($html)
{
	return '<svg class="custom-logo" xmlns="http://www.w3.org/2000/svg" viewBox="2.924 3.401 68.937 25.256"><title>wt+</title><path fill="#000" d="M61.176 28.656a1.681 1.681 0 0 1-1.683-1.68v-7.323h-7.308a1.698 1.698 0 0 1-1.694-1.693 1.69 1.69 0 0 1 1.694-1.683h7.308V8.97c0-.938.757-1.694 1.683-1.694s1.684.757 1.684 1.694v7.308h7.308c.926 0 1.694.757 1.694 1.683s-.758 1.693-1.694 1.693H62.86v7.309a1.673 1.673 0 0 1-1.684 1.693zM40.1 28.577c-5.683 0-6.958-4.947-6.958-6.677V5.082a1.74 1.74 0 0 1 3.478 0V9.25h7.354c.938 0 1.705.768 1.705 1.717 0 .949-.768 1.717-1.705 1.717H36.62V21.9c.011.328.248 3.265 3.479 3.265 2.383 0 3.051-1.231 3.264-1.627.046-.091.091-.169.125-.216l.226.091-.203-.136c.147-.227.587-.938 1.503-.938.236 0 .496.056.756.169.396.17.678.463.836.858.26.644 0 1.333-.067 1.524-.147.361-1.548 3.682-6.438 3.682l-.001.005zm-17.396-.181l-.192-.022c-.023-.011-.158-.011-.305-.056l-.079-.023a1.655 1.655 0 0 1-.768-.553 2.203 2.203 0 0 1-.248-.418l-.056-.137-3.479-9.115-.915-2.405L12.2 27.335a1.751 1.751 0 0 1-.621.757 1.927 1.927 0 0 1-.373.203l-.113.034c-.09.022-.192.045-.271.056a.86.86 0 0 1-.226.023l-.147-.012c-.079 0-.181-.01-.305-.045l-.102-.022a2.097 2.097 0 0 1-.418-.203l-.068-.056a1.68 1.68 0 0 1-.294-.271l-.045-.045a2.324 2.324 0 0 1-.226-.361l-.034-.078-5.919-15.916a1.71 1.71 0 0 1 .045-1.333c.203-.429.553-.745.971-.915.188-.067.387-.102.587-.102.745 0 1.412.452 1.66 1.118l.034.09 4.281 11.466 4.45-11.635c.034-.113.147-.282.192-.339a.827.827 0 0 1 .09-.113c.076-.09.163-.17.26-.237.034-.034.056-.045.079-.068.045-.034.102-.056.147-.079v-.034l.723-.215.011.045h.011l.26-.011.034.023c.102.011.192.034.282.056l.045.011c.192.068.316.124.441.215l.068.045c.102.079.203.169.282.271l.068.079c.09.124.158.237.203.35l.068.169 3.479 9.115.904 2.361 4.326-11.544c.248-.655.926-1.118 1.649-1.118a1.706 1.706 0 0 1 1.57 1.017c.192.418.203.881.045 1.322l-5.964 15.948a1.516 1.516 0 0 1-.226.373 2.368 2.368 0 0 1-.339.34l-.045.033a1.838 1.838 0 0 1-.361.192l-.102.034a.26.26 0 0 1-.09.023 1.24 1.24 0 0 1-.373.056l-.102-.011.003-.001z"/></svg>';
});



remove_filter( 'the_excerpt', 'wptexturize' ); // font doesn't have ellipsis character ???

add_filter('excerpt_length', function($length)
{
	global $post;
	
	if ($post->post_type != 'post')
		return $length;
	
	global $wp_query;
	
	if (isset($wp_query->post) && $wp_query->post->post_name == 'home')
		return 17;
	
	return 99;
});

add_filter('excerpt_more', function()
{
	return '...';
});



add_filter('post_thumbnail_html', function($html, $post_id, $post_thumbnail_id, $size, $attr)
{
	if ($html)
		return $html;
	
	$gallery = get_post_meta($post_id, 'wt_gallery', true);
	
	if ($gallery && is_array($gallery) && is_numeric($gallery[0]))
	{
		$html = wp_get_attachment_image($gallery[0], $size);
		
		if ($html)
			return $html;
	}
	
	return '<div class="wt-relative wt-height-100">' . get_custom_logo() . '</div>';
	
//	$custom_logo_id = get_theme_mod( 'custom_logo' );
//
//	if (!$custom_logo_id)
//		return $html;
//	return wp_get_attachment_image( $custom_logo_id, 'medium', false, ['class' => 'custom-logo']);
//	return '<div class="wt-background-dark-gray wt-relative">' . wp_get_attachment_image( $custom_logo_id, 'medium', false, ['class' => 'custom-logo wt-center']) . '</div>';

}, 10, 5);



function wt_adjacent_post_where($where, $in_same_term, $excluded_terms, $taxonomy, $post)
{
	if ($post->post_type == 'wt_lookbook')
	{
		$where .= " AND post_parent = {$post->post_parent} ";
	}
	
	return $where;
}
add_filter('get_previous_post_where', 'wt_adjacent_post_where', 10, 5);
add_filter('get_next_post_where', 'wt_adjacent_post_where', 10, 5);


//add_filter( 'wp_embed_handler_youtube', function($embed, $attr, $url, $rawattr )
//{
//	wt_log($embed);
//	return $embed;
//}, 10, 4);

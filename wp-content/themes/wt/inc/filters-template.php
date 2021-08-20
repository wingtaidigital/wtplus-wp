<?php
function wt_is_showcase_child()
{
	global $post;
	
	if ($post->post_parent)
	{
		$parent = get_post($post->post_parent);
		
		if ($parent->post_name == 'showcase')
			return true;
	}
	
	return false;
}



add_filter('author_template_hierarchy', function($templates)
{
	global $wp_query;
	
	if (isset($wp_query->query_vars['profile']))
	{
		array_unshift( $templates, "custom-templates/author/profile.php" );
	}

	return $templates;
});



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

//		if ($parent->post_name == 'showcase')
		array_unshift( $templates, "custom-templates/{$parent->post_name}/{$post->post_name}.php" );
	}

//	if (wt_is_showcase_child())
//		array_unshift( $templates, "custom-templates/showcase/{$post->post_name}.php" );
//	wt_dump($templates);
	return $templates;
});



add_filter('single_template_hierarchy', function($templates)
{
	global $post;
//	if (is_singular('wt_store'))
	if ($post->post_type == 'wt_store')
		array_unshift( $templates, "archive-wt_store.php" );

	return $templates;
});









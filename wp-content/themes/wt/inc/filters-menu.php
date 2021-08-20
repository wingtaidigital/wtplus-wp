<?php
add_filter('wp_nav_menu_objects', function($sorted_menu_items, $args)
{
	if (empty($args->theme_location) || $args->theme_location != 'main')
		return $sorted_menu_items;
	
	global $post;
	
	$offset = 0;
	$new_items = [];
	
	foreach ($sorted_menu_items as $i => $item)
	{
//			wt_dump($item);
		switch ($item->title)
		{
			case 'Brands':
				array_push($item->classes, 'wt-2-columns');
				
				$query = wt_get_brand_query(['post_parent' => 0]);
				$half = ceil($query->post_count / 2);
				$sub_menu = [[], []];
				$current_post_id = empty($post) ? 0 : $post->ID;
				
				while ($query->have_posts())
				{
					$query->the_post();
					
					$new_item = clone $item;
					
					$new_item->db_id = 0;
					$new_item->menu_item_parent = $item->ID;
					$new_item->object_id = $post->ID;
					$new_item->url = get_permalink();
					$new_item->classes = get_post_meta($post->ID, 'wt_case_sensitive', true) ? ['wt-case-sensitive'] : [];
					
//						if ($post->post_title == 'G2000')
//							$new_item->title = 'G<span class="wt-case-sensitive">2000</span>';
//						else
						$new_item->title = get_the_title();
					
					if (is_single($new_item->object_id))
					{
						$new_item->classes[] = 'current-menu-item';
					}
					else if ($current_post_id && is_singular('wt_product'))
					{
						$brand = get_post_meta($current_post_id, 'wt_brand', true);
						
						if ($brand == $new_item->object_id)
							$new_item->classes[] = 'current-menu-item';
					}
					
					$sub_menu[$query->current_post < $half ? 0 : 1][] = $new_item;
//						$sorted_menu_items[] = $new_item;
				}
				
				wp_reset_postdata();
				
				for ($i = 0; $i < $half; $i++)
				{
					foreach ([0, 1] as $column)
					{
						if (isset($sub_menu[$column][$i]))
							$sorted_menu_items[] = $sub_menu[$column][$i];
					}
				}
//					foreach ($sub_menu as $column)
//					{
//						foreach ($column as $new_item)
//						{
//							$sorted_menu_items[] = $new_item;
//						}
//					}
				
				break;
			
			case 'Lookbook':
				$posts = get_posts([
					'post_type' => 'wt_lookbook',
					'post_parent' => 0,
					'posts_per_page' => 1,
					'fields' => 'ids'
				]);
				
				if ($posts)
				{
					$query = new WP_Query([
						'post_type' => 'wt_lookbook',
						'post_parent__in' => $posts,
						'posts_per_page' => -1,
						'orderby' => 'title',
						'order' => 'ASC',
					]);
					
					if ($query->have_posts())
					{
						array_push($item->classes, 'wt-2-columns');
						
						$half = ceil($query->post_count / 2);
						$sub_menu = [[], []];
						$current_post_id = empty($post) ? 0 : $post->ID;
						
						while ($query->have_posts())
						{
							$query->the_post();
							
							$new_item = clone $item;
							
							$new_item->db_id = 0;
							$new_item->menu_item_parent = $item->ID;
							$new_item->object_id = $post->ID;
							$new_item->url = get_permalink();
							$new_item->title = get_the_title();
							$new_item->classes = is_single($new_item->object_id) ? ['current-menu-item'] : [];
							
							$brand = get_post_meta($post->ID, 'wt_brand', true);
							
							if ($brand)
								$new_item->classes[] = get_post_meta($brand, 'wt_case_sensitive', true) ? 'wt-case-sensitive' : '';
							
							$sub_menu[$query->current_post < $half ? 0 : 1][] = $new_item;
						}
						
						wp_reset_postdata();
						
						for ($i = 0; $i < $half; $i++)
						{
							foreach ([0, 1] as $column)
							{
								if (isset($sub_menu[$column][$i]))
									$sorted_menu_items[] = $sub_menu[$column][$i];
							}
						}
					}
				}
				
				break;
			
			case 'Stories':
				$categories = get_categories();
				
				foreach ($categories as $category)
				{
					$new_item = clone $item;
					
					$new_item->db_id = 0;
					$new_item->menu_item_parent = $item->ID;
					$new_item->object_id = $post->ID;
					$new_item->url = get_category_link($category->term_id);
					$new_item->title = sanitize_text_field($category->name);
					$new_item->classes = is_category($category->term_id) ? 'current-menu-item' : '';
					
					$sorted_menu_items[] = $new_item;
				}
				
				break;
			
			case 'WT+ MEMBERSHIP':
				$query = new WP_Query([
					'post_type' => 'page',
					'pagename' => 'wt'
				]);
				
				if (is_page('wt'))
				{
					$item->url = $url = '#';
				}
				else
				{
					$url = $item->url . '#';
				}
				
				while ($query->have_posts())
				{
					$query->the_post();
					
					while ( have_rows('wt_sections') )
					{
						the_row();
						
						$menu = get_sub_field('menu');
						
						if (!$menu)
							$menu = get_sub_field('title');
						
						$new_item = clone $item;
						
						$new_item->db_id = 0;
						$new_item->menu_item_parent = $item->ID;
						$new_item->object_id = $post->ID;
						$new_item->url = $url . sanitize_title($menu);
						$new_item->title = sanitize_text_field($menu);
						$new_item->classes = '';
						
						$sorted_menu_items[] = $new_item;
					}

					// for FAQ
                    $new_item = clone $new_item;
                    $new_item->url = 'https://support.wtplus.com.sg/hc/en-us';
                    $new_item->title = sanitize_text_field('FAQs');
                    $sorted_menu_items[] = $new_item;
				}
				
				$query = new WP_Query([
					'post_type' => 'page',
					'post_name__in' => ['faq-frequently-asked-questions', 'faqs-frequently-asked-questions', 'faqs', 'faq', 'frequently-asked-questions']
				]);

				while ($query->have_posts())
				{
					$query->the_post();

//					$menu = get_the_title();
					$menu = 'FAQs';

					$new_item = clone $item;

					$new_item->db_id = 0;
					$new_item->menu_item_parent = $item->ID;
					$new_item->object_id = $post->ID;
					$new_item->url = $url . sanitize_title($menu);
//                    $new_item->url = 'https://support.wtplus.com.sg/hc/en-us';
					$new_item->title = sanitize_text_field($menu);
					$new_item->classes = '';

					$sorted_menu_items[] = $new_item;
				}
				
				wp_reset_postdata();
				
				break;
				
			case 'Showcase':
				$default = [
					'ID' => 0,
					'db_id' => 0,
					'menu_item_parent' => $item->ID
				];
				
				if (is_user_logged_in())
				{
					$new_item = wp_parse_args($default, [
						'title' => 'Dashboard',
						'url' => get_author_posts_url(get_current_user_id()),
						'menu_order' => 0,
						'classes' => is_author() ? ['current-menu-item'] : []
					]);
					$new_items[] = (object) $new_item;
					
					$new_item = wp_parse_args($default, [
						'title' => 'Log out',
						'url' => wp_logout_url()
					]);
					$new_items[] = (object) $new_item;
				}
				else
				{
					$new_item = wp_parse_args($default, [
						'title' => 'Log in',
						'classes' => is_page('showcase/login') ? ['current-menu-item'] : []
					]);
					$new_items[] = (object) $new_item;
					
					$new_item = wp_parse_args($default, [
						'title' => 'Register',
						'url' => wp_registration_url(),
						'classes' => is_page('showcase/signup') ? ['current-menu-item'] : []
					]);
					$new_items[] = (object) $new_item;
				}
				
				break;
				
			case 'Locations':
				$offset = $i - 1;
				
				break;
		}
		
//			if ($item->title == 'Brands')
//			{
//
//			}
//			else if ($item->title == 'wt+')
//			{
//
//			}
//			else if ($item->title == 'Showcase')
//			{
//
////				wt_dump($item);
//			}
//			else if ($item->title == 'Locations')
//			{
//
//			}
	}

	if ($new_items)
	{
		if (is_user_logged_in())
		{
			array_splice( $sorted_menu_items, $offset, 0, [$new_items[0]] );
			
			if (isset($new_items[1]))
				$sorted_menu_items[] = $new_items[1];
		}
		else
		{
			array_splice( $sorted_menu_items, $offset, 0, $new_items );
		}
	}

	return $sorted_menu_items;
}, 10, 2);



add_filter('nav_menu_css_class', function( $classes, $item, $args, $depth )
{
	if ($depth)
		return $classes;
	
	switch ($item->title)
	{
		case 'Stories':
		case 'Blog':
			if (is_home() || is_category() || is_singular('post'))
				$classes[] = 'current-menu-item';
			
			break;
			
		case 'Brands':
			if (is_singular('wt_brand') || is_singular('wt_product'))
				$classes[] = 'current-menu-item';
			
			break;
			
		case 'Lookbook':
			if (is_singular('wt_lookbook'))
				$classes[] = 'current-menu-item';
		
			break;
			
		case 'Showcase':
			if (is_author())
			{
				$classes[] = 'current-menu-item';
			}
			else
			{
				global $wp;
				
				if (preg_match( '#^showcase(/.+)?$#', $wp->request))
					$classes[] = 'current-menu-item';
			}
			
			break;
	}

	return $classes;
}, 10, 4);



add_filter('nav_menu_link_attributes', function($atts, $item, $args, $depth)
{
	if ($args->theme_location == 'main' && empty($item->url))
	{
		$atts['data-open'] = 'showcase-login-modal';
	}
	
	return $atts;
}, 10, 4);



add_filter('nav_menu_item_title', function($title, $item, $args, $depth)
{
//		mfe_dump($args);
	if (strpos($args->menu->slug, 'social') !== false)
	{
		$class = wt_get_social_icon($item->url);
		
		if ($class)
			return '<i class="icon icon-' . $class . '" title="' . $title . '"></i>';
		
//		$social_icons = wt_get_social_icons();
//
//		foreach ( $social_icons as $domain => $class )
//		{
//			if (false !== strpos($item->url, $domain))
//				return '<i class="icon icon-' . $class . '" title="' . $title . '"></i>';
//		}
	}
	
	return $title;
}, 10, 4 );

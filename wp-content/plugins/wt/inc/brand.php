<?php
$post_types = array('wt_product', 'wt_store');



add_action('restrict_manage_posts', function($post_type)
{
	global $post_types;
	
	if (in_array($post_type, $post_types))
	{
		$query = wt_get_brand_query();
		
		if ($query->have_posts())
		{
			?>
			
			<select name="brand">
				<option value="">All brands</option>
				
				<?php
				$selected = empty($_GET['brand']) ? '' : $_GET['brand'];
				
				while ($query->have_posts())
				{
					$query->the_post();
					?>
					
					<option value="<?php the_ID(); ?>" <?php selected($selected, get_the_ID()); ?>><?php the_title(); ?></option>
					
					<?php
				}
				
				wp_reset_postdata();
				?>
			</select>
			
			<?php
		}
	}
}, 10);



function wt_add_brand_column($posts_columns)
{
	$posts_columns['wt_brand'] = 'Brand';
	
	return $posts_columns;
}



function wt_render_brand_column($column_name, $post_id)
{
	if ($column_name == 'wt_brand')
	{
		$meta = get_post_meta($post_id, $column_name, true);
		
		if ($meta)
			echo '<a href="' . get_edit_post_link($meta) . '">' . get_the_title($meta) . '</a>';
	}
}



foreach ($post_types as $post_type)
{
	add_filter("manage_{$post_type}_posts_columns", 'wt_add_brand_column');
	add_action("manage_{$post_type}_posts_custom_column", 'wt_render_brand_column', 10, 2);
}

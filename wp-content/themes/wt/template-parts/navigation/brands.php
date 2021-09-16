<ul class="menu vertical">
	<li class="<?php echo is_page('brands') ? 'active' : '' ?>">
		<a href="<?php echo get_permalink(get_page_by_path('brands')); ?>">All Brands</a>
	</li>
	
	<?php
	$query = wt_get_brand_query();
	
	while ($query->have_posts())
	{
		$query->the_post();
		?>
		
		<li class="<?php echo is_single($post->ID) ? 'active' : '' ?>" itemscope itemtype="http://schema.org/Brand">
			<a href="<?php the_permalink(); ?>" itemprop="url"><?php the_title(); ?></a>
		</li>
		
		<?php
	}
	
	wp_reset_postdata();
	?>
</ul>

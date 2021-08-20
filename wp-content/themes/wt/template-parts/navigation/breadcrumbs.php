<ul class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">
	<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
		<a href="<?php echo home_url(); ?>" itemprop="item">Home</a>
	</li>
	
	<?php if (is_singular('wt_brand')) { ?>
		<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
			<a href="<?php echo get_permalink(get_page_by_path('brands')); ?>" itemprop="item">Brands</a>
		</li>
	<?php } ?>
	
	<li itemprop="itemListElement">
		<span class="show-for-sr">Current:</span>
		<?php
		if (is_post_type_archive('wt_store') || is_singular('wt_store'))
			echo 'Store Locator';
		else
			the_title();
		?>
	</li>
</ul>

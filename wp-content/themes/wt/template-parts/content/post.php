<?php
$permalink = get_permalink();
?>
<article class="row medium-unstack wt-post" itemscope itemtype="http://schema.org/BlogPosting">
	<a href="<?php echo $permalink; ?>" class="column shrink wt-background-cover" style="background-image: url(<?php echo get_the_post_thumbnail_url($post, 'large'); ?>)"></a>
	<div class="column flex-container">
		<div class="wt-gutter wt-gutter-vertical-x2">
			<div class="wt-margin-bottom" itemprop="keywords">
				<?php the_terms( $post->ID, 'category', '', ' ' ); ?>
			</div>
			
			<a href="<?php echo $permalink; ?>"><h1 class="wt-h2 wt-text-black wt-margin-bottom" itemprop="headline"><?php the_title(); ?></h1></a>
			
			<div class="wt-margin-bottom" itemprop="about">
				<?php the_excerpt(); ?>
			</div>
			
			<a href="<?php echo $permalink; ?>" class="wt-text-hover wt-bold" itemprop="url">READ MORE</a>
		</div>
	</div>
</article>

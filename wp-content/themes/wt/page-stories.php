<?php get_header(); ?>

<?php
while (have_posts())
{
	the_post();
	
	$fields = [];
	$fields['title'] = wp_kses_post(get_field('title', $post->ID, false));
	$fields['content'] = wp_kses_post(get_field('content'/*, $post->ID, false*/));
	$fields['cta'] = wp_kses_post(get_field('cta', $post->ID, false));
	$fields['post'] = get_field('post'/*, $post->ID, false*/);
	$fields['image'] = get_the_post_thumbnail_url($post, 'large');
	$fields['portrait_image'] = get_post_meta($post->ID, 'wt_portrait_image', true);
	
	if ($fields['portrait_image'])
		$fields['portrait_image'] = wp_get_attachment_image_url($fields['portrait_image']);
	
	if ($fields['post'])
	{
		if (empty(wp_strip_all_tags($fields['title'])))
			$fields['title'] = '<h1>' . $fields['post']->post_title . '</h1>';
		
		if (empty($fields['content']))
			$fields['content'] = $fields['post']->post_content;
		
		$image = get_the_post_thumbnail_url($fields['post'], 'large');
		if ($image)
			$fields['image'] = $image;
		
		$image = get_post_meta($fields['post']->ID, 'wt_portrait_image', true);
		if ($image)
		{
			$fields['portrait_image'] = wp_get_attachment_image_url($image);
		}
	}
	
//	wt_dump($fields);
	?>
	
	<style>
		<?php if ($fields['image']) { ?>
		#header
		{
			background-image: url(<?php echo $fields['image']; ?>);
		}
		<?php } ?>
		
		<?php if ($fields['portrait_image']) { ?>
		@media print, screen and (max-width: 63.9375em)
		{
			#header
			{
				background-image: url(<?php echo $fields['portrait_image']; ?>);
			}
		}
		<?php } ?>
	</style>
	
	<?php if ($fields['post']) { ?>
		<a href="<?php echo get_permalink($fields['post']); ?>">
	<?php } ?>
	
	<header class="wt-background-cover wt-relative" id="header">
		<div class="wt-center wt-gutter wt-clear-last-child-margin">
			<div class="wt-margin-bottom"><?php echo wp_kses_post($fields['title']); ?></div>
			
			<div class="wt-margin-bottom"><?php echo wp_kses_post($fields['content']); ?></div>
			
			<?php
			if ($fields['post'])
				wt_cta(wp_kses_post($fields['cta']));
			?>
		</div>
	</header>
	
	<?php if ($fields['post']) { ?>
		</a>
	<?php } ?>
	
	<?php
}
?>

<div class="wt-gutter-vertical wt-row">
	<?php
	$query = new WP_Query([
		'post_type' => 'post',
		'posts_per_page' => ceil(get_option('posts_per_page') / 2)
	]);
//	wt_dump($query);
	while ($query->have_posts())
	{
		$query->the_post();
		get_template_part('template-parts/content/' . $post->post_type);
	}
	
	wp_reset_postdata();
	?>
</div>

<div class="row xlarge-collapse wt-gutter-bottom-x2">
	<div class="column">
		<a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="wt-text-hover wt-bold">&lt; VIEW MORE ARTICLES</a>
	</div>
</div>

<?php get_footer();

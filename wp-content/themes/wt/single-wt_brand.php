<?php
defined('ABSPATH') || exit;

get_header();

while (have_posts())
{
	the_post();
	
	$header_image = get_field('wt_header_image');
	$header_portrait_image = get_field('wt_header_portrait_image');
	$images = count($header_image) >= count($header_portrait_image) ? $header_image : $header_portrait_image;
	$store_image = get_field('wt_store_image');
	$store_embed = get_field('wt_store_embed');
	
	if ($header_image)
	{
//		wt_dump($header_image);
//		if (!is_array($header_image))
//			$header_image = [$header_image];
//
//		if (!is_array($header_image))
//			$header_image = [$header_image];
		?>
		
		<header class="wt-margin-bottom-small">
			<div class="wt-slick">
				<?php
				foreach ($images as $i => $image)
				{
					?>
					
					<article data-image="<?php echo empty($header_image[$i]) ? $header_portrait_image[$i]['url'] : $header_image[$i]['url']; ?>"
					         data-portrait-image="<?php echo empty($header_portrait_image[$i]) ? $header_image[$i]['url'] : $header_portrait_image[$i]['url']; ?>">
					</article>
					
					<?php
//			}
				}
				?>
			</div>
			<?php /*
			<div class="wt-slick <?php echo $header_portrait_image ? 'show-for-medium' : ''; ?>">
				<?php foreach ($header_image as $image) { ?>
					<article class="wt-background-cover" style="background-image: url(<?php echo esc_url($image['url']) ?>)">
						<?php echo wp_get_attachment_image($image['ID'], 'large', false, []); ?>
					</article>
				<?php } ?>
			</div>
			
			<?php if ($header_portrait_image) { ?>
				<div class="wt-slick hide-for-medium">
					<?php foreach ($header_portrait_image as $image) { ?>
						<article class="wt-background-cover" style="background-image: url(<?php echo esc_url($image['url']) ?>)">
							<?php echo wp_get_attachment_image($image['ID'], 'medium', false, []); ?>
						</article>
					<?php } ?>
				</div>
			<?php }*/ ?>
			
			<?php //echo wp_get_attachment_image($header_image, 'full', false, []); ?>
		</header>
		
		<?php
	}
	?>
	
	<?php
	/*$header_image = wp_get_attachment_url(get_field('wt_header_image'));
	
	<header class="wt-background-cover wt-margin-bottom-small" style="background-image: url(<?php echo esc_url($header_image); ?>)">
		<?php //the_post_thumbnail('medium', ['class' => 'wt-brand-logo']); ?>
	</header>
	*/?>
	
	<section class="row expanded wt-background-light-gray wt-margin-bottom-small">
		<div class="column small-12 <?php echo $store_image || $store_embed ? 'medium-6' : ''; ?> flex-container text-center wt-min-height">
			<div class="align-self-middle wt-content wt-gutter-vertical-half wt-width-half" itemscope itemtype="http://schema.org/Brand">
				<?php
				the_post_thumbnail('medium', [
					'class' => 'wt-margin-bottom wt-brand-logo',
					'itemprop' => "logo"
				]);
				?>
				
				<?php the_content(); ?>
				
				<?php
				$lookbooks = get_posts([
					'post_type' => 'wt_lookbook',
					'meta_key' => 'wt_brand',
					'meta_value' => $post->ID,
					'posts_per_page' => 1,
					'fields' => 'ids'
				]);
				
				if ($lookbooks)
				{
					?>
					
					<p><a href="<?php echo get_permalink($lookbooks[0]); ?>" class="wt-text-hover">LOOKBOOK</a></p>
					
					<?php
				}
				?>
				
				<?php
				$stores = get_posts([
					'post_type' => 'wt_store',
					'meta_key' => 'wt_brand',
					'meta_value' => $post->ID,
					'posts_per_page' => 1,
					'fields' => 'ids'
				]);
				
				if ($stores)
				{
					?>
					
					<a href="<?php echo add_query_arg('brand', $post->post_name, get_post_type_archive_link('wt_store')); ?>" class="wt-text-hover">
						STORE LOCATOR
					</a>
					
					<?php
				}
				?>
			</div>
		</div>
		
		<?php if ($store_image) { ?>
			<div class="column small-12 medium-6 wt-background-cover wt-min-height" style="background-image: url(<?php echo esc_url($store_image); ?>)"></div>
		<?php } elseif ($store_embed) { ?>
			<div class="column small-12 medium-6 wt-min-height flex-container text-center align-center align-middle">
				<?php echo $store_embed; ?>
			</div>
		<?php } ?>
	</section>
	
	<?php get_template_part('template-parts/content/blocks'); ?>
	
	<?php
	$query = new WP_Query([
		'post_type' => 'wt_event',
		'posts_per_page' => -1,
		'meta_query' => [
			[
				'key'   => 'wt_brands',
				'value' => '"' . $post->ID . '"',
				'compare' => 'LIKE',
			],
			[
				'key'   => 'wt_date_to',
				'value' => current_time('mysql'),
				'compare' => '>=',
				'type' => 'DATETIME'
			],
		],
		'orderby' => [
			'wt_date_from' => 'ASC',
		],
	]);
	
	if ($query->have_posts())
	{
		?>
		
		<section class="wt-clear-last-child-margin">
			<?php
			while ($query->have_posts())
			{
				$query->the_post();
				
				get_template_part('template-parts/content/' . $post->post_type);
			}
			
			wp_reset_query();
			?>
		</section>
		
		<?php
	}
	?>
	
	<?php //wt_ig($post->ID); ?>
	
	<?php get_template_part('template-parts/content/infinite-products'); ?>
	
	<?php
}

get_footer();

<?php
defined('ABSPATH') || exit;

if ($post->post_name != 'showcase')
{
	$showcase = get_page_by_path('showcase');
	
	setup_postdata( $GLOBALS['post'] = &$showcase );
//	the_post();
}
//wt_dump($post);
if (have_posts() && get_post_status() == 'publish')
{
	?>
	
	<section id="showcase" class="wt-background-cover" style="background-image: url(<?php the_post_thumbnail_url('large'); ?>)">
		<div class="wt-center wt-gutter">
			<header class="wt-margin-bottom wt-upper"><h1><?php the_title(); ?></h1></header>
			
			<div class="wt-content">
				<?php the_content(); ?>
			</div>
			
			<?php
			if (!is_user_logged_in())
			{
				$cta = get_post_meta(get_the_ID(), 'wt_showcase_cta', true);
				
				if ($cta)
				{
					?>
					
					<a href="<?php echo wp_registration_url(); ?>" class="wt-text-hover"><?php echo sanitize_text_field($cta); ?></a>
					
					<?php
				}
			}
			?>
		</div>
	</section>

	<?php
}

wp_reset_postdata();

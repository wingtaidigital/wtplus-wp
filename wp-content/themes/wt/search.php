<?php
defined('ABSPATH') || exit;

get_header();
?>

<div class="row xlarge-collapse wt-gutter-vertical-x2">
	<div class="column">
		<?php get_search_form(); ?>
		
		<h1 class="wt-gutter-vertical-x2"><?php echo $wp_query->found_posts; ?> search <?php echo _n('result', 'results', $wp_query->found_posts, 'wt'); ?> for: <mark><?php echo get_search_query() ?></mark></h1>
		
		<?php if (have_posts()) { ?>
			<?php
			add_filter('the_title', 'wt_mark_search', 11);
			add_filter('the_content', 'wt_mark_search', 11);
			add_filter('get_the_excerpt', 'wt_get_excerpt', 11, 2);
			add_filter('get_the_excerpt', 'wt_mark_search', 12);
			
			while (have_posts())
			{
				the_post();
				?>
				
				<article>
					<a href="<?php echo the_permalink(); ?>" class="row align-stretch">
						<div class="column small-12 medium-3">
							<div class="flex-container align-middle -wt-background-light-gray">
								<div class="flex-child-auto text-center">
									<?php the_post_thumbnail('medium'); ?>
								</div>
							</div>
						</div>
						<div class="column small-12 medium-9 flex-container">
							<div class="flex-child-auto wt-clear-last-child-margin">
								<h1 class="wt-margin-bottom"><?php the_title(); ?></h1>
								<?php the_excerpt(); ?>
							</div>
						</div>
					</a>
				</article>
				
				<?php
			}
			
			remove_filter('the_title', 'wt_mark_search', 11);
			remove_filter('the_content', 'wt_mark_search', 11);
			remove_filter('get_the_excerpt', 'wt_mark_search', 12);
			remove_filter('get_the_excerpt', 'wt_get_excerpt', 11, 2);
			
			$links = paginate_links([
				'type' => 'array',
				'prev_next' => false,
				'mid_size' => 1
			]);
			
			if ($links)
			{
				?>
		
				<ul class="pagination text-center" role="navigation" aria-label="Pagination">
					<?php foreach ($links as $link) { ?>
						<li><?php echo $link; ?></li>
					<?php } ?>
				</ul>
				
				<?php
			}
			?>
		<?php } else { ?>
			No results found.
		<?php } ?>
	</div>
</div>

<?php get_footer();

<?php
defined('ABSPATH') || exit;

get_header();
?>

<div class="wt-gutter-vertical-x2 wt-generic">
	<div class="row">
		<div class="column">
			<?php
			while (have_posts())
			{
				the_post();
				?>
				
				<h1 class="text-center wt-h3 wt-gutter-bottom-x2"><?php the_title(); ?></h1>
				
				<?php the_content(); ?>
				
				<?php
			}
			?>
		</div>
	</div>
</div>

<?php get_footer();

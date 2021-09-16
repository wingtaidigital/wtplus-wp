<?php
/* Template Name: Content Blocks */

defined('ABSPATH') || exit;

get_header();

//while (have_posts())
//{
//	the_post();
	?>
	
	<div class="row">
		<article class="column" itemscope itemtype="http://schema.org/Article">
			<?php get_template_part('template-parts/content'); ?>
		</article>
	</div>
	
	<?php
//}
?>

<?php get_footer();

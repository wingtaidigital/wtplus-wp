<?php
defined('ABSPATH') || exit;

get_header();

while (have_posts())
{
	the_post();
	?>
	
	<div class="row">
		<article class="column" itemscope itemtype="http://schema.org/BlogPosting">
			<?php get_template_part('template-parts/content'); ?>
			
			<footer>
				<?php if (has_tag()) { ?>
					<section class="text-center">
						<h1>Tags</h1>
						
						<div itemprop="keywords">
							<?php the_terms($post->ID, 'post_tag'); ?>
						</div>
					</section>
				<?php } ?>
				
				<section class="text-center">
					<h1>Share this story</h1>
					
					<div class="shareaholic-canvas" data-app="share_buttons" data-app-id="27236149"></div>
					<script type="text/javascript" data-cfasync="false" src="//dsms0mj1bbhn4.cloudfront.net/assets/pub/shareaholic.js" data-shr-siteid="14ec77913499cbc5a8aa555ac6d9b775" async="async"></script>
				</section>
				
				<section class="row expanded">
					<div class="column"><?php echo previous_post_link('%link', '<strong>< VIEW PREVIOUS</strong><br> %title'); ?></div>
					<div class="column text-right"><?php echo next_post_link('%link', '<strong>VIEW NEXT ></strong><br> %title'); ?></div>
				</section>
				
				<section class="text-center">
					<a href="<?php echo home_url('stories'); ?>" class="wt-upper wt-text-hover wt-bold">< Back to stories</a>
				</section>
			</footer>
		</article>
	</div>
	
	<?php
}
?>

<?php get_footer();

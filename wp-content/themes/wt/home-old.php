<?php get_header(); ?>

<?php
if (is_tag())
{
	?>
	
	<h1 class="text-center wt-h3 wt-gutter-vertical-x2">Tag: <?php single_tag_title(); ?></h1>
	
	<?php
}
else
{
	$post_page = get_post(get_option( 'page_for_posts' ));
	
	if ($post_page)
	{
		?>
		
		<h1 class="text-center wt-h3 wt-gutter-vertical-x2"><?php echo get_the_title($post_page); ?></h1>
		
		<?php
	}
	?>
	<div class="menu-centered wt-gutter-bottom-x2">
		<ul class="menu vertical medium-horizontal wt-upper">
			<li class="<?php echo is_home() ? 'is-active' : ''; ?>"><a href="<?php echo get_permalink(get_option('page_for_posts')); ?>">All</a></li>
			
			<?php
			$category_id = get_query_var( 'cat' ) ;
			$categories = get_categories();
//				wt_dump($categories);
			
			foreach ($categories as $category)
			{
				?>
				
				<li class="<?php echo is_category($category) ? 'is-active' : ''; ?>"><a href="<?php echo get_category_link($category->term_id); ?>"><?php echo sanitize_text_field($category->name); ?></a></li>
				
				<?php
			}
			?>
		</ul>
	</div>
<?php
}
?>

<div class="wt-gutter-bottom-x2 wt-row" id="posts">
	<?php
	while (have_posts())
	{
		the_post();
		get_template_part('template-parts/content/' . $post->post_type);
	}
//	wt_dump($wp_query);
	?>
</div>

<?php if ($wp_query->found_posts > $wp_query->post_count) { ?>
	<form data-route="wt/v1/posts" data-content="posts" class="text-center wt-gutter-bottom-x2">
		<input type="hidden" name="paged" value="2">
		
		<?php if (is_category()) { ?>
			<input type="hidden" name="cat" value='<?php echo $wp_query->get_queried_object_id(); ?>'>
		<?php } else if (is_tag()) { ?>
			<input type="hidden" name="tag_id" value='<?php echo $wp_query->get_queried_object_id(); ?>'>
		<?php } ?>
		
		<button type="submit" class="button secondary large" data-submit="SHOW MORE" data-submitting="Loading..." data-submitted="SHOW MORE" data-enable="1">SHOW MORE</button>
	</form>
<?php } ?>

<?php get_footer();

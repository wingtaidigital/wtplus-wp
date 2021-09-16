<?php
defined('ABSPATH') || exit;

get_header();

if (!empty($_GET['ReturnURL']))
{
	$whitelisted = preg_match("~^(https://custsignup.wingtairetail.com.sg/|https://staging.memgate.com/)~", $_GET['ReturnURL']);
	
	if ($whitelisted)
	{
		$_GET['ReturnURL'] = esc_url($_GET['ReturnURL']);
		
		if (!empty($_GET['Timeout']))
		{
			?>
			
			<meta http-equiv="refresh" content="<?php echo (int) $_GET['Timeout']; ?>; url=<?php echo $_GET['ReturnURL']; ?>">
			
			<?php
		}
	}
	else
	{
		$_GET['ReturnURL'] = '';
	}
}
?>

<?php
while (have_posts())
{
	the_post();
	?>
	
	<h1 class="text-center wt-h3 wt-gutter-vertical-x2" id="main" data-scroll="main"><?php the_title(); ?></h1>
	
	<?php
	$query = new WP_Query([
		'post_parent' => $post->ID,
		'post_type' => 'wt_lookbook',
		'posts_per_page' => -1,
		'orderby' => 'menu_order title',
		'order' => 'ASC'
	]);
//	wt_dump($query);
	
	$is_parent = true;
	
	while ($query->have_posts())
	{
		$query->the_post();
		
//		$brand = get_field('wt_brand');
//
//		if ($brand)
//			$lookbook_title = sanitize_title($brand->post_title);

//		$lookbook_title = '';
		?>
		
		<?php get_template_part('template-parts/content/lookbooks'); ?>
		
		<?php
	}
	
	wp_reset_postdata();
	
	$previous = get_previous_post_link('%link', '<&nbsp;%title');
	$next = get_next_post_link('%link', '%title&nbsp;>');
	?>
	
	<p class="wt-gutter-half wt-margin-bottom-0">For all products,
		please contact the
		store to check for
		stock availability</p>
	
	<div class="row expanded wt-gutter-vertical-x2">
		<?php if ($previous) { ?>
			<div class="column"><?php echo $previous; ?></div>
		<?php } ?>
		
		<?php if ($next) { ?>
			<div class="column text-right"><?php echo $next; ?></div>
		<?php } ?>
	</div>
	
	<div class="wt-gutter-bottom"></div>
	
	<a href="#" id="top" class="hide"><i class="icon icon-chevron-up" aria-hidden="true"></i> <span class="wt-text-hover">Back to top</span></a>
	
	<?php if (!empty($_GET['ReturnURL'])) { ?>
		<div class="text-center wt-margin-bottom">
			<a href="<?php echo $_GET['ReturnURL']; ?>" class="button wt-background-black">Exit</a>
		</div>
	<?php } ?>
	
	<?php
}
?>

<?php get_footer();

<?php
defined('ABSPATH') || exit;

get_header();

$showcase = get_page_by_path('showcase');
$cta = get_post_meta($showcase->ID, 'wt_event_cta', true);
?>

<div class="wt-background-light-gray wt-gutter-bottom-x2">
	<section id="showcase" class="wt-background-cover" style="background-image: url(<?php echo esc_url(get_the_post_thumbnail_url(get_page_by_path('showcase'), 'large')); ?>)">
		<header class="wt-center wt-gutter wt-upper"><h1><?php the_title(); ?></h1></header>
	</section>
	
	<?php
	$query = new WP_Query([
		'post_type' => 'wt_event',
		'posts_per_page' => -1,
		'meta_query' => [
			[
				'key'   => 'wt_can_register',
				'value' => '1'
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
		
		<section class="">
			<h1 class="text-center wt-h3 wt-gutter-vertical-x2">OPEN FOR PARTNER REGISTRATION</h1>
			
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
	
	<div class="row collapse">
		<div class="column">
			<?php get_template_part('template-parts/showcase/join'); ?>
		</div>
	</div>
	
	<?php
	$query = new WP_Query([
		'post_type' => 'wt_event',
		'posts_per_page' => -1,
		'meta_query' => [
			[
				'key'   => 'wt_can_register',
				'value' => '0'
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
//		$showcase = get_page_by_path('showcase');
//		$cta = get_post_meta($showcase->ID, 'wt_event_cta', true);
		?>
		
		<section class="">
			<h1 class="text-center wt-h3 wt-gutter-vertical-x2">UPCOMING EVENTS</h1>
			
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

</div>

<?php get_footer();

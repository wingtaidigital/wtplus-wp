<?php
defined('ABSPATH') || exit;

get_header();

$base_url = home_url('showcase/details/');
?>

<?php get_template_part('template-parts/showcase/showcase') ?>

<?php if (have_rows('wt_highlights')) { ?>
	<section id="highlights">
		<?php
		while ( have_rows('wt_highlights') )
		{
			the_row();
			
			$odd = get_row_index() % 2;
			$fields = wt_get_content_fields();
//			$image = get_sub_field('image');
//			$title = sanitize_text_field(get_sub_field('title'));
//			$content = wp_kses_post(get_sub_field('content'));
//			$cta = sanitize_text_field(get_sub_field('cta'));
//			$event = get_sub_field('event');
			$url = $base_url;
			
			if ($fields['post'])
			{
				$url .= '#' . $fields['post']->post_name;
				
//				if (empty($image))
//					$image = get_the_post_thumbnail_url($fields['post'], 'large');
//
//				if (empty($title))
//					$title = get_the_title($fields['post']);
//
//				if (empty($content))
//					$content = get_the_excerpt($fields['post']);
			}
			?>
			
			<article class="wt-background-light-gray wt-margin-bottom-small"
			         <?php if ($fields['background_color']) { ?>
			            style="background-color: <?php echo $fields['background_color']; ?>"
			         <?php } ?>
	         >
				<a href="<?php echo $url; ?>" class="row expanded">
					<div class="column small-12 large-6 small-order-1 large-order-<?php echo $odd ? '1' : '2'; ?> wt-background-cover" style="background-image: url(<?php echo $fields['image']; ?>)"></div>
					<div class="column small-12 large-6 small-order-2 large-order-<?php echo $odd ? '2' : '1'; ?> flex-container text-center">
						<div class="flex-child-auto align-self-middle">
							<header class="wt-text-black wt-margin-bottom"><?php echo $fields['title']; ?></header>
							
							<div class="wt-content wt-text-black wt-margin-bottom">
								<?php echo $fields['content']; ?>
							</div>
							
							<?php
							if ($fields['cta'])
								wt_cta(wp_kses_post($fields['cta']));
							?>
						</div>
					</div>
				</a>
			</article>
			
			<?php
		}
		?>
	</section>
<?php } ?>

<?php if (have_rows('wt_partners')) { ?>
	<section id="partners" class="row expanded wt-background-secondary">
		<div class="column small-12 large-6 wt-background-cover" style="background-image: url(<?php echo esc_url(get_field('wt_partners_image')); ?>)"></div>
		<div class="column small-12 large-6 flex-container text-center wt-gutter-vertical-x2">
			<div class="flex-child-auto align-self-middle">
				<h1 class="wt-h2 wt-text-white wt-gutter-vertical-half"><?php echo sanitize_text_field(get_field('wt_partners_title')); ?></h1>
		
				<ul class="no-bullet">
					<?php
					while ( have_rows('wt_partners') )
					{
						the_row();
						
						$user = get_sub_field('user');
						$event = get_sub_field('event');
						$url = $base_url;
						
						if ($event)
							$url .= '#' . $event->post_name;
						?>
						
						<li class="wt-gutter-vertical-half"><a href="<?php echo esc_url($url); ?>" class="wt-h6 wt-text-white"><?php echo sanitize_text_field($user['display_name']); ?></a></li>
						
						<?php
					}
					?>
				</ul>
			</div>
		</div>
	</section>
<?php } ?>

<?php get_template_part('template-parts/showcase/join') ?>

<?php get_footer();

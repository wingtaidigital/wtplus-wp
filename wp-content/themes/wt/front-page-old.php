<?php
defined('ABSPATH') || exit;

get_header();
?>

<?php if (have_rows('wt_slider')) { ?>
	<section class="wt-slick wt-slick-autoplay">
		<?php
		while ( have_rows('wt_slider') )
		{
			the_row();
			
			$fields = wt_get_content_fields();
//			wt_dump($fields);
			$fields['content_background_color'] = get_sub_field('content_background_color');
			$tag = $fields['url'] ? 'a' : 'article';

//			if (get_sub_field('existing'))
//			{
//				$p = get_post(get_sub_field('post'));
//
//				if ($p)
//				{
//					$image = get_the_post_thumbnail_url($p, 'large');
//					$title = "<h1>{$p->post_title}</h1>";
//					$content = $p->post_content;
//					$url = get_permalink($p);
//				}
//			}
//			else
//			{
//				$image = get_sub_field('image');
//				$portrait_image = get_sub_field('portrait_image');
//				$title = get_sub_field('title');
//				$content = get_sub_field('content');
//				$url = get_sub_field('url');
//			}
//
//			if ($fields['image'])
//			{
				?>
				
				<<?php echo $tag; ?> data-image="<?php echo $fields['image']; ?>"
				         data-portrait-image="<?php echo $fields['portrait_image']; ?>"
				         <?php if (!$fields['image'] && $fields['background_color']) { ?>
					         style="background-color: <?php echo $fields['background_color']; ?>"
						<?php } ?>
				>
					<?php if ($fields['content']) { ?>
						<div class="wt-content wt-clear-last-child-margin wt-background-secondary show-for-large"
							<?php if ($fields['content_background_color']) { ?>
								style="background-color: <?php echo $fields['content_background_color']; ?>"
							<?php } ?>
						>
							<?php echo $fields['content']; ?>
						</div>
					<?php } ?>
					
					<div class="row <?php echo $fields['align'] == 'center' ? '' : 'align-' . $fields['align']; ?> ">
						<div class="column small-12 <?php echo $fields['align'] == 'center' ? '' : 'medium-6'; ?> text-center">
							<?php if ($fields['title']) { ?>
								<header><?php echo $fields['title']; ?></header>
							<?php } ?>
							
							<?php
							if ($fields['url'])
								wt_cta($fields['cta']);
							?>
						</div>
					</div>
				</<?php echo $tag; ?>>
				
				<?php
//			}
		}
		
		$autoplay = get_field('wt_autoplay') ? true : false;
		$autoplay_speed = $autoplay ? get_field('wt_autoplay_speed') * 1000 : 0;
		
		// nested so that bool and int would be parsed as string
		wp_localize_script('wt', 'wpSettings', [
			'slick' => [
				'autoplay' => $autoplay,
				'autoplaySpeed' => $autoplay_speed
			]
		]);
		?>
	</section>
<?php } ?>

<?php get_template_part('template-parts/content/blocks'); ?>

<?php
$query = new WP_Query([
	'post_type' => 'post',
	'posts_per_page' => 3
]);

if ($query->have_posts())
{
	?>
	
<!--	<section class="wt-gutter-vertical-x2" id="blog">-->
<!--		<h1 class="text-center wt-h2 wt-gutter-half wt-gutter-bottom">Stories This Week</h1>-->
<!--		-->
<!--		<div class="row">-->
<!--			--><?php
//			while ($query->have_posts())
//			{
//				$query->the_post();
//				?>
<!--				-->
<!--				<div class="column small-12 large-4 wt-margin-bottom wt-column">-->
<!--					<div class="flex-container flex-dir-column">-->
<!--						--><?php //get_template_part('template-parts/content/' . $post->post_type); ?>
<!--					</div>-->
<!--				</div>-->
<!--				-->
<!--				--><?php
//			}
//
//			wp_reset_postdata();
//			?>
<!--			-->
<!--		</div>-->
<!--	</section>-->
	
	<?php
}
?>

<?php //get_template_part('template-parts/content/products') ?>

<?php get_template_part('template-parts/showcase/showcase') ?>

<section id="instagram" class="row collapse">
	<div class="column">
		<h1 class="text-center wt-h2 wt-gutter-half wt-gutter-bottom">What's on @wtplussg</h1>
	
		<div class="text-center wt-gutter">
			<!-- SnapWidget -->
			<script src="https://snapwidget.com/js/snapwidget.js"></script>
			<iframe src="https://snapwidget.com/embed/928039" class="snapwidget-widget" allowtransparency="true" frameborder="0" scrolling="no" style="border:none; overflow:hidden; width:100%;"></iframe>
		</div>
		
		<div class="text-right wt-gutter">
			<a href="https://www.instagram.com/wtplussg/" target="_blank" rel="noreferrer" class="wt-text-hover -wt-gutter-half">VIEW MORE</a>
		</div>
	</div>
</section>

<?php //wt_ig(); ?>

<?php
//$page = get_page_by_path('showcase');
//
//if ($page)
//{
////	$fields = ['image', 'title', 'content', 'cta'];
////	$showcase = [];
////
////	foreach ($fields as $field)
////	{
////		$showcase[$field] = get_field('wt_showcase_' . $field);
////
////		if (empty($showcase[$field]) && $field != 'cta')
////		{
////			if ($field == 'image')
////				$showcase[$field] = get_the_post_thumbnail_url($page, 'large');
////			else
////			{
////				$showcase[$field] = $page->{"post_$field"};
////
////				if ($field == 'title')
////					$showcase[$field] = "<h1>{$showcase[$field]}</h1>";
////			}
////		}
////	}
//
//	$showcase['image'] = get_the_post_thumbnail_url($page, 'large');
//}
?>

<?php
/*$items = wp_get_nav_menu_items('social');

foreach ($items as $item)
{
	if (strpos($item->url, 'instagram') === false)
		continue;
	
	$url = esc_url(trailingslashit($item->url));
	
	if (isset($_GET['get_ig']))
		$json = null;
	else
		$json = get_transient('wt_ig');
		
	if (empty($json))
	{
		$response = wp_remote_get($url . 'media/'); // not working on js because of cors
//		wt_dump($response);
		
		if (!is_wp_error($response))
		{
			$json = json_decode($response['body'], true);
			
			set_transient('wt_ig', $json, DAY_IN_SECONDS);
			
//			$expiration = empty($cache_max_time) ? get_option('options_wt_ig_expiration') : $cache_max_time;
//
//			set_transient('wt_ig', $json, $expiration ? (int) $expiration : DAY_IN_SECONDS);
		}
	}
	
	if (!empty($json['items']))
	{
//		wt_dump($json['items']);
		?>
		
		<section id="instagram" class="row collapse">
			<div class="column wt-slick-container">
				<h1 class="text-center wt-gutter-half">What's on #wtplussg</h1>
				
				<div class="text-center wt-slick-arrows wt-gutter">
					<?php foreach ($json['items'] as $ig_item) { ?>
						<article class="wt-gutter-half" itemscope itemtype="http://schema.org/SocialMediaPosting">
							<a href="<?php echo esc_url($ig_item['link']); ?>" target="_blank" itemprop="url"><img src="<?php echo $ig_item['images']['standard_resolution']['url']; ?>" itemprop="image"></a>
						</article>
					<?php } ?>
				</div>
				
				<div class="text-right wt-gutter">
					<a href="<?php echo $url; ?>" target="_blank" class="wt-text-hover wt-gutter-half">VIEW MORE</a>
				</div>
			</div>
		</section>
		
		<?php
	}
	
	break;
}*/
?>

<?php get_footer();

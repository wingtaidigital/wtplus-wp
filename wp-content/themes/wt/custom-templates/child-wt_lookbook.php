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
//	$lookbook_title = get_the_title($post->post_parent);

	get_template_part('template-parts/content/lookbooks');
	?>

	<?php
	$brand = get_post_meta($post->ID, 'wt_brand', true);
    $brand_whatsapp = get_post_meta($brand, 'wt_whatsapp', true);

	if ($brand)
	{
		$query = new WP_Query([
			'post__not_in' => [$post->ID],
			'post_type' => 'wt_lookbook',
			'meta_key' => 'wt_brand',
			'meta_value' => $brand
		]);
//	wt_dump($query);
		while ($query->have_posts())
		{
			$query->the_post();
//			$header_post = $post->post_parent;
//			wt_dump($post);
			?>

			<?php get_template_part('template-parts/content/lookbooks'); ?>

			<?php
		}

		wp_reset_postdata();
	}
	?>

	<?php get_template_part('template-parts/content/infinite-products'); ?>

    <?php if ($brand_whatsapp) { ?>
        <p class="wt-gutter-half wt-margin-bottom-0">For all products, please contact the store via
            <a title="https://wa.me/<?= $brand_whatsapp ?>" href="https://wa.me/<?= $brand_whatsapp ?>" target="_blank" rel="noreferrer noopener">whatsapp</a>
        </p>
    <?php } else { ?>
        <p class="wt-gutter-half wt-margin-bottom-0">For all products, please contact the store to check for stock availability</a></p>
    <?php } ?>

	<div class="row expanded wt-gutter-vertical-x2">
		<div class="column"><a href="<?php echo get_post_type_archive_link('wt_lookbook'); ?>"><&nbsp;Back</a></div>
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

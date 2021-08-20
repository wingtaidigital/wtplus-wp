<?php
$products = get_field('wt_products');
$is_sub_field = false;

if (!$products)
{
	if (is_singular('wt_brand'))
	{
		$products = get_posts([
			'post_type'  => 'wt_product',
			'meta_query' => [
				[
					'key'   => 'wt_brand',
					'value' => $post->ID
				]
			]
		]);
	}
	else
	{
		$products = get_sub_field('wt_products');
		
		if ($products)
			$is_sub_field = true;
	}
}

if ($products && is_array($products) && count($products) >= 2)
{
	if ($is_sub_field)
	{
		$image = get_sub_field('wt_products_image');
		$title = get_sub_field('wt_products_title');
		$cta   = get_sub_field('wt_products_cta');
	}
	else
	{
		$image = get_field('wt_products_image');
		$title = get_field('wt_products_title');
		$cta   = get_field('wt_products_cta');
	}
	
	$image = esc_url($image);
	$title = wp_kses_post($title);
	$cta = sanitize_text_field($cta);
	
	$odd = [];
	$even = [];
	
	foreach ($products as $i => $p)
	{
		if (($i + 1) % 2 == 0)
		{
			$even[] = $p;
		}
		else
		{
			$odd[] = $p;
		}
	}
	?>
	
	<section class="text-center wt-products wt-products-slide" <?php echo is_front_page() ? 'id="products"' : ''; ?>>
		<div class="hide-for-medium wt-slick-arrows wt-flex wt-gutter-x2">
			<header>
				<div class="flex-container align-center align-middle wt-background-contain" style="background-image: url(<?php echo $image; ?>)">
					<div>
						<?php echo $title; ?>
					</div>
				</div>
			</header>
			
			<?php foreach ($products as $p) { ?>
				<?php wt_product($p, $cta, true); ?>
			<?php } ?>
		</div>
		
		<div class="row collapse align-stretch show-for-medium wt-slick-pair">
			<div class="column small-4">
				<div class="wt-slick-left">
					<?php foreach ($odd as $p) { ?>
						<?php wt_product($p, $cta, true); ?>
					<?php } ?>
				</div>
			</div>
			<header class="column small-4 flex-container align-center align-middle wt-background-contain" style="background-image: url(<?php echo $image; ?>)">
				<div>
					<?php echo $title; ?>
				</div>
			</header>
			<div class="column small-4">
				<div class="wt-slick-right">
					<?php foreach ($even as $p) { ?>
						<?php wt_product($p, $cta, true); ?>
					<?php } ?>
				</div>
			</div>
		</div>
	</section>
	
	<?php
}

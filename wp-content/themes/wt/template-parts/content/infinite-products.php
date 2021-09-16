<?php
$posts_per_page = get_option('posts_per_page') * 2;
$brand = is_singular('wt_brand') ? $post->ID : get_post_meta($post->ID, 'wt_brand', true);
$is_sub_field = false;

if ($brand)
{
	$products = [];
	$query = new WP_Query([
		'post_type'      => 'wt_product',
		'posts_per_page' => $posts_per_page,
		'meta_query'     => [
			[
				'key'   => 'wt_brand',
				'value' => $brand
			]
		],
		'orderby'        => [
			'menu_order' => 'ASC',
			'date'       => 'DESC'
		],
	]);
}
else
{
	$products = get_sub_field('wt_products');
	
	if ($products)
	{
		$is_sub_field = true;
//		$products = array_column($products, 'ID');
		$products = array_map(function($o)
		{
			return $o->ID;
		}, $products);
		
		if ($products)
		{
			$query = new WP_Query([
				'post_type'      => 'wt_product',
				'posts_per_page' => $posts_per_page,
				'post__in'       => $products,
				'orderby'        => [
					'post__in' => 'ASC',
				],
			]);
		}
	}
}
// gitlab ticket #23 to hide for cath and g2k collection
if (isset($query) && $query->have_posts() && (($brand != 235) && ($brand != 10186)))
{
	if ($is_sub_field)
	{
		$title = get_sub_field('wt_products_title');
		$cta = get_sub_field('wt_products_cta');
	}
	else
	{
		$title = get_post_meta($brand, 'wt_products_title', true);
		$cta   = get_post_meta($brand, 'wt_products_cta', true);
	}
	
	$title = empty(strip_tags($title)) ? '<h1>View the Collection</h1>' : wp_kses_post($title);
	$cta   = sanitize_text_field("FIND OUT MORE");
	
	if (isset($GLOBALS['wt_products_scroll']))
		$GLOBALS['wt_products_scroll'] = absint($GLOBALS['wt_products_scroll']);
	else
		$GLOBALS['wt_products_scroll'] = 0;
	
	$id = 'products-' . $GLOBALS['wt_products_scroll'];
	?>
	
	<span id="catalog"></span>
	<section class="text-center wt-products wt-products-scroll" id="<?php echo $id; ?>" data-scroll="<?php echo $id; ?>" data-brand="<?php echo $brand; ?>" data-post__in="<?php echo implode(',', $products); ?>">
		<header class="wt-gutter-half wt-gutter-bottom">
			<?php echo $title; ?>
		</header>
		
		<div class="row collapse align-stretch">
			<?php
			while ($query->have_posts())
			{
				$query->the_post();
				?>
				
				<div class="column small-6 medium-3">
					<?php wt_product($post, $cta); ?>
				</div>
				
				<?php
			}
			
			wp_reset_postdata();
			?>
		</div>
		
		<?php if ($query->found_posts > $posts_per_page) { ?>
			<button class="button hollow secondary wt-margin-top" data-load>scroll to load more</button>
		<?php } ?>
	</section>
	
	<?php
	$GLOBALS['wt_products_scroll']++;
}

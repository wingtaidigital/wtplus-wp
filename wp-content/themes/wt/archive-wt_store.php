<?php
defined('ABSPATH') || exit;

get_header();
?>

<?php
$page = get_page_by_path('stores');
$brand = empty($_GET['brand']) ? 0 : $_GET['brand'];

if ($brand && !is_numeric($brand))
{
	$brand = get_page_by_path($brand, OBJECT, 'wt_brand');

	if ($brand)
		$brand = $brand->ID;
}

if (!$brand && is_singular('wt_store'))
{
	$brand = get_post_meta($post->ID, 'wt_brand', true);
}
//wt_dump($brand);
?>

<h1 class="text-center wt-h3 wt-gutter-top-x2 wt-gutter-bottom-half"><?php echo sanitize_text_field($page->post_title); ?></h1>
<div class="text-center wt-gutter-bottom-x2"><?php echo $page->post_content ?></div>

<div class="row collapse large-unstack wt-gutter-bottom-x2" id="store-locator">
	<div class="column small-12 large-4 wt-background-light-gray wt-gutter-half wt-gutter-vertical">
		<label class="wt-gutter-half wt-upper wt-h6">
			Filter by Brand
			<select name="brand" data-route="wt/v1/stores">
				<option value="">All brands</option>
				<?php
				$brands = $wpdb->get_col("
					SELECT meta_value
					FROM $wpdb->posts
						INNER JOIN $wpdb->postmeta
							ON ID = post_id
								AND post_type = 'wt_store'
								AND post_status = 'publish'
								AND meta_key = 'wt_brand'
				");
//				wt_dump($brands);

//				$query = wt_get_brand_query();
				$query = new WP_Query([
					'post_status' => ['publish', 'draft'],
					'post_type' => 'wt_brand',
					'post__in' => $brands,
					'posts_per_page' => -1,
					'orderby' => 'title',
					'order' => 'ASC',
				]);
//				wt_dump($query);
				while ($query->have_posts())
				{
					$query->the_post();

//					$store_query = new WP_Query([
//						'post_type' => 'wt_store',
//						'posts_per_page' => 1,
//						'fields' => 'ids',
//						'meta_key' => 'wt_brand',
//						'meta_value' => $post->ID,
////						'suppress_filters' => false
//					]);
////					wt_dump($store_query->posts);
//
//					if (!$store_query->have_posts())
//						continue;
					?>

					<option value="<?php the_ID(); ?>" <?php selected($brand, $post->ID); ?>><?php the_title(); ?></option>

					<?php
				}

				wp_reset_postdata();
				?>
			</select>
		</label>

		<label class="wt-gutter-half wt-gutter-bottom-half wt-upper wt-h6">
			Filter by Location
			<select name="region" data-route="wt/v1/stores">
				<option value="">All locations</option>
				<?php
				$terms = get_terms('wt_region');

				foreach ($terms as $term)
				{
					?>

					<option value="<?php echo esc_attr($term->slug); ?>" <?php //selected($brand, $post->ID); ?>><?php echo sanitize_text_field($term->name); ?></option>

					<?php
				}

				wp_reset_postdata();
				?>
			</select>
		</label>

		<h2 class="wt-gutter-half wt-margin-bottom wt-h6">STORE <small class="hide-for-large"><a href="#map">Map View</a></small></h2>

		<div id="stores">
			<?php
			if (have_posts())
			{
				while (have_posts())
				{
					the_post();

					get_template_part('template-parts/content/' . $post->post_type);
				}
			}
			else
			{
				?>

				<p class="wt-gutter-half">No stores found.</p>

				<?php
			}
			?>
		</div>
	</div>
	<div class="column small-12 large-8">
		<?php
		$url = 'https://maps.googleapis.com/maps/api/js?callback=wtInitMap';
		$key = get_option('options_wt_google_api_key');

		if ($key)
			$url = add_query_arg('key', $key, $url);

		wp_enqueue_script('google-maps', $url, array(), null, true);
		?>
		<div id="map"></div>
	</div>
</div>

<?php get_footer();

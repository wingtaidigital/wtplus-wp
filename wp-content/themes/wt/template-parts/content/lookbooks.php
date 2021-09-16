<section class="wt-gutter-bottom-x2">
	<?php
	global $is_parent;
	
	if ($is_parent)
	{
		$url = get_permalink();
		$title = get_the_title();
	}
	else
	{
		$url = get_permalink($post->post_parent);
		$title = get_the_title($post->post_parent);
	}
	
	if (!empty($_GET['ReturnURL']))
	{
		$url = add_query_arg('ReturnURL', $_GET['ReturnURL'], $url);
	
		if (!empty($_GET['Timeout']))
		{
			$url = add_query_arg('Timeout', (int) $_GET['Timeout'], $url);
		}
		
		$url = rawurldecode($url);
	}
	?>
	
	<?php
	$html = '';
	$brand = get_post_meta($post->ID, 'wt_brand', true);
	
	if ($brand)
	{
		$brand = get_post($brand);
		
		if ($brand)
		{
			$stores = get_posts([
				'post_type' => 'wt_store',
				'meta_key' => 'wt_brand',
				'meta_value' => $brand->ID,
				'posts_per_page' => 1,
				'fields' => 'ids'
			]);
			
			if ($stores)
			{
				ob_start();
				?>
				
				<div class="column shrink wt-gutter-vertical-half wt-clear-last-child-margin">
					<a href="<?php echo add_query_arg('brand', $brand->post_name, get_post_type_archive_link('wt_store')); ?>" class="wt-text-hover wt-text-bold wt-gutter-half">
                        Contact your nearest store today
                    </a>
				</div>
				
				<?php
				$html = ob_get_clean();
			}
		}
	}
	?>

    <?php if(empty($pagename)) { ?>
        <h1 class="wt-gutter-half wt-margin-bottom">
            <a href="<?php echo $url; ?>" class="wt-text-hover"><?php echo $title; ?></a>
        </h1>
    <?php } ?>

	<div class="wt-slick-fade wt-margin-bottom">
		<?php
		while ( have_rows('wt_gallery') )
		{
			the_row();

	//		$title = wp_kses_post(get_sub_field('title'));
	//		$content = wp_kses_post(get_sub_field('content'));
			$image = get_sub_field('image', false);
	//		$absolute_position = get_sub_field('absolute_position');
			$attr = [];

	//		if ($absolute_position)
	//		{
				list($url, $width) = wp_get_attachment_image_src($image, 'large');
				$attr = [
					'id' => 'image-' . $image,
					'data-resize' => 'image' . $image,
					'data-width' => $width,
					'class' => 'wt-lookbook'
				];
	//		}
			?>

			<article>
				<div class="wt-image-container text-center">
					<?php echo wp_get_attachment_image($image, 'large', false, $attr); ?>
	<!--				<div class="wt-gutter-vertical wt-center wt-clear-last-child-margin">-->
	<!--					<!--					<header class="wt-margin-bottom">--><?php ////echo $title; ?><!--<!--</header>-->
	<!--					--><?php //echo $content; ?>
	<!--				</div>-->
				</div>

				<?php //if (have_rows('wt_products')) { ?>
					<div class="row expanded wt-absolute<?php //echo $absolute_position ? 'expanded wt-absolute' : ''; ?>">
						<?php
						echo $html;

						while ( have_rows('wt_products') )
						{
							the_row();
							?>

							<div class="column shrink wt-gutter-vertical-half wt-clear-last-child-margin"
								<?php
								if (isset($width) /*&& $absolute_position*/)
								{
									$left = get_sub_field('left');

									if (is_numeric($left))
									{
										?>
										style="left: <?php echo $left / $width * 100; ?>%"
										<?php
									}
								}
								?>
							>
								<?php echo wp_kses_post(get_sub_field('content')); ?>
							</div>

							<?php
						}
						?>
					</div>
				<?php //} ?>
			</article>

			<?php
		}
		?>
	</div>
</section>

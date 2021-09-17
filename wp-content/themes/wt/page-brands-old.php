<?php get_header(); ?>

<?php
while (have_posts())
{
	the_post();
	
	$image = get_post_meta($post->ID, 'wt_image',true);
	
	if ($image)
	{
		list($url, $width, $height) = wp_get_attachment_image_src($image, 'large');
		?>
		
		<div class="text-center wt-image-container wt-margin-bottom-small">
			<?php
			echo wp_get_attachment_image($image, 'large', false, [
				'id' => 'image',
				'data-resize' => 'image',
				'data-width' => $width
			]);
			?>
			
			<?php
			$fields = [
				'width' => 'px',
				'height' => 'px',
				'left' => '%',
				'top' => '%',
				'color' => ''
			];
			
			while ( have_rows('wt_markers') )
			{
				the_row();
				
				$brand = get_sub_field('brand');
				
				if (!$brand)
					continue;
				?>
				
				<a href="<?php echo get_permalink($brand); ?>" data-anchor="<?php echo sanitize_text_field($brand->post_name); ?>" class="wt-marker show-for-medium" style="
				<?php
				foreach ($fields as $field => $unit)
				{
					$value = get_sub_field($field);
					
					if (!is_numeric($value))
						continue;
					
					if ($unit == '%')
					{
						if ($field == 'left')
							$divisor = $width;
						else
							$divisor = $height;
						
						$value = $value / $divisor * 100;
					}
//							else
//							{
//								$field = "min-$field";
//							}
					
					echo "$field: {$value}{$unit};";
				}
				?>
					">
					<span class="<?php echo get_post_meta($brand->ID, 'wt_case_sensitive', true) ? 'wt-case-sensitive' : ''; ?>"
						<?php
						$value = get_sub_field('color');
						
						if ($value)
							echo 'style="color: ' . sanitize_hex_color($value) . '"';
						?>
					>
						<?php echo sanitize_text_field($brand->post_title); ?>
					</span>
				</a>
				
				<?php
			}
			?>
			
			<div class="wt-center wt-clear-last-child-margin">
				<?php the_content(); ?>
			</div>
		</div>
		
		<?php
	}
	?>
	
	<div class="row collapse expanded wt-margin-bottom-small">
		<div class="column small-12 medium-6 text-center">
			<div class="hide-for-medium">
				<h1 class="wt-text-secondary" aria-selected="true">WT+ BRANDS</h1>
				<?php the_post_thumbnail(); ?>
			</div>
			
			<ul class="tabs vertical" id="tabs" data-responsive-accordion-tabs="accordion medium-tabs" data-deep-link="true" data-allow-all-closed="true">
				<li class="tabs-title is-active show-for-medium"><a href="#all" class="wt-text-secondary" aria-selected="true">WT+ BRANDS</a></li>
				
				<?php
				$query = wt_get_brand_query();
				
				while ($query->have_posts())
				{
					$query->the_post();
					
					$name = sanitize_text_field($post->post_name);
					?>
					
					<li class="tabs-title <?php echo get_post_meta($post->ID, 'wt_case_sensitive', true) ? 'wt-case-sensitive' : 'wt-upper'; ?>" id="trigger-<?php echo $name; ?>">
						<a href="#<?php echo $name; ?>" data-url="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</li>
					
					<?php
				}
				
				wp_reset_postdata();
				?>
				
			</ul>
		</div>
		<div class="column medium-6 flex-container align-justify">
			<div class="tabs-content vertical flex-container" data-tabs-content="tabs">
				<div class="tabs-panel is-active flex-container wt-background-cover" id="all"
				     style="background-image: url(<?php echo esc_url(the_post_thumbnail_url('large')); ?>"
				>
				</div>
				
				<?php
				while ($query->have_posts())
				{
					$query->the_post();
					?>
					
					<article class="tabs-panel -align-self-middle" id="<?php echo sanitize_text_field($post->post_name); ?>" itemscope itemtype="http://schema.org/Brand">
						<?php
						$header_image = get_field('wt_header_image');
						
						if ($header_image)
							echo wp_get_attachment_image($header_image[0]['ID'], 'large', false, [/*'class' => 'wt-margin-bottom'*/]);
						?>
						
						<div class="wt-gutter-x2 wt-gutter-vertical-x2">
							<div class="text-center wt-margin-bottom">
								<?php
								the_post_thumbnail('medium', [
									'class' => 'wt-brand-logo',
									'itemprop' => "logo"
								]);
								?>
							</div>
							
							<div class="wt-content wt-margin-bottom" itemprop="description">
								<?php the_content(); ?>
							</div>
							
							<div class="text-center">
								<a href="<?php the_permalink(); ?>" class="wt-text-hover wt-upper" itemprop="url">
									More on <span class="<?php echo get_post_meta($post->ID, 'wt_case_sensitive', true) ? 'wt-case-sensitive' : ''; ?>"><?php the_title(); ?></span>
								</a>
							</div>
						</div>
					</article>
					
					<?php
				}
				
				wp_reset_postdata();
				?>
				
			</div>
		</div>
	</div>
	
	<?php
}
?>

<?php get_footer();

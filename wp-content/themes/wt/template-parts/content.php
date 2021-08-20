<?php defined('ABSPATH') || exit; ?>

<?php if (empty(get_post_meta($post->ID, 'wt_hide_title', true))) { ?>
	<header class="text-center wt-gutter-vertical-x2">
		<h1 itemprop="headline"><?php the_title(); ?></h1>
		
		<?php
		$sub_title = get_post_meta($post->ID, 'wt_title', true);
		
		if ($sub_title)
		{
			?>
			
			<h2><?php echo sanitize_text_field($sub_title); ?></h2>
			
			<?php
		}
		?>
	</header>
<?php } elseif (!empty(get_post_meta($post->ID, 'wt_show_margin', true))) { ?>
	<div class="wt-gutter-bottom-x2"></div>
<?php } ?>

<div class="wt-content" itemprop="articleBody">
	<?php
	$allowed_domains = ['facebook.com', 'instagram.com', 'youtube.com', 'youtube-nocookie.com', 'embed.playbuzz.com'];
	
	while ( have_rows('wt_content') )
	{
		the_row();
		
		$layout = get_row_layout();
		$margin = get_sub_field('show_margin');
		$index = get_row_index();
		?>
		
		<div class="wt-layout-<?php esc_attr_e($layout); ?> <?php echo $margin ? 'wt-gutter-bottom-x2' : ''; ?>">
			<?php
			switch ($layout)
			{
				case 'banner':
					$image = get_sub_field('image');
					
					if ($image)
					{
						$portrait_image = get_sub_field('portrait_image');
						$responsive = $image && $portrait_image;
						?>
						
						<div class="text-center">
							<?php
							echo wp_get_attachment_image( $image, 'large', false, [
								'itemprop' => 'image',
								'class' => $responsive ? 'show-for-large' : ''
							] );
						
							if ($portrait_image)
							{
								echo wp_get_attachment_image( $portrait_image, 'large', false, [
									'itemprop' => 'image',
									'class' => 'hide-for-large'
								] );
							}
							?>
						</div>
						
						<?php
					}
					
					break;
				
				
				case 'blockquote':
					$quote = get_sub_field('quote');
					
					if ($quote)
					{
						$cite = get_sub_field('cite');
						?>
						
						<div class="row align-center">
							<div class="column medium-8">
								<blockquote>
									<?php echo $quote; ?>
									
									<?php if ($cite) { ?>
										<div class="clearfix">
											<cite class="float-right"><?php echo sanitize_text_field($cite); ?></cite>
										</div>
									<?php } ?>
								</blockquote>
							</div>
						</div>
						
						<?php
						
						$cite = get_sub_field('cite');
						
					}
					
					break;
				
				
				case 'gallery':
					$columns = get_sub_field('columns');
					
					if ($columns)
					{
	//								$count = count($columns);
	//								$class = 8;
	//
	//								if ($count === 2)
	//									$class = 4;
	
	//								wt_dump($columns);
						$has_caption = get_sub_field('has_caption');
						$width = absint(get_sub_field('width'));
						?>
						
						<div class="row align-center">
							<div class="column medium-<?php echo $width; ?>">
								<div class="row align-justify medium-unstack">
									<?php foreach ($columns as $column) { ?>
										<figure class="column wt-clear-last-child-margin">
											<?php echo wp_get_attachment_image( $column['image'], 'medium', false, ['itemprop' => 'image'] ); ?>
											
											<?php
											if ($has_caption)
											{
												if ($column['content'])
												{
													?>
													
													<figcaption>
														<?php echo wp_kses_post($column['content']); ?>
													</figcaption>
													
													<?php
												}
											}
											?>
										</figure>
									<?php } ?>
								</div>
							</div>
						</div>
						
						<?php
					}
					
					break;
					
					
				case 'embed':
					$embed = get_sub_field('embed');
					$content = get_sub_field($embed);
					$matched = preg_match('/src="([^"]+)"/', $content, $matches);
					
					if (!$matched)
						$matched = preg_match("/src='([^\"]+)'/", $content, $matches);;
					
					if ($embed == 'code')
					{
						if (!$matched)
							continue 2;
						
						$allowed = false;
						
						foreach ($allowed_domains as $domain)
						{
							if (strpos($matches[1], $domain))
							{
								$allowed = true;
								break;
							}
						}
						
						if (!$allowed)
							continue 2;
						
						$content = wp_kses($content, [
							'a' => [
								'href' => true,
								'style' => true,
								'target' => ['_blank'],
							],
							'blockquote' => [
								'class' => true,
								'data-instgrm-captioned' => true,
								'data-instgrm-version' => true,
								'style' => true,
							],
							'div' => [
								'style' => true,
								'class' => true,
								'data-id' => true
							],
							'iframe' => [
								'allowfullscreen' => true,
								'allowTransparency' => true,
								'frameborder' => true,
								'scrolling' => true,
								'src' => true,
								'style' => true,
								//									'width' => true,
								'height' => true,
							],
							'p' => [
								'style' => true,
							],
							'script' => [
								'async' => true,
								'defer' => true,
								'src' => true,
							],
							'time' => [
								'style' => true,
							],
						]);
						/*								$content = preg_replace('#(<script.*?>).*?(</script>)#', '$1$2', $content);*/
					}
					?>
					
					<div class="row align-center">
						<div class="column medium-8 text-center">
							<?php
							if (!empty($matches[1]) && (strpos($matches[1], 'youtube.com') !== false || strpos($matches[1], 'youtube-nocookie.com') !== false))
								$content = '<div class="responsive-embed widescreen">' . $content . '</div>';
							
							echo $content;
							?>
						</div>
					</div>
					
					<?php
					break;
				
				
				case 'h2':
					$content = get_sub_field('content');
					
					if ($content)
					{
						?>
						
						<div class="row align-center">
							<div class="column medium-8 wt-clear-last-child-margin"><?php echo wp_kses_post($content); ?></div>
						</div>
						
						<?php
					}
					
					break;
					
					
				case 'offer':
					$anchor = get_sub_field('anchor');
					$image = get_sub_field('image');
					$title = get_sub_field('title');
					$subtitle = get_sub_field('subtitle');
					$cta = get_sub_field('cta');
					$content = get_sub_field('content');
					$more_content = get_sub_field('more_content');
					
					if ($content && $more_content)
					{
						$pos = strrpos($content, '</');
						
						if ($pos)
							$content = substr($content, 0, $pos) . ' <button aria-hidden="true" data-toggle="offer-' . $index . '">Read <span class="wt-more">more</span><span class="wt-less">less</span></button>' . substr($content, $pos);
					}
					?>
					
					<?php if ($anchor) { ?>
						<div id="<?php echo sanitize_text_field($anchor); ?>"></div>
					<?php } ?>
				
					<div class="row align-center">
						<div class="column small-12 medium-10 large-7">
							<div class="row">
								<?php if ($image) { ?>
									<div class="column small-5 medium-5 text-center -wt-background-cover" <?php /* style="background-image: url(<?php echo wp_get_attachment_image_url($image, 'medium'); ?>)" */ ?>>
										<?php echo wp_get_attachment_image($image, 'medium', false/*, ['class' => 'wt-margin-bottom']*/); ?>
									</div>
								<?php } ?>
								<div class="column">
									<div class="flex-container flex-dir-column">
										<header class="flex-child-shrink wt-margin-top">
											<?php if ($title) { ?>
												<h1 class="wt-margin-bottom"><?php echo sanitize_text_field($title); ?></h1>
											<?php } ?>
											
											<?php if ($subtitle) { ?>
												<h2 class="wt-h2 wt-margin-bottom"><?php echo wp_kses_post($subtitle); ?></h2>
											<?php } ?>
										</header>
										<div class="flex-child-grow flex-container">
											<div class="align-self-middle">
												<?php if ($cta) { ?>
													<a class="button secondary expanded" href="<?php echo esc_url($cta['url']); ?>" target="<?php echo sanitize_text_field($cta['target']); ?>"><?php echo sanitize_text_field($cta['title']); ?></a>
												<?php } ?>
												
												<?php if ($content) { ?>
													<div class="wt-content" id="offer-<?php echo $index; ?>" data-toggler=".wt-expanded">
														<?php echo $content; ?>
														
														<?php if ($more_content) { ?>
															<div class="wt-less">
																<?php echo wp_kses_post($more_content); ?>
															</div>
														<?php } ?>
													</div>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<?php
					break;
				
					
				case 'post':
					$posts = get_sub_field('posts');
					
					if (!$posts)
						break;
					$type = get_sub_field('type');
					$query = new WP_Query([
						'post_type' => 'post',
						'post__in' => $posts,
						'posts_per_page' => -1
					]);
					?>
		
					<div class="<?php echo $type === 'vertical' ? 'row' : 'wt-row'; ?>">
						<?php
						while ($query->have_posts())
						{
							$query->the_post();
							?>
							
							<?php if ($type === 'vertical') { ?>
								<div class="column small-12 large-4 wt-margin-bottom wt-column">
									<div class="flex-container flex-dir-column">
							<?php } ?>
							
							<?php get_template_part('template-parts/content/' . $post->post_type); ?>
							
							<?php if ($type === 'vertical') { ?>
									</div>
								</div>
							<?php } ?>
							
							<?php
						}
						
						wp_reset_postdata();
						?>
					</div>
					
					<?php
					break;
				
					
				case 'product':
					$type = get_sub_field('type');
					
					get_template_part('template-parts/content/' . ($type === 'scroll' ? 'infinite-' : '') . 'products');
					
					break;
				
					
				case 'qa':
					$blocks = get_sub_field('blocks');
					
					if ($blocks)
					{
						?>
						
						<div class="row align-center">
							<div class="column medium-8">
								<?php
								foreach ($blocks as $block)
								{
									?>
									
									<div class="clearfix">
										<div class="wt-question"><?php echo wp_kses_post($block['question']); ?></div>
										<p class="wt-answer"><?php echo wp_kses_post($block['answer']); ?></p>
									</div>
									
									<?php
								}
								?>
							</div>
						</div>
						
						<?php
					}
					
					break;
					
				
				case 'slider':
					$slides = get_sub_field('slides');
					
					if ($slides)
					{
						$has_link = get_sub_field('has_link');
						$autoplay = get_sub_field('autoplay');
						$slick = $autoplay ? '{"autoplay": true, "autoplaySpeed": ' . absint($autoplay) * 1000 . '}' : '';
						?>
						
						<div class="wt-slick-arrows-single" data-slick='<?php echo $slick; ?>'>
							<?php
							foreach ($slides as $slide)
							{
								$link = $has_link && !empty($slide['link'])? $slide['link'] : [];
								$responsive = $slide['image'] && !empty($slide['portrait_image']);
								?>
								
								<div class="text-center">
									<?php if ($link) { ?>
										<a href="<?php echo esc_url($link['url']); ?>" <?php echo empty($link['target']) ? '' : 'target="' . esc_attr($link['target']) . '"'; ?> <?php echo empty($link['title']) ? '' : 'title="' . esc_attr($link['title']) . '"'; ?>>
									<?php } ?>
									
									<?php
									echo wp_get_attachment_image($slide['image'], 'large', false, [
										'itemprop' => 'image',
										'class' => $responsive ? 'show-for-large' : ''
									]);
									
									if (!empty($slide['portrait_image']))
									{
										echo wp_get_attachment_image($slide['portrait_image'], 'large', false, [
											'itemprop' => 'image',
											'class'    => $responsive ? 'hide-for-large' : ''
										]);
									}
									?>
									
									<?php if ($link) { ?>
										</a>
									<?php } ?>
								</div>
								
								<?php
							}
							?>
						</div>
						
						<?php
					}
					
					break;
					
					
				case 'wysiwyg':
					$content = get_sub_field('content');
					
					if ($content)
					{
						?>
						
						<div class="row align-center">
							<div class="column medium-8 wt-clear-last-child-margin"><?php echo wp_kses_post($content); ?></div>
						</div>
						
						<?php
					}
					
					break;
			}
			?>
		</div>
		<?php
	}
	?>
</div>

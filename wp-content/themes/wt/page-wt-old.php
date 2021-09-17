<?php get_header(); ?>

<?php
while (have_posts())
{
	the_post();
	?>
	
	<div class="row align-center">
		<div class="column small-12">
			<section>
				<div class="text-center wt-gutter-vertical-x2">
					<?php the_post_thumbnail('large'); ?>
				</div>
				
				<div class="wt-gutter-vertical-x2">
					<?php the_content(); ?>
				</div>
			</section>
			
			<?php
			while ( have_rows('wt_sections') )
			{
				the_row();
				
				$title = get_sub_field('title');
				$menu = get_sub_field('menu');
				
				if (!$menu)
					$menu = $title;
				?>
				
				<div id="<?php echo sanitize_title($menu); ?>"></div>
				<section class="wt-gutter-vertical-x2">
					<h1 class="text-center wt-h2 wt-margin-bottom"><?php echo sanitize_text_field($title); ?></h1>
					
					<?php
					$allowed_tags = wp_kses_allowed_html( 'post' );
					$allowed_tags['ul'] = array_merge($allowed_tags['ul'], [
						'data-accordion' => 1,
						'data-multi-expand' => 1,
						'data-allow-all-closed' => 1
					]);
					$allowed_tags['li'] = array_merge($allowed_tags['li'], [
						'data-accordion-item' => 1,
					]);
					$allowed_tags['div'] = array_merge($allowed_tags['div'], [
						'data-tab-content' => 1,
					]);
					echo wp_kses(get_sub_field('content'), $allowed_tags);
					?>
				</section>
				
				<?php
			}
			?>
			
			<?php
			$query = new WP_Query([
				'post_type' => 'page',
				'post_name__in' => ['faq-frequently-asked-questions', 'faqs-frequently-asked-questions', 'faqs', 'faq', 'frequently-asked-questions']
			]);
			
			while ($query->have_posts())
			{
				$query->the_post();
				?>
				
				<div id="faqs"></div>
				<section class="wt-gutter-vertical-x2">
					<h1 class="text-center wt-h2 wt-margin-bottom">+ FAQs</h1>
					
					<ul class="accordion" data-accordion data-multi-expand="true" data-allow-all-closed="true">
						<?php
						$dom = new DOMDocument();
						$dom->loadHTML('<?xml encoding="UTF-8">' . wpautop(wp_kses_post(get_the_content())));
						$h2s = $dom->getElementsByTagName('h2');
						$to_remove = [];
						
						foreach ($h2s as $node)
						{
							if (!$to_remove)
							{
								$to_remove[] = $node;
								continue;
							}
							
							$to_remove[] = $node;
							$node = $node->nextSibling;
							
							while ($node)
							{
								$to_remove[] = $node;
								$node = $node->nextSibling;
							}
						}
						
						foreach ($to_remove as $node)
							$node->parentNode->removeChild($node);
//						echo $dom->saveHTML();
						$h3s = $dom->getElementsByTagName('h3');
						
						foreach ($h3s as $h3)
						{
							$li = $dom->createElement('li');
							$attr = $dom->createAttribute('data-accordion-item');
							$li->appendChild($attr);
							$attr = $dom->createAttribute('class');
							$attr->value = 'accordion-item';
							$li->appendChild($attr);
							
							$a = $dom->createElement('a');
							$attr = $dom->createAttribute('class');
							$attr->value = 'accordion-title';
							$a->appendChild($attr);
							
							$div = $dom->createElement('div');
							$attr = $dom->createAttribute('data-tab-content');
							$div->appendChild($attr);
							$attr = $dom->createAttribute('class');
							$attr->value = 'accordion-content';
							$div->appendChild($attr);
							
							$node = $h3->nextSibling;
							$to_append = [];
							
							while ($node)
							{
//								wt_dump($node);
								$to_append[] = $node;
								$node = $node->nextSibling;
							}
							
							foreach ($to_append as $node)
								$div->appendChild($node);
							
							$h3->parentNode->insertBefore($li, $h3);
							$li->appendChild($a);
							$a->appendChild($h3);
							$li->appendChild($div);
						}
						
						echo $dom->saveHTML();
						?>
					</ul>
				</section>
				
				<?php
				break;
			}
			
			wp_reset_postdata();
			?>
		</div>
	</div>
	
	<?php
}
?>

<?php get_footer();

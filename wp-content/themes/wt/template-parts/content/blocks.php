<?php if (have_rows('wt_blocks')) { ?>
	<section class="row expanded collapse" id="blocks">
		<?php
		while ( have_rows('wt_blocks') )
		{
			the_row();
			
			$fields = wt_get_content_fields();
			$fields['width'] = get_sub_field('width');
//			$background_color = get_sub_field('background_color');
//			$cta = get_sub_field('cta');
//			$width = get_sub_field('width');
//			$align = esc_attr(get_sub_field('align'));
//			$image = '';
//
////			if (empty($align))
////				$align = 'center';
//
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
//				$portraitImage = get_sub_field('portrait_image');
//				$title = get_sub_field('title');
//				$content = get_sub_field('content');
//				$url = get_sub_field('url');
//			}
			
			if ($fields['image'] || $fields['title'] || $fields['content'])
			{
				$id = 'block-'. get_row_index();
				?>
				
				<article id="<?php echo $id; ?>" class="column small-12 <?php echo $fields['width'] == '50%' ? 'medium-6' : ''; ?> flex-container wt-background-cover wt-margin-bottom-small">
					<style>
						#<?php echo $id; ?>
						{
						<?php if ($fields['image']) { ?>
							background-image: url(<?php echo $fields['image']; ?>);
						<?php } else if ($fields['background_color']) { ?>
							background-color: <?php echo $fields['background_color']; ?>; )
						<?php } ?>
						}
						
						<?php if ($fields['portrait_image']) { ?>
						@media print, screen and (max-width: 63.9375em)
						{
						#<?php echo $id; ?>
						{
							background-image: url(<?php echo $fields['portrait_image']; ?>);
						}
						}
						<?php } ?>
					</style>
					
					<?php if ($fields['url'] && !$fields['cta']) { ?>
						<a href="<?php echo $fields['url']; ?>" class="flex-child-auto flex-container"><?php echo $fields['cta']; ?>
					<?php } ?>
					
					<div class="row expanded align-middle <?php echo $fields['align'] == 'center' ? '' : 'align-' . $fields['align']; ?> flex-child-auto is-collapse-child">
						<div class="column small-12 <?php echo $fields['align'] == 'center' ? '' : 'medium-6'; ?> text-center">
							<?php if ($fields['title']) { ?>
								<header class="wt-margin-bottom"><?php echo $fields['title']; ?></header>
							<?php } ?>
							
							<?php if ($fields['content']) { ?>
								<div class="wt-content"><?php echo $fields['content']; ?></div>
							<?php } ?>
							
							<?php if ($fields['cta'] && $fields['url']) { ?>
								<a href="<?php echo $fields['url']; ?>" class="wt-text-hover wt-clear-margin"><?php echo $fields['cta']; ?></a>
							<?php } ?>
						</div>
					</div>
					
					<?php if ($fields['url'] && !$fields['cta']) { ?>
						</a>
					<?php } ?>
				</article>
				
				<?php
			}
			
		}
		?>
	</section>
<?php }

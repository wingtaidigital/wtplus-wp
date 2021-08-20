<article id="<?php echo esc_attr($post->post_name); ?>" class="wt-margin-bottom" itemscope itemtype="http://schema.org/Event">
	<div class="row wt-event">
		<div class="column small-12 large-6 wt-background-cover" style="background-image: url(<?php the_post_thumbnail_url(); ?>)"></div>
		<div class="column small-12 large-6 wt-gutter-x2 wt-gutter-vertical-x2">
			<?php ob_start(); ?>
			
			<h1 class="wt-h2 wt-margin-bottom" itemprop="name"><?php the_title(); ?></h1>
			
			<dl>
				<?php
				$meta = ['date', 'time', 'location'];
				
				foreach ($meta as $key)
				{
					$value = get_post_meta($post->ID, 'wt_' . $key, true);
					
					if (empty($value))
						continue;
					?>
					
					<dt><?php echo ucfirst($key); ?></dt>
					<dd><?php echo sanitize_text_field($value); ?></dd>
					
					<?php
				}
				?>
			</dl>
			
			<?php
			$html = ob_get_clean();
			
			echo $html;
			?>
			
			<?php the_content(); ?>
			
			<?php
			$can_register = get_post_meta($post->ID, 'wt_can_register', true);
			
			if ($can_register)
			{
//				global $cta;
				$showcase = get_page_by_path('showcase');
				$cta = get_post_meta($showcase->ID, 'wt_event_cta', true);
				
				if (is_user_logged_in())
				{
					$user_id = get_current_user_id();
					$registrations = get_user_meta($user_id, 'wt_registrations', true);
//					wt_dump($registrations);
					if (!is_array($registrations))
						$registrations = [];
					
					$registered = array_key_exists($post->ID, $registrations);
					
					if ($registered && $registrations[$post->ID] == 2)
						$registered = false;
//					wt_dump($registered);
					?>
					
					<a data-open="event-<?php echo $post->ID; ?>" class="wt-text-hover"><?php echo $cta; ?></a>
					
					<article class="reveal wt-event" id="event-<?php echo $post->ID; ?>" data-reveal data-deep-link="true" itemscope itemtype="http://schema.org/Event">
						<div class="wt-container-small">
							<?php if (!$registered) { ?>
								<div class="wt-not-submitted">
									<div class="wt-background-light-gray wt-gutter wt-gutter-vertical wt-clear-last-child-margin">
										<?php echo $html; ?>
									</div>
									
									<div class="wt-gutter-vertical">
										You are registering your interest for the above event. Click on the button below to confirm.
									</div>
									
									<form method="post" data-route="wt/v1/events/<?php echo $post->ID; ?>/register" data-hide="1">
										<button type="submit" class="button secondary expanded">REGISTER MY INTEREST</button>
									</form>
								</div>
							<?php } ?>
							
							<div class="wt-submitted <?php echo $registered ? '' : 'hide'; ?>">
								<strong>You have successfully registered your interest.</strong><br>
								We will be in touch with you. Alternatively, check the status of application at <a href="<?php echo get_author_posts_url($user_id); ?>">your dashboard</a>.
							</div>
						</div>
						
						<button class="close-button" data-close aria-label="Close modal" type="button">
							<span aria-hidden="true" class="wt-sans-serif">&times;</span>
						</button>
					</article>
					
					<?php
				}
				else
				{
					?>
					
					<a data-open="showcase-login-modal" data-id="<?php echo $post->ID; ?>"><?php echo $cta; ?></a>
					
					<?php
				}
			}
			?>
		</div>
	</div>
</article>

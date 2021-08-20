<?php defined('ABSPATH') || exit; ?>

<?php
$user_id = get_current_user_id();
$registrations = get_user_meta($user_id, 'wt_registrations', true);
$args = [
	'post_type' => 'wt_event',
	'posts_per_page' => -1,
	'meta_query' => [
		[
			'key'   => 'wt_can_register',
			'value' => '1'
		],
	],
	'orderby' => [
		'wt_date_from' => 'ASC',
	],
];

if ($registrations)
{
	$registered_events = array_keys($registrations);
	$registered_args = ['post__in' => $registered_events];
	$max = get_option('options_wt_event_registrations_max_age');
	
	if ($max)
	{
		$max = strtotime('-' . absint($max) . ' years');
		$registered_args['meta_query'][] = [
			'key'   => 'wt_date_to',
			'value' => gmdate( 'Y-m-d H:i:s', ( $max + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) ),
			'compare' => '>=',
			'type' => 'DATETIME'
		];
	}
//	wt_dump(array_merge_recursive($args, $registered_args));
	$query = new WP_Query(array_merge_recursive($args, $registered_args));
	
	$i = 0;
	
	foreach ($registrations as $event => $status)
	{
		if ($status == 2)
			unset($registered_events[$i]);
		
		$i++;
	}
	
	unset($args['post__in']);
	
	$args['post__not_in'] = $registered_events;
	
	if ($query->have_posts())
	{
		$statuses = [
			-1 => 'Pending approval',
			0 => 'Rejected',
			1 => 'Approved',
			2 => 'Cancelled',
		]
		?>
		
		<section class="wt-gutter-bottom-x2">
			<h1 class="wt-h6 wt-upper wt-gutter-bottom">Events registered</h1>
			
			<table class="wt-table">
				<thead>
					<tr class="wt-upper">
						<th>Event</th>
						<th>Date</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php
					while ($query->have_posts())
					{
						$query->the_post();
						
						$event_id = get_the_ID();
						
						if (empty($registrations[$event_id]))
							continue;
						
						$status = $registrations[$event_id];
						
						if (empty($statuses[$status]))
							continue;
						
						$title = get_the_title();
						?>
						
						<tr>
							<td><?php echo $title; ?></td>
							<td><?php echo sanitize_text_field(get_post_meta($event_id, 'wt_date', true)); ?></td>
							<td>
								<?php if ($status == -1) { ?>
									<div class="wt-not-submitted">
										<?php echo $statuses[$status]; ?>
										
										<form data-route="wt/v1/events/<?php echo $event_id; ?>/cancel" data-confirm="Are you sure you want to cancel your application for <?php echo $title; ?>?" data-hide="1" class="wt-inline">
											<button type="submit" data-submit="(Cancel application)" data-submitting="(Cancelling...)" data-submitted="(Cancelled)" class="wt-text-secondary wt-text-hover">
												(Cancel application)
											</button>
										</form>
									</div>
									
									<div class="wt-submitted hide">Cancelled</div>
								<?php } else { ?>
									<?php echo $statuses[$status]; ?>
								<?php } ?>
							</td>
						</tr>
						
						<?php
					}
					
					wp_reset_query();
					?>
				</tbody>
			</table>
		</section>
		
		<?php
	}
}

$args['meta_query'][] = [
	'key'   => 'wt_date_to',
	'value' => current_time('mysql'),
	'compare' => '>=',
	'type' => 'DATETIME'
];
//wt_dump($args);
$query = new WP_Query($args);
	
if ($query->have_posts())
{
	?>
	
	<section class="">
		<h1 class="wt-h6 wt-upper wt-gutter-bottom">Sign up for events</h1>
		
		<table class="wt-table">
			<thead>
				<tr class="wt-upper">
					<th>Event</th>
					<th>Date</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
				while ($query->have_posts())
				{
					$query->the_post();
					?>
					
					<tr>
						<td><a data-open="event-<?php echo $post->ID; ?>" class="wt-text-hover"><?php the_title(); ?></a></td>
						<td><?php echo sanitize_text_field(get_post_meta($post->ID, 'wt_date', true)); ?></td>
						<td>
							<a data-open="event-<?php echo $post->ID; ?>" class="wt-text-hover">Sign up now</a>
							<article class="reveal wt-event" id="event-<?php echo $post->ID; ?>" data-reveal>
								<div class="wt-container-small">
									<div class="wt-not-submitted">
										<h1 class="text-center wt-h3 wt-margin-bottom"><?php the_title(); ?></h1>
										
										<?php the_post_thumbnail('medium', ['class' => 'wt-margin-bottom']); ?>
										
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
										
										<div class="wt-gutter-bottom wt-clear-last-child-margin">
											<?php the_content(); ?>
										</div>
										
										<div class="wt-gutter-bottom">
											You are registering your interest for the above event. Click on the button below to confirm.
										</div>
										
										<form method="post" data-route="wt/v1/events/<?php echo $post->ID; ?>/register" data-hide="1">
											<button type="submit" class="button secondary expanded">REGISTER MY INTEREST</button>
										</form>
									</div>
									
									<div class="wt-submitted hide">
										<strong>You have successfully registered your interest.</strong><br>
										We will be in touch with you. Alternatively, check the status of application at <a href="<?php echo get_author_posts_url($user_id); ?>">your dashboard</a>.
									</div>
								</div>
								
								<button class="close-button" data-close aria-label="Close modal" type="button">
									<span aria-hidden="true" class="wt-sans-serif">&times;</span>
								</button>
							</article>
						</td>
					</tr>
					
					<?php
				}
				
				wp_reset_query();
				?>
			</tbody>
		</table>
	</section>

<?php
}
?>

<?php
defined('ABSPATH') || exit;

$badges = wt_crm_get_badges();
//wt_dump($badges);
if ($badges)
{
	global $profile;
	
	$member_badges = wt_crm_get_list_field_value($profile['DynamicFieldLists'], 'Badges');
	
	if ($member_badges)
		$member_badges = explode(',', $member_badges);
	else
		$member_badges = [];
//	wt_dump($member_badges);
	?>
	
	<section>
		<h1 class="wt-h6 wt-upper wt-margin-bottom">wt+ Achievement Badges</h1>
		<p>Collect as many badges as you can!</p>
		
		<?php foreach ($badges as $category => $category_badges) { ?>
			<section class="row wt-badges" data-equalizer data-equalize-on-stack="true">
				<div class="column small-6 medium-3 text-center wt-gutter-vertical-half">
					<div class="wt-relative" data-equalizer-watch>
						<h1 class="wt-h6 wt-center"><?php echo sanitize_text_field($category); ?></h1>
					</div>
				</div>
				<div class="column small-6 medium-9">
					<div class="row small-up-1 medium-up-3 xlarge-up-4">
						<?php
						foreach ($category_badges as $badge)
						{
		//					$has_badge = in_array($badge['SystemCode'], $member_badges);
							$name = sanitize_text_field($badge['Name']);
							$name_attr = esc_attr($name);
							?>
							
							<figure class="column text-center wt-gutter-vertical-half" data-equalizer-watch>
								<img src="<?php echo esc_url($badge['Description']); ?>" class="wt-badge <?php //echo $has_badge ? '' : 'disabled'; ?>" alt="<?php echo $name_attr; ?>" title="<?php echo $name_attr; ?>">
								<figcaption><?php echo $name; ?></figcaption>
							</figure>
							
							<?php
						}
						?>
					</div>
				</div>
			</section>
		<?php } ?>
	</section>
	
	<?php
}

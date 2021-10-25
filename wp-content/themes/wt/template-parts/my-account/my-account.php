<?php
defined('ABSPATH') || exit;

global $profile, $card;

//$card['TotalPointsBAL'] = $card['TotalPointsBAL'];
?>

<section>
	<h1 class="wt-h6 wt-upper wt-margin-bottom">Membership at a glance</h1>
	
	<div class="row wt-bold wt-margin-bottom-x2">
		<div class="column small-6 medium-5 flex-container flex-dir-column">
			<div class="wt-background-light-gray wt-gutter wt-gutter-vertical wt-upper text-center flex-child-grow">
				<div class="wt-text-secondary wt-h3"><?php echo $card['TotalPointsBAL']; ?></div>
				<span class="">Balance Points</span>
			</div>
		</div>
		
		<?php
		$enquiry = wt_crm_api([
			'Command'                    => 'CARD ENQUIRY',
			'CardNo'                     => $_SESSION['wt_crm']['CardNo'],
			'RetrieveNettToNextTier'     => true,
//			"RetrieveMembershipInfo"     => true,
//			"RetrieveActiveVouchersList" => true,
//			"FilterBy_VoucherNo"         => "",
//			"FilterBy_VoucherType"       => "",
//			"FilterBy_ValidFrom"         => "",
//			"FilterBy_ValidTo"           => "",
//			"FilterBy_TriggerSource"     => "",
//			"SortOrder"                  => "ASC",
//			"SortBy_VoucherNo"           => false,
//			"SortBy_VoucherType"         => false,
//			"SortBy_ValidFrom"           => false,
//			"SortBy_ValidTo"             => true,
//			"PageNumber"                 => 1,
//			"PageCount"                  => 99,
//			"RetrieveReceiptMessage"     => true,
//			"RequestDynamicColumnLists"  => [],
//			"RequestDynamicFieldLists"   => [],
		]);
//		wt_dump($enquiry);
		
		if (!is_wp_error($enquiry) && !empty($enquiry['CardInfo']['RewardCycleLists']) && is_array($enquiry['CardInfo']['RewardCycleLists']))
		{
			$date = '9999-12-31T23:59:59';
			
			foreach ($enquiry['CardInfo']['RewardCycleLists'] as $cycle)
			{
				if ($cycle['PointsType'] === 'Current' || $cycle['PointsExpiringDate'] > $date)
					continue;
				
				$date = $cycle['PointsExpiringDate'];
				$grace = $cycle;
			}
			
			if (isset($grace))
			{
				?>
				
				<div class="column small-6 medium-5 flex-container flex-dir-column">
					<div class="wt-background-light-gray wt-gutter wt-gutter-vertical wt-upper flex-child-grow flex-container align-middle">
						<div>
							<span class="wt-text-secondary"><?php echo $grace['PointsBALValue']; ?></span> points expiring on
							<?php echo wt_format_date($grace['PointsExpiringDate'], 'display'); ?>
						</div>
					</div>
				</div>
				
				<?php
			}
		}
		?>
	</div>
	
	<?php
	$tiers = [
		'Silver'  => [
			'next_tier'  => 300,
			'next_label' => 'Gold'
			
		],
//		'Gold'    => [
//			'renew'      => 300,
//			'next_tier'  => 1000,
//			'next_label' => 'Premium'
//		],
//		'Premium' => [
//			'renew'      => 1000,
//			'next_label' => 'Renew Premium'
//		],
	];
	$tier = $tiers[ucfirst($card['TierCode'])];
	$max = empty($tier['next_tier']) ? $tier['renew'] : $tier['next_tier'];
//	$ratio = explode(':', $card['DollarToPointsRatio']);
//	$current = $card['TotalPointsBAL'] / $ratio[0] * $ratio[1];
	$current = $card['CurrentNetSpent'];
	$current_percentage = $current < $max ? $current / $max * 100 : 100;
	$renew = empty($tier['renew']) ? 0 : number_format_i18n($tier['renew'] - $current, 2);
	$current = number_format_i18n($current, 2);
	$expiry_date = wt_crm_format_date($card['ExpiryDate'], 'display');
	/*
	?>
	
	<div class="wt-progress-container wt-background-light-gray">
		<div class="progress secondary" role="progressbar" tabindex="0" aria-valuenow="25" aria-valuemin="0" aria-valuemax="<?php echo $max; ?>">
		    <span class="progress-meter" style="width: <?php echo $current_percentage; ?>%; <?php //z-index: 9 ?>">
			    <p class="progress-meter-text">$<?php echo $current; ?></p>
		    </span>
			
			<span class="progress-meter wt-transparent" style="width: 0%; <?php //background-color: #c0c0c0; z-index: 8 ?>">
			    <p class="progress-meter-text"><?php echo $card['TierCode']; ?></p>
		    </span>
			
			<?php if ($card['TierCode'] === 'Gold') { ?>
				<span class="progress-meter wt-transparent" style="width: 30%; <?php //background-color: #c0c0c0; z-index: 8 ?>">
			    <p class="progress-meter-text">$300<br>Renew Gold</p>
		    </span>
			<?php } ?>
			
			<span class="progress-meter wt-transparent" style="width: 100%; <?php //background: linear-gradient(to right, gold 30%, #efeff3 100%); ?>">
			    <p class="progress-meter-text">$<?php echo $max; ?><br><?php echo $tier['next_label']; ?></p>
		    </span>
		</div>
	</div> <?php */ ?>
	
	<div>Current Tier: <?php echo $card['TierCode']; ?></div>
	
	<?php if ($renew > 0 && $card['TierCode'] !== 'Silver') { ?>
		<div>
			Spend <span class="wt-text-secondary">$<?php echo $renew; ?></span> by <span class="wt-text-secondary"><?php echo $expiry_date; ?></span> to renew your
			<?php echo $card['TierCode']; ?> Membership
		</div>
	<?php } ?>
	
	<?php if (!is_wp_error($enquiry) && $enquiry['CardInfo']['NettToNextTier']) { ?>
		<div>
			Spend <span class="wt-text-secondary">$<?php echo number_format_i18n($enquiry['CardInfo']['NettToNextTier'], 2); ?></span> by
			<span class="wt-text-secondary"><?php echo $expiry_date; ?></span>
			to upgrade to <?php echo $tier['next_label']; ?>
		</div>
	<?php } ?>
	
<!--	--><?php //echo $card['TierCode']; ?><!-- Tier expiry date: --><?php //echo wt_crm_format_date($card['ExpiryDate'], 'display'); ?>
	
	<!--	Member since: --><?php //echo wt_crm_format_date($profile['JoinDate'], 'display'); ?>
	
	<hr>
</section>

<section>
	<h1 class="wt-h6 wt-upper wt-margin-bottom">Personal Details</h1>
	
	<table class="unstriped wt-width-auto">
		<tbody>
			<?php
			global $profile;
			
			$fields = [
				[
					'label' => 'First name',
					'value' => empty($profile['fields']['FirstName']) ? '' : $profile['fields']['FirstName'],
				],
				[
					'label' => 'Last name',
					'value' => empty($profile['fields']['LastName']) ? '' : $profile['fields']['LastName'],
				],
				[
					'label' => 'Mobile',
					'value' => $profile['MobileNo'],
				],
				[
					'label' => 'Email',
					'value' => function_exists('wt_is_dummy_email') && wt_is_dummy_email($profile['Email']) ? '' : $profile['Email'],
				],
				[
					'label' => 'Date of Birth',
					'value' => empty($profile['DOB']) ? '' : wt_crm_format_date($profile['DOB'], 'display'),
				],
				[
					'label' => 'Address',
					'value' => wt_get_home_address($profile),
				],
			];
			
			foreach ($fields as $field)
			{
				if (empty($field['value']))
					continue;
				?>
				
				<tr>
					<th class="text-left"><?php echo sanitize_text_field($field['label']); ?>:</th>
					<td><?php echo sanitize_text_field($field['value']); ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	
	<?php /* <ul class="fa-ul">
						<li></li>
						<li><i class="fa-li fa fa-user"></i><?php echo sanitize_text_field($profile['IC']); ?></li>
						
						<?php
						$mobile = wt_get_mobile_number($profile);
						
						if (!empty($mobile))
						{
							?>
							
							<li><i class="fa-li fa fa-mobile"></i><?php echo $mobile; ?></li>
							
							<?php
						}
						?>
						
						<li><i class="fa-li fa fa-envelope"></i><?php echo sanitize_text_field($profile['Email']); ?>
						</li>
						<li><i class="fa-li fa fa-birthday-cake"></i><?php echo wt_crm_format_date($profile['DOB']); ?>
						</li>
						
						<?php
						$address = [];
						
						foreach ($profile['Addresses'] as $add)
						{
							foreach ($add as $key => $value)
							{
								if (empty($value) || $key == 'AddressType')
									continue;
								
								$address[] = $value;
							}
							
							break;
						}
						
						if (!empty($address))
						{
							?>
							
							<li><i class="fa-li fa fa-map-marker"></i><?php echo implode(', ', $address); ?></li>
							
							<?php
						}
						?>
					
					</ul> */ ?>
	
	<a href="<?php echo home_url('my-account/profile'); ?>" class="wt-text-hover wt-margin-bottom">Click to update profile ></a>
	
<!--	<hr>-->
</section>

<?php
/*$badges = wt_crm_get_badges();
//$badges = false;
if ($badges)
{
	$member_badges = wt_crm_get_list_field_value($profile['DynamicFieldLists'], 'Badges');
	
	if ($member_badges)
		$member_badges = explode(',', $member_badges);
	else
		$member_badges = [];
//	wt_dump($member_badges);
	?>
	
	<section>
		<h1 class="wt-h6 wt-upper wt-margin-bottom">Your Achievement Badges</h1>
		
		<?php
		if ($member_badges)
		{
			foreach ($badges as $category => $category_badges)
			{
				foreach ($category_badges as $i => $badge)
				{
					if (!in_array($badge['SystemCode'], $member_badges))
						unset($badges[$category][$i]);
				}
			}
			
			$badges = array_filter($badges);
			?>
			
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
			
			<?php
		}
		?>
		
		<a href="<?php echo home_url('my-account/badges/'); ?>">View all badges</a>
	</section>
	
	<?php
}*/
?>

<?php
/*$memberships = wt_crm_api('profile/' . $_SESSION['wt_crm']['CustomerNumber'] . '/memberships', ['method' => 'GET']);
//wt_dump($memberships);

if (!is_wp_error($memberships))
{
?>
	<section class="">
		<h1 class="wt-gutter-vertical-x2 wt-h6">MEMBERSHIP TIER</h1>
		
		<?php
		foreach ($memberships as $membership)
		{
			if ($membership['Status'] == 'ACTIVE')
				break;
		}
		
		$type = sanitize_text_field($membership['Type']);
		
		switch($membership['Status'])
		{
			case 'ACTIVE':
				$label_class = 'success';
				break;
			
			case 'UPGRADED':
				$label_class = 'warning';
				break;
			
			case 'TERMINATED':
				$label_class = 'alert';
				break;
			
			default:
				$label_class = '';
		}
		?>
		
		<article class="row collapse wt-membership">
			<div class="column small-12 medium-4 flex-container text-center wt-text-white wt-gutter wt-gutter-vertical wt-background-<?php echo lcfirst($type); ?>">
				<h1 class="flex-child-auto align-self-middle wt-h3 wt-text-weight-normal">
					<?php echo $type; ?>
				</h1>
			</div>
			<div class="column small-12 medium-8 wt-background-light-gray wt-gutter wt-gutter-vertical">
				<div class="wt-upper"><strong><?php echo $type; ?> Tier</strong></div>
				
				<?php echo sanitize_text_field($membership['MemberNo']); ?>
				
				<div class="wt-margin-bottom"><span class="label <?php echo $label_class; ?>"><?php echo sanitize_text_field($membership['Status']); ?></span></div>
				
				<i class="icon icon-perm_contact_calendar" aria-hidden="true"></i>
				From: <?php echo wt_crm_format_date($membership['ValidFrom'], 'display'); ?>
				
				<?php if ($type != 'Silver') { ?>
					<br>
					Expiry: <?php echo wt_crm_format_date($membership['ExpiryDate'], 'display'); ?>
				<?php } ?>
				
				<?php /*if (date('Y', strtotime($membership['ExpiryDate'])) < 9999)
									{ ?>
										<?php echo wt_crm_format_date($membership['ExpiryDate']); ?>
									<?php }* ?>
			</div>
		</article>
	</section>
<?php
}*/

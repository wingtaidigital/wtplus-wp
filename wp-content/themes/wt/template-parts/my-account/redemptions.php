<?php defined('ABSPATH') || exit; ?>

<?php
//	if (!empty($_SESSION['wt_crm']['CustomerNumber']))
//	{
//		$summary = wt_crm_api('profile/' . $_SESSION['wt_crm']['CustomerNumber'] . '/summary', ['method' => 'GET']);
//		//	wt_dump($summary);
//
//		if (!is_wp_error($summary) && is_array($summary['MembershipSummaries']))
//		{
//			foreach ($summary['MembershipSummaries'] as $membership)
//			{
//				if ($membership['Status'] == 'ACTIVE' && !empty($membership['AccountSummaries'][0]))
//				{
//					$account = $membership['AccountSummaries'][0];
//					$_SESSION['wt_crm']['MemberNumber'] = $membership['MemberNo'];
//					$_SESSION['wt_crm']['Balance'] = $account['Balance'];
//					break;
//				}
//			}
//		}
//	}

global $account;

//$transient_name = 'wt_' . $account['MemberType'] . '_vouchers';

//if (isset($_GET['clear_cache']))
//	$transient = null;
//else
//	$transient = get_transient($transient_name);
//
//if ($transient)
//{
//	$vouchers = json_decode($transient, true);
//}
//else
//{
	$vouchers = wt_crm_api('vouchertype/search?getImage=true&memberType=' . $account['MemberType'], ['method' => 'GET']);//status=ACTIVE&memberType=SILVER
//}

if (!is_wp_error($vouchers) && $vouchers && is_array($vouchers))
{
	$now = wt_crm_format_date();
	$logo = '<div class="wt-logo-container">' . get_custom_logo() . '</div>';
//	$logo = get_theme_mod( 'custom_logo' );
//
//	if ($logo)
//		$logo = wp_get_attachment_image( $logo, 'medium', false, ['class' => 'custom-logo']);
	
//	if (!$transient)
//		set_transient($transient_name, json_encode($vouchers));
	
	$url = home_url('my-account/vouchers/');
	?>

	<section class="row" data-equalizer>
		<?php
		foreach ($vouchers as $voucher)
		{
//				if ($voucher['CostCurrency'] != 'wt+ Points')
//				if ($voucher['CostMoneyID'] != 13)
			//!$voucher['CostValue'] ||
			if (
				$voucher['Status'] != 'ACTIVE' ||
				$voucher['StartDate'] && $voucher['StartDate'] > $now ||
				$voucher['EndDate'] && $voucher['EndDate'] < $now ||
				$voucher['ExpiryDate'] && $voucher['ExpiryDate'] < $now
			)
			{
//				wt_dump($voucher);
//				wt_dump($voucher['StartDate']);
//				wt_dump($voucher['EndDate']);
//				wt_dump($voucher['Memberships']);
				continue;
			}
			
			$voucher['VoucherCode'] = sanitize_text_field($voucher['VoucherCode']);
			$voucher['VoucherName'] = sanitize_text_field($voucher['VoucherName']);
			$voucher['CostValue'] = sanitize_text_field($voucher['CostValue']);
			$voucher['CostCurrency'] = sanitize_text_field($voucher['CostCurrency']);
			$voucher['LongDescription'] = wpautop(sanitize_text_field($voucher['LongDescription']));
			$voucher['ExpiryDate'] = $voucher['ExpiryDate'] ? wt_crm_format_date($voucher['ExpiryDate'], 'display') : '';
			$image = $voucher['Image'] ? '<img src="data:image/gif;base64,' . esc_attr($voucher['Image']) . '">' : $logo;
			?>
			
			<article class="column small-12 medium-4 flex-container text-center wt-margin-bottom wt-voucher">
				<div class="card flex-child-auto flex-container flex-dir-column">
					<div class="wt-flex-none wt-overflow-hidden" data-equalizer-watch>
						<?php echo $image; ?>
					</div>
					
					<div class="card-section flex-child-auto flex-container flex-dir-column">
						<div class="flex-child-auto">
							<h1 class="wt-h6 wt-margin-bottom">
								<?php echo $voucher['VoucherName']; ?>
								<small><?php echo $voucher['VoucherCode']; ?></small>
							</h1>
							
							<?php echo sanitize_text_field($voucher['LongDescription']); ?>
							
							<?php if ($voucher['ExpiryDate']) { ?>
								<p>Expiry: <?php echo $voucher['ExpiryDate']; ?></p>
							<?php } ?>
						</div>
						
						<p><?php echo $voucher['CostValue']; ?> <?php echo $voucher['CostCurrency']; ?></p>
						
						<?php if ($account['Balance'] >= $voucher['CostValue']) { ?>
							<a data-open="voucher-<?php echo $voucher['VoucherCode']; ?>" class="button expanded small wt-margin-bottom-0">Redeem</a>
						<?php } else { ?>
							<button type="button" class="button expanded small" disabled>Insufficient Points</button>
						<?php } ?>
					</div>
				</div>
			</article>
			
			<?php if ($account['Balance'] >= $voucher['CostValue']) { ?>
				<article class="reveal" id="voucher-<?php echo $voucher['VoucherCode']; ?>" data-reveal>
					<div class="text-center wt-container-small">
						<div class="wt-margin-bottom">
							<?php echo $image; ?>
						</div>
						
						<h1 class="wt-h6 wt-margin-bottom">
							<?php echo $voucher['VoucherName']; ?>
							<small><?php echo $voucher['VoucherCode']; ?></small>
						</h1>
						
						<?php echo wpautop(sanitize_text_field($voucher['LongDescription'])); ?>
						
						<?php if ($voucher['ExpiryDate']) { ?>
							<p>Expiry: <?php echo $voucher['ExpiryDate']; ?></p>
						<?php } ?>
						
						<p><?php echo $voucher['CostValue']; ?> <?php echo $voucher['CostCurrency']; ?></p>
						
						<form data-route="wt/v1/crm/members/<?php echo $account['MemberNumber']; ?>/vouchers/redeem" method="post" data-confirm="Are you sure you want to redeem this voucher?">
							<input type="hidden" name="VoucherDetails[0][VoucherCode]" value="<?php echo $voucher['VoucherCode']; ?>">
							
							<label class="wt-margin-bottom">
								Quantity:
								<input type="number" name="qty" value="1" min="1" max="<?php echo $voucher['CostValue'] && $account['Balance'] ? floor($account['Balance']/$voucher['CostValue']) : ''; ?>" class="text-center" required>
							</label>
							
							<button type="submit" class="button expanded wt-margin-bottom-0" data-submit="Redeem" data-submitting="Redeeming..." data-submitted="Redeem" data-enable="true">Redeem</button>
							
							<div class="callout small hide wt-margin-top wt-margin-bottom-0" data-success="Redeemed successfully. Please <a href='<?php echo $url; ?>'>check under Vouchers</a>."></div>
						</form>
					</div>
					
					<button class="close-button" data-close aria-label="Close modal" type="button">
						<span aria-hidden="true" class="wt-sans-serif">&times;</span>
					</button>
				</article>
			<?php } ?>
			
			<?php
		}
		?>
	
	</section>
	
	<?php
}
else
{
	?>
	
	There are no vouchers available.
	
	<?php
}

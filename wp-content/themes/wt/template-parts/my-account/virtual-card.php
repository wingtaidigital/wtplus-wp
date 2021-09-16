<?php defined('ABSPATH') || exit; ?>

<?php global $card; //wt_dump($card) ?>

<div class="wt-gutter-vertical-x2 wt-gutter-half wt-account wt-container-small text-center">
	<form method="get" data-route="wt/v1/crm/customers/qr-code">
		<div class="callout hide"></div>
		
		<img src="<?php echo wt_crm_qr_code($_SESSION['wt_crm']['MemberID']); //$card['CVCInfo']?>" class="wt-margin-bottom wt-qr-code">
		
		<!--<button type="submit" class="wt-text-secondary" data-submit='<i class="icon icon-repeat" aria-hidden="true"></i> Refresh QR Code' data-submitting="Refreshing" data-submitted="Refreshed">
			<i class="icon icon-repeat" aria-hidden="true"></i> Refresh QR Code
		</button>-->
	</form>
</div>

<?php
defined('ABSPATH') || exit;



$member = wt_crm_api([
	"Command"  => "MEMBER ENQUIRY",
	'MemberID' => $_SESSION['wt_crm']['MemberID'],
]);

if (!is_wp_error($member))
{
	if (!empty($member['CardLists']) && is_array($member['CardLists']))
	{
		function wt_get_transaction_type($type)
		{
			switch ($type)
			{
				case 'MINUS ADJUSTMENT':
				case 'PLUS ADJUSTMENT':
					return 'ADJUSTMENT';
					
				case 'VOUCHER ISSUANCE WITH POINTS':
					return 'POINTS REDEMPTION';
					
				case 'ADDITIONAL REWARDS':
					return 'REWARDS';
			}
			
			return $type;
		}
		
		function wt_transaction_row($transaction)
		{
			if (!in_array($transaction['TransactType'], ['SALES', 'POINTS REDEMPTION', 'VOUCHER ISSUANCE WITH POINTS', 'MINUS ADJUSTMENT', 'PLUS ADJUSTMENT', 'ADDITIONAL REWARDS']) || wt_crm_format_date($transaction['TransactDate'], 'input') < '2018-10-01')
				return false;
			?>
			
			<tr>
				<td nowrap><?php echo wt_crm_format_date($transaction['TransactDate'], 'display'); ?></td>
				<td><?php echo sanitize_text_field(wt_get_transaction_type($transaction['TransactType'])); ?></td>
				<td><?php echo sanitize_text_field($transaction['TransactOutletName']); ?></td>
				<td nowrap class="text-right">
					<?php if (empty($transaction['SpendingAmt'])) { ?>
						-
					<?php } else { ?>
						$<?php echo number_format_i18n($transaction['SpendingAmt'], 2); ?>
					<?php } ?>
				</td>
				<td class="text-right"><?php echo (int) $transaction['Points']; ?></td>
			</tr>
			
			<?php
			return true;
		}
		?>
		
		<section class="table-scroll">
			<table class="wt-table">
				<thead>
					<tr class="wt-upper">
						<th>Date</th>
						<th>Type</th>
						<th>Location</th>
						<th>Amount</th>
						<th>Point</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$have_transactions = false;
					
					foreach ($member['CardLists'] as $card)
					{
						$transactions = wt_crm_api([
							'CardNo'                    => $card['CardNo'],
							"Command"                   => "GET TRANSACTION HISTORY",
							'FilterBy_TransactDateFrom' => '2018-10-01'
						]);
						
						if (!is_wp_error($transactions) && !empty($transactions['TransactionLists']) && is_array($transactions['TransactionLists']))
						{
							foreach ($transactions['TransactionLists'] as $transaction)
							{
								$success = wt_transaction_row($transaction);
								
								if (!empty($transaction['SalesRelatedTransactionList']) && is_array($transaction['SalesRelatedTransactionList']))
								{
									foreach ($transaction['SalesRelatedTransactionList'] as $sub_transaction)
										wt_transaction_row($sub_transaction);
								}
								
								if (!$have_transactions && $success)
									$have_transactions = true;
							}
						}
					}
					?>
				</tbody>
			</table>
		</section>
		
		<?php if (!$have_transactions) { ?>
			No transactions
		<?php } ?>
		
		<?php
	}
	else
	{
		?>
		
		No transactions
		
		<?php
	}
}
else
{
	?>

	Error occurred. Please try refreshing the browser.
	
	<?php
}
?>

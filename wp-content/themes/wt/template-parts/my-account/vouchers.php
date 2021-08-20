<?php defined('ABSPATH') || exit; ?>

<section>
    <ul id="vouchers" class="tabs wt-upper" data-tabs data-deep-link="true" data-deep-link-smudge="true">
        <li class="tabs-title is-active"><a href="#redemption">Rewards Boutique</a></li>
        <li class="tabs-title"><a href="#my-vouchers">My Rewards</a></li>
        <li class="tabs-title"><a href="#gift-history">Gift History</a></li>
    </ul>

    <div class="tabs-content" data-tabs-content="vouchers">
        <?php
        global $card;

        $card['TotalPointsBAL'] = (int)$card['TotalPointsBAL'];
        $logo                   = '<div class="wt-logo-container">' . get_custom_logo() . '</div>';
        $url                    = home_url('my-account/vouchers/#my-vouchers');

        $transaction_lists = wt_has_transactions_today(3);
        $has_gifted        = wt_has_transactions_today(20);

        $voucher_types = [];
        //		$voucher_type_codes = [];
        $response = wt_crm_api([
            "Command" => "GET ITEM LISTS",
            'FilterBy_CatalogCode' => 'RewardsBoutique',
            'RetrieveRedeemableOnly' => true
        ]);

        if (!is_wp_error($response) && !empty($response['ItemLists']) && is_array($response['ItemLists'])) {
            $voucher_types = array_column($response['ItemLists'], null, 'SKU');

            $has_purchased = wt_has_normal_transactions($voucher_types, $transaction_lists);
        } else {
            $has_purchased = false;
        }

        $active_voucher_types = $voucher_types;

        $vouchers = [];
        $response = wt_crm_api([
            "Command" => "GET VOUCHERS",
            'CardNo' => $_SESSION['wt_crm']['CardNo'],
            'RetrieveActiveVouchers' => true,
            "RetrieveMembershipInfo" => false,
        ]);

        if (!is_wp_error($response) && !empty($response['ActiveVoucherLists']) && is_array($response['ActiveVoucherLists'])) {
            $vouchers = $response['ActiveVoucherLists'];
        }

        $response = wt_crm_api([
            "Command" => "GET VOUCHER TYPE",
            "RetrieveTnC" => true,
            // "FilterBy_IsRedeemable" => true,
            // 'FilterBy_VoucherTypeCode' => implode(',', $voucher_type_codes),
        ]);

        $responseFilter = wt_crm_api([
            "Command" => "GET VOUCHER TYPE",
            "RetrieveTnC" => true,
            "FilterBy_IsRedeemable" => true,
            // 'FilterBy_VoucherTypeCode' => implode(',', $voucher_type_codes),
        ]);

        $merge = array_merge($response['VoucherTypeLists'], $responseFilter['VoucherTypeLists']);
        wt_log("VOUCHER DATA: " . sizeof($response['VoucherTypeLists']), 'crm');
        wt_log("VOUCHER DATA FILTER: " . sizeof($responseFilter['VoucherTypeLists']), 'crm');
        wt_log("VOUCHER MERGE: " . sizeof($merge), 'crm');

        if (!is_wp_error($response) && !is_wp_error($responseFilter) && !empty($merge) && is_array($merge)) {
            foreach ($merge as $voucher) {
                if (empty($voucher_types[$voucher['Code']])) {
                    $voucher_types[$voucher['Code']]                = $voucher;
                    $voucher_types[$voucher['Code']]['ItemDetails'] = $voucher['TnC'];
                    continue;
                }

                if (!empty($voucher['ImageLink']))
                    $voucher_types[$voucher['Code']]['ImageLink'] = $voucher['ImageLink'];

                if (!empty($voucher['TnC'])) {
                    $voucher_types[$voucher['Code']]['ItemDetails'] = $voucher['TnC'];
                }
            }
        }
        ?>

        <div class="tabs-panel is-active" id="redemption">
            <p>Points available for redemption: <span
                        class="wt-text-secondary"><?php echo $card['TotalPointsBAL']; ?></span></p>

            <?php
            $has_vouchers = false;

            if ($active_voucher_types): ?>

                <div class="row" data-equalizer>
                    <?php
                    foreach ($active_voucher_types as $voucher_type_code => $voucher) {
                        if (is_null($voucher['RedemptionPoints']))
                            continue;

                        $has_vouchers = true;
                        $voucher      = array_merge($voucher, $voucher_types[$voucher_type_code]);
                        $id           = "voucher-type-" . sanitize_title($voucher_type_code);
                        $is_flashDeal = (isset($voucher['Level_Code']) && ($voucher['Level_Code'] == 'FlashDeal'));
                        ?>

                        <article class="column small-12 medium-4 -large-6 flex-container text-center wt-margin-bottom">
                            <div class="card flex-child-auto flex-container flex-dir-column">
                                <div class="wt-flex-none wt-overflow-hidden" data-equalizer-watch>
                                    <?php
                                    $image = empty($voucher['ImageLink']) ? $logo : '<img src="' . esc_url($voucher['ImageLink']) . '">';
                                    echo $image;
                                    ?>
                                </div>

                                <div class="card-section flex-child-auto flex-container flex-dir-column">
                                    <div class="flex-child-auto">
                                        <h1 class="wt-h6 wt-margin-bottom">
                                            <?php
                                            $name = sanitize_text_field($voucher['Name']);
                                            echo $name;
                                            ?>
                                        </h1>
                                    </div>

                                    <p>
                                        Redeem&nbsp;using
                                        <strong>
                                            <?php
                                            $value = (int)$voucher['RedemptionPoints'];
                                            echo $value;
                                            ?>&nbsp;wt+&nbsp;points
                                        </strong>
                                    </p>

                                    <?php if ($card['TotalPointsBAL'] >= $value): ?>

                                        <?php if ($is_flashDeal): ?>
                                            <?php if (wt_has_flash_deal_transactions($transaction_lists, $voucher['AlternateID'])): ?>
                                                <a data-open="hit-limit"
                                                   class="button expanded small secondary wt-upper disabled">Purchase</a>
                                            <?php else: ?>
                                                <a data-open="<?php echo $id; ?>"
                                                   class="button expanded small secondary wt-upper">Purchase</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($has_purchased): ?>
                                                <a data-open="hit-limit"
                                                   class="button expanded small secondary wt-upper disabled">Purchase</a>
                                            <?php else: ?>
                                                <a data-open="<?php echo $id; ?>"
                                                   class="button expanded small secondary wt-upper">Purchase</a>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if (($has_gifted) || ($is_flashDeal)): ?>
                                            <a data-open="hit-limit"
                                               class="button expanded small secondary wt-upper disabled">Gift to
                                                friend</a>
                                        <?php else: ?>
                                            <a data-open="<?php echo $id; ?>-gift"
                                               class="button expanded small secondary wt-upper">Gift to friend</a>
                                        <?php endif; ?>

                                    <?php else: ?>

                                        <button type="button" class="button expanded small secondary wt-upper" disabled>
                                            Insufficient Points
                                        </button>

                                    <?php endif; ?>

                                    <?php if (!empty($voucher['ItemDetails'])): ?>
                                        <button data-open="<?php echo $id; ?>-tnc" class="wt-text-secondary"><small>Click
                                                here for <u>T&C</u></small></button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>

                        <?php if ($card['TotalPointsBAL'] >= $value) { ?>
                            <?php if ((!$has_purchased) || ($is_flashDeal)): ?>
                                <article class="reveal" id="<?php echo $id; ?>" data-reveal>
                                    <div class="text-center wt-container-small">
                                        <div class="wt-margin-bottom">
                                            <?php echo $image; ?>
                                        </div>

                                        <h1 class="wt-h6 wt-margin-bottom">
                                            <?php echo $name; ?>
                                        </h1>

                                        <p>
                                            Redeem&nbsp;using
                                            <strong>
                                                <?php
                                                $value = (int)$voucher['RedemptionPoints'];
                                                echo $value;
                                                ?>&nbsp;wt+&nbsp;points
                                            </strong>
                                        </p>

                                        <form data-route="wt/v1/crm/vouchers/<?php echo $voucher['SKU']; ?>"
                                              method="post"
                                              data-confirm="Are you sure you want to redeem this voucher?">
                                            <?php /* <label class="wt-margin-bottom">
												Quantity:
												<input type="number" name="VoucherQty" value="1" min="1" max="<?php echo $value && $card['TotalPointsBAL'] ? floor($card['TotalPointsBAL'] / $value) : ''; ?>" class="text-center" required>
											</label> */ ?>
                                            <input type="hidden" name="EmailTemplate" value="<?php echo $voucher['RefInfo']['Ref1']; ?>">
                                            <button type="submit"
                                                    class="button expanded secondary wt-upper wt-margin-bottom-0"
                                                    data-submit="Purchase" data-submitting="Purchasing..."
                                                    data-submitted="Purchased" -data-enable="true">Purchase
                                            </button>

                                            <div class="callout small hide wt-margin-top wt-margin-bottom-0"
                                                 data-success="Redeemed successfully. Please <a href='<?php echo $url; ?>' data-reload>check under My Rewards</a>."></div>
                                        </form>
                                    </div>

                                    <button class="close-button" data-close aria-label="Close modal" type="button">
                                        <span aria-hidden="true" class="wt-sans-serif">&times;</span>
                                    </button>
                                </article>
                            <?php endif; ?>

                            <?php if (!$has_gifted) { ?>
                                <article class="reveal" id="<?php echo $id; ?>-gift" data-reveal>
                                    <h1 class="wt-h6 wt-upper wt-margin-bottom text-center">Share the joy of shopping
                                        with a friend!</h1>

                                    <div class="text-center wt-container-small">
                                        <div class="wt-margin-bottom">
                                            <?php echo $image; ?>
                                        </div>

                                        <h2 class="wt-h6 wt-margin-bottom">
                                            <?php echo $name; ?>
                                        </h2>

                                        <p>
                                            Redeem&nbsp;using
                                            <strong>
                                                <?php
                                                $value = (int)$voucher['RedemptionPoints'];
                                                echo $value;
                                                ?>&nbsp;wt+&nbsp;points
                                            </strong>
                                        </p>

                                        <form data-route="wt/v1/crm/vouchers/<?php echo $voucher['SKU']; ?>/send"
                                              method="post" data-confirm="Are you sure you want to gift this voucher?">
                                            <label class="wt-margin-bottom text-left">
                                                Friend’s Name
                                                <input type="text" name="RecipientName" placeholder="Full Name"
                                                       required>
                                            </label>

                                            <label class="wt-margin-bottom text-left">
                                                Friend’s Email
                                                <input type="email" name="Recipient"
                                                       placeholder="An email will be sent to this email address"
                                                       required>
                                            </label>

                                            <button type="submit"
                                                    class="button expanded secondary wt-upper wt-margin-bottom-0"
                                                    data-submit="Submit" data-submitting="Submitting..."
                                                    data-submitted="Submitted" -data-enable="true">Submit
                                            </button>

                                            <div class="callout small hide wt-margin-top wt-margin-bottom-0"
                                                 data-success="Your gift has been sent to your friend. <?php echo $voucher['RedemptionPoints']; ?> points have been deducted from your account. An email has been sent to him/her with instructions to redeem the voucher."></div>
                                        </form>
                                    </div>

                                    <button class="close-button" data-close aria-label="Close modal" type="button">
                                        <span aria-hidden="true" class="wt-sans-serif">&times;</span>
                                    </button>
                                </article>
                            <?php } ?>
                        <?php } ?>

                        <?php if (!empty($voucher['ItemDetails'])) { ?>
                            <article class="reveal" id="<?php echo $id; ?>-tnc" data-reveal>
                                <div class="wt-gutter wt-gutter-vertical">
                                    <h1 class="wt-h6 wt-margin-bottom">
                                        <?php echo $name; ?> T&C
                                    </h1>

                                    <?php echo wpautop(wp_kses_post($voucher['ItemDetails'])); ?>
                                </div>

                                <button class="close-button" data-close aria-label="Close modal" type="button">
                                    <span aria-hidden="true" class="wt-sans-serif">&times;</span>
                                </button>
                            </article>
                        <?php } ?>

                        <?php
                    }
                    ?>

                </div>
            <?php endif; ?>
            <?php if (!$has_vouchers): ?>
                Stay tuned! Vouchers coming your way soon!
            <?php endif; ?>
        </div>

        <div class="tabs-panel" id="my-vouchers">
            <form data-route="wt/v1/crm/voucher/receive" method="post">
                <label class="wt-margin-bottom-0">Have a Voucher Code? Key in here!</label>

                <div class="input-group">
                    <input type="text" name="VoucherNo" required>
                    <div class="input-group-button">
                        <button type="submit" class="button secondary wt-upper">Submit</button>
                    </div>
                </div>

                <div class="callout small hide wt-margin-top wt-margin-bottom-0"
                     data-success="Voucher redeemed successfully. Refresh the page to see it."></div>
                <?php /*<div class="callout small hide wt-margin-top wt-margin-bottom-0" data-success="Voucher redeemed successfully. <a href='<?php echo $url; ?>' data-reload>Refresh the page to see it.</a>"></div>*/ ?>
            </form>

            <hr>

            <?php if ($vouchers): ?>
                <div class="row" data-equalizer>
                    <?php
                    foreach ($vouchers as $voucher):
                        if (!empty($voucher['TenderMode']) && $voucher['TenderMode'] === 'Hidden')
                            continue;

                        if (!empty($voucher_types[$voucher['VoucherTypeCode']]))
                            $voucher = array_merge($voucher, $voucher_types[$voucher['VoucherTypeCode']]);
                        ?>

                        <article class="column small-12 medium-4 -large-6 flex-container text-center wt-margin-bottom">
                            <div class="card flex-child-auto flex-container flex-dir-column">
                                <div class="wt-flex-none wt-overflow-hidden" data-equalizer-watch>
                                    <?php
                                    $image = empty($voucher['ImageLink']) ? $logo : '<img src="' . esc_url($voucher['ImageLink']) . '">';
                                    echo $image;
                                    ?>
                                </div>

                                <div class="card-section flex-child-auto flex-container flex-dir-column">
                                    <div class="flex-child-auto">
                                        <h1 class="wt-h6 wt-margin-bottom">
                                            <?php
                                            $name = sanitize_text_field($voucher['VoucherTypeName']);
                                            echo $name;
                                            ?>
                                        </h1>
                                    </div>

                                    <p>
                                        Valid from
                                        <?php echo wt_crm_format_date($voucher['ValidFrom'], 'display'); ?>
                                        to
                                        <?php echo wt_crm_format_date($voucher['ValidTo'], 'display'); ?>
                                    </p>

                                    <?php
                                    if (!empty($voucher['ItemDetails'])):
                                        $tnc_id = "voucher-type-" . sanitize_title($voucher['VoucherTypeCode']) . '-tnc';
                                        ?>

                                        <div class="text-center">
                                            <button data-open="<?php echo $tnc_id; ?>" class="wt-text-secondary"><small>Click
                                                    here for <u>T&C</u></small></button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>

                        <?php if (!empty($voucher['ItemDetails']) && (!array_key_exists($voucher['VoucherTypeCode'], $active_voucher_types) || is_null($voucher['RedemptionPoints']))): ?>
                        <article class="reveal" id="<?php echo $tnc_id; ?>" data-reveal>
                            <div class="wt-gutter wt-gutter-vertical">
                                <h1 class="wt-h6 wt-margin-bottom">
                                    <?php echo $name; ?> T&C
                                </h1>

                                <?php echo wpautop(wp_kses_post($voucher['ItemDetails'])); ?>
                            </div>

                            <button class="close-button" data-close aria-label="Close modal" type="button">
                                <span aria-hidden="true" class="wt-sans-serif">&times;</span>
                            </button>
                        </article>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                You currently do not have any wt+ vouchers.
                <!--				<a href="#redemption">Redeem vouchers here</a>-->
            <?php endif; ?>
        </div>

        <div class="tabs-panel" id="gift-history">
            <?php
            global $member;

            $transactions = [];
            $gifts        = [];

            if ($member && !is_wp_error($member)):
                if (!empty($member['CardLists']) && is_array($member['CardLists'])):
                    $voucher_nos = [];

                    foreach ($member['CardLists'] as $card) {
                        $response = wt_crm_api([
                            'CardNo' => $card['CardNo'],
                            "Command" => "GET TRANSACTION HISTORY",
                            'FilterBy_Mode' => 20
                        ]);

                        if (!is_wp_error($response) && !empty($response['TransactionLists']) && is_array($response['TransactionLists'])) {
                            foreach ($response['TransactionLists'] as $transaction) {
                                if (empty($transaction['Ref3'])) {
                                    continue;
                                }

                                $transactions[] = $transaction;
                                $voucher_nos[]  = $transaction['Ref3'];
                            }
                        }
                    }

                    if ($voucher_nos) {
                        $response = wt_crm_api([
                            "Command" => "GET VOUCHERS",
                            "CardNo" => '9999999',
                            'FilterBy_VoucherNo' => implode(',', $voucher_nos),
                            'RetrieveMembershipInfo' => false,
                            'RetrieveActiveVouchers' => true,
                            'RetrieveRedeemedVouchers' => true,
                            'RetrieveExpiredVouchers' => true,
                            //														'RetrieveVoidedVouchers' => true,
                            //														'RetrieveDelayVouchers"' => true,
                        ]);

                        if (!is_wp_error($response)) {
                            $statuses = [
                                'Active' => 'Pending',
                                'Redeemed' => 'Accepted',
                                'Expired' => 'Rejected - 14 days validity',
                            ];

                            foreach ($statuses as $key => $label) {
                                $list = $key . 'VoucherLists';

                                if (!empty($response[$list]) && is_array($response[$list])) {
                                    foreach ($response[$list] as $voucher) {
                                        $gifts[$voucher['VoucherNo']] = $label;
                                    }
                                }
                            }
                        }
                    }
                    ?>

                    <section class="table-scroll">
                        <table class="wt-table">
                            <thead>
                            <tr class="wt-upper">
                                <th>Date</th>
                                <th>Voucher Code</th>
                                <th>Friend</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td nowrap><?php echo wt_crm_format_date($transaction['TransactDate'], 'display'); ?></td>
                                    <td><?php echo sanitize_text_field($transaction['Ref4']); ?></td>
                                    <td><?php echo sanitize_text_field($transaction['Ref1']); ?></td>
                                    <td><?php echo empty($gifts[$transaction['Ref3']]) ? '' : $gifts[$transaction['Ref3']]; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </section>

                    <?php if (!$transactions): ?>
                    No transactions
                <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($has_purchased || $has_gifted): ?>
    <section class="reveal" id="hit-limit" data-reveal>
        <div class="text-center wt-container-small">
            You have reached your daily redemption limit, each member is limited to 1 redemption per day
        </div>

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true" class="wt-sans-serif">&times;</span>
        </button>
    </section>
<?php endif; ?>

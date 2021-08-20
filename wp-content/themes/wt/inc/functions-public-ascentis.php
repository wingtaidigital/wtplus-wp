<?php
defined('ABSPATH') || exit;


function wt_brand_fieldset($brands = '')
{
    $codes = wt_crm_get_system_codes('BrandPreference');

    if (!$codes)
        return;

    $brands = explode(',', $brands);
    ?>

    <fieldset>
        <legend>Brand Preferences*</legend>

        <div class="row small-up-1 medium-up-3 -xlarge-up-4">
            <?php foreach ($codes as $code => $name) { ?>
                <label class="column">
                    <input type="checkbox" name="DynamicFieldLists[BrandPreference][]"
                           value="<?php esc_attr_e($code) ?>" <?php echo in_array($code, $brands) ? 'checked' : ''; ?>
                           data-grouprequired><?php echo sanitize_text_field($name); ?>
                </label>
            <?php } ?>
        </div>
    </fieldset>

    <?php
}


function wt_child_row($index = '{{i}}', $params = [], $show = true)
{
	// if (!$params)
	// {
    $format = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    $fields = wt_crm_get_child_fields();

    foreach ($fields as $field) {
        /*$field_name = "Child{$field}";

        if (is_numeric($index))
            $field_name .= ucfirst($format->format($index + 1));*/

        if (empty($params[$field])) {
            $params[$field] = '';
        } else {
            $params[$field] = sanitize_text_field($params[$field]);

            if ($field === 'DOB')
                $params[$field] = wt_format_date($params['DOB'], 'input');
        }
    }
	// }

    $disabled = $show ? '' : 'disabled';
    ?>

    <tr>
        <td><input type="text" name="children[<?php echo $index; ?>][First]" value="<?php echo $params['First']; ?>"
                   title="First Name" required <?php echo $disabled; ?> maxlength="50"></td>
        <td><input type="text" name="children[<?php echo $index; ?>][Last]" value="<?php echo $params['Last']; ?>"
                   title="Last Name" required <?php echo $disabled; ?> maxlength="50"></td>
        <td><input type="date" name="children[<?php echo $index; ?>][DOB]" value="<?php echo $params['DOB']; ?>"
                   title="Date of Birth" min="<?php echo wt_format_date(strtotime('-14 years'), 'input'); ?>"
                   max="<?php echo current_time('Y-m-d'); ?>" required <?php echo $disabled; ?>></td>
        <td>
            <button type="button" title="Remove" class="wt-remove" data-clone-button="add-child">&times;</button>
        </td>
    </tr>

    <?php
}

function wt_crm_get_children($list)
{
    $list         = array_combine(array_column($list, 'Name'), array_column($list, 'ColValue'));
    $children     = [];
    $child_fields = wt_crm_get_child_fields();
    $f            = new NumberFormatter("en", NumberFormatter::SPELLOUT);

    for ($i = 0; $i < 6; $i++) {
        $suffix = ucfirst($f->format($i + 1));

        foreach ($child_fields as $field) {
            if (!empty($list["Child{$field}{$suffix}"]) && $list["Child{$field}{$suffix}"] !== '1/1/1900 12:00:00 AM')
                $children[$i][$field] = sanitize_text_field($list["Child{$field}{$suffix}"]);
        }
    }
	// wt_dump($children);
    return $children;
}


function wt_crm_format_date($date = null, $context = 'crm')
{
    /*$timezone = get_option('timezone_string');

    if ($timezone)
        date_default_timezone_set(get_option('timezone_string'));*/

    $timestamp      = (int)substr($date, 6, -5);
    $timestamp      = strtotime('+8 hours', $timestamp);
    $formatted_date = wt_format_date($timestamp, $context);

    //	date_default_timezone_set( 'UTC' );

    return $formatted_date;
}


function wt_friend_row($index = '{{i}}')
{
    ?>

    <tr>
        <td valign="top" nowrap>
            <label>
                Friend's Name
                <input type="text" name="NameList[<?php echo $index; ?>][Name]" autocomplete="off" required>
                <span class="form-error"></span>
            </label>
        </td>
        <td valign="top" nowrap>
            <label>
                Friend's Email
                <input type="email" name="NameList[<?php echo $index; ?>][Email]" autocomplete="off" required
                       class="wt-exists" data-route="wt/v1/crm/exists?field=email&value=">
                <span class="form-error">
					<span class="customError">This email address has already been registered</span>
					<span class="typeMismatch">Please enter a valid email</span>
					<span class="valueMissing">Please enter email</span>
				</span>
            </label>
        </td>
        <td>
            <?php if ($index) { ?>
                <button type="button" title="Remove" class="wt-remove" data-clone-button="add-child">&times;</button>
            <?php } ?>
        </td>
    </tr>

    <?php
}


function wt_crm_get_list_field_value($list, $field_name)
{
    foreach ($list as $field) {
        if (isset($field['Name'], $field['ColValue']) && $field['Name'] === $field_name)
            return $field['ColValue'];
    }

    return '';
}


function wt_crm_get_system_codes($parent_code)
{
    $transient = 'wt_system_codes_' . $parent_code;
    $codes     = get_transient($transient);
//	$codes = [];

    if (!$codes) {
        $response = wt_crm_api([
            "ParentCode" => $parent_code,
            "Command" => "GET SYSTEM CODES",
			// "EnquiryCode" => "POS",
			// "OutletCode"  => "HQ",
			// "PosID"       => "POS0001",
			// "CashierID"   => "Cashier0001"
        ]);

        if (!is_wp_error($response) && isset($response['SystemCodes']) && is_array($response['SystemCodes'])) {
			// wt_log($response['SystemCodes'], 'crm');
            $codes = [];

            foreach ($response['SystemCodes'] as $code) {
                $codes[$code['SystemCode']] = $code['Name'];
            }

            set_transient($transient, $codes);
        }
    }

    if ($codes)
        return $codes;
}

function wt_crm_get_system_code_name($parent_code, $code)
{
    $codes = wt_crm_get_system_codes($parent_code);

    if (isset($codes[$code]))
        return sanitize_text_field($codes[$code]);
}


function wt_crm_get_badges()
{
    $transient = 'wt_badges';
    $badges    = get_transient($transient);

    if (!$badges) {
        $response = wt_crm_api([
            "ParentCode" => 'Badges',
            "Command" => "GET SYSTEM CODES",
        ]);

        if (!is_wp_error($response) && isset($response['SystemCodes']) && is_array($response['SystemCodes'])) {
            $codes  = $response['SystemCodes'];
            $badges = [];

            foreach ($codes as $badge) {
                $names = explode(' | ', $badge['Name']);

                if (count($names) < 3)
                    continue;

                $badge['Name']                      = $names[1];
                $badges[trim($names[0])][$names[2]] = $badge;
            }

            foreach ($badges as $category => $category_badges) {
                ksort($badges[$category]);
            }

            set_transient($transient, $badges, HOUR_IN_SECONDS);
        }
    }

    if ($badges)
        return $badges;
}


function wt_crm_get_messages()
{
    $transient = 'wt_messages';
    $messages  = get_transient($transient);

    if (!$messages) {
        $response = wt_crm_api([
            "ParentCode" => 'POSSMS',
            "Command" => "GET SYSTEM CODES",
        ]);

        if (!is_wp_error($response) && isset($response['SystemCodes']) && is_array($response['SystemCodes'])) {
            $codes    = $response['SystemCodes'];
            $messages = [];

            foreach ($codes as $code) {
                $messages[$code['Name']] = $code['Description'];
            }

            set_transient($transient, $messages, DAY_IN_SECONDS);
        }
    }

    if ($messages)
        return $messages;
}


function wt_get_genders()
{
    return [
        'M' => 'Male',
        'F' => 'Female',
    ];
}


function wt_get_home_address($profile)
{
    if (empty($profile) || empty($profile['Country']))
        return;

    if ($profile['Country'] === 'CCSG')
        $fields = ['Block', 'Level', 'Unit', 'Street', 'Building'];
    else
        $fields = ['Address1', 'Address2', 'Address3'];

	// $fields[] = 'PostalCode';
    $address = [];

    foreach ($fields as $field) {
        if (!empty($profile[$field]))
            $address[] = sanitize_text_field($profile[$field]);
    }

    $address[] = wt_crm_get_system_code_name('Country', $profile['Country']);

    if (!empty($profile['PostalCode']))
        $address[] = sanitize_text_field($profile['PostalCode']);

    return implode(' ', $address);
}


function wt_get_membership_color($type)
{
	// $colors = wt_get_membership_colors();
    $colors = [
        'Silver' => '#A5A09A',
        'Gold' => '#A4947C',
        'Premium' => '#413F3B',
    ];

    if (isset($colors[$type]))
        return $colors[$type];
}


function wt_get_voucher_status_color($status)
{
    if ($status == 'USED')
        return 'warning';

    if ($status == 'EXPIRED' || $status == 'CANCELLED')
        return 'alert';

    return 'success';
}


function wt_get_voucher_remarks($str)
{
    $a1 = explode('|', $str);

    if (!$a1)
        return;

    $remarks = [];

    foreach ($a1 as $s) {
        $a2 = explode(':', $s);

        if (!$a2 || count($a2) < 2) {
            continue;
        }

        $remarks[$a2[0]] = $a2[1];
    }

    return $remarks;
}


function wt_has_transactions_today($mode = 20)
{
    if ($mode == 3) {
        // $response = file_get_contents(__DIR__ . '/get_transaction_history_20.json');
        // $response = json_decode($response, true);
        $response = wt_crm_api([
            "Command" => "GET TRANSACTION HISTORY",
            'CardNo' => $_SESSION['wt_crm']['CardNo'],
            'FilterBy_Mode' => $mode,
            'FilterBy_TransactDateFrom' => current_time('Y-m-d'),
        ]);

        if (is_wp_error($response) || empty($response['TotalTransactionCounts']))
            return false;

        if (!isset($response['TransactionLists'])) {
            return [];
        } else {
            $response = $response['TransactionLists'];

            return $response;
        }
    } else {
        $response = wt_crm_api([
            "Command" => "GET TRANSACTION HISTORY",
            'CardNo' => $_SESSION['wt_crm']['CardNo'],
            'FilterBy_Mode' => $mode,
            'FilterBy_TransactDateFrom' => current_time('Y-m-d'),
        ]);

        if (is_wp_error($response) || empty($response['TotalTransactionCounts']))
            return false;

        return true;
    }
}

/**
 * check if current sku is flash deal and it's redeemed
 *
 * @param $transaction_lists array
 * @param $ref7 string
 * @return bool
 */
function wt_has_flash_deal_transactions($transaction_lists, $ref7)
{
    foreach ($transaction_lists as $item) {
        if ($item['Ref7'] == $ref7) {
            return true;
            break;
        }
    }
    return false;
}

/**
 * check if inside transaction have normal voucher
 *
 * @param $item_lists
 * @param $transaction_list
 * @return boolean
 */
function wt_has_normal_transactions($item_lists, $transaction_lists)
{
    foreach ($transaction_lists as $trn) {
        foreach ($item_lists as $item) {
            if ($item['Level_Code'] != 'FlashDeal') {
                if ($trn['Ref7'] == $item['AlternateID']) {
                    return true;
                    break;
                }
            }
        }
    }

    return false;
}

function wt_interest_fieldset($interest)
{
    ?>

    <fieldset>
        <legend><?php echo $interest['label']; ?></legend>

        <div class="row small-up-1 medium-up-3 -xlarge-up-4">
            <?php foreach ($interest['items'] as $item) { ?>
                <label class="column">
                    <input type="checkbox" name="interests[<?php echo esc_attr($item['Code']); ?>]"
                           value="Y" <?php echo $item['Value'] == 'Y' ? 'checked' : ''; ?>><?php echo sanitize_text_field($item['DisplayText']); ?>
                </label>
            <?php } ?>
        </div>
    </fieldset>

    <?php
}


function wt_mobile_fieldset($member = [], $required = true)
{
    $names['CountryCode'] = 'DynamicColumnLists[CountryCode]';
    $names['Number']      = 'MobileNo';

    if (isset($member['DynamicColumnLists']))
        $values['CountryCode'] = (int)wt_crm_get_list_field_value($member['DynamicColumnLists'], 'CountryCode');

    if (empty($values['CountryCode']))
        $values['CountryCode'] = '65';

    $values['Number'] = empty($member['MobileNo']) ? '' : sanitize_text_field($member['MobileNo']);

    require_once get_template_directory() . '/vendor/autoload.php';

    $util    = libphonenumber\PhoneNumberUtil::getInstance();
    $regions = $util->getSupportedRegions();

    if ($values['Number'] && $values['CountryCode'] == '65') {
        try {
            $phoneNumber = $util->parse($values['Number'], 'SG', null, true);
            $phoneNumber = $phoneNumber->getNationalNumber();

            if ($phoneNumber)
                $values['Number'] = $phoneNumber;
        } catch (libphonenumber\NumberParseException $e) {
        }
    }

    if (!empty($values['Number'][0]) && $values['Number'][0] === '+') {
        $country_code_length = strlen($values['CountryCode']);

        if ($values['CountryCode'] == substr($values['Number'], 1, $country_code_length)) {
            $values['Number'] = substr($values['Number'], $country_code_length + 1);
        }
    }
    ?>

    <fieldset>
        <legend class="wt-margin-bottom-0">Mobile<?php echo $required ? '*' : ''; ?></legend>

        <div class="row collapse wt-mobile">
            <div class="column shrink">
                <select name="<?php echo $names['CountryCode']; ?>" title="Country Code"
                        class="wt-select" <?php echo $required ? 'required' : ''; ?>>
                    <?php
                    foreach ($regions as $calling_code => $iso_code) {
                        $calling_code = sanitize_text_field($calling_code);
                        ?>

                        <option value="<?php echo $calling_code; ?>" <?php selected($values['CountryCode'], $calling_code); ?>>
                            +<?php echo $calling_code; ?></option>

                        <?php
                    }
                    ?>
                </select>
            </div>
            <div class="column">
                <input type="tel" name="<?php echo $names['Number']; ?>" value="<?php echo $values['Number']; ?>"
                       title="Mobile Number"
                       placeholder="Please fill in your mobile number" -autocomplete="tel"
                    <?php echo $required ? 'required' : ''; ?> pattern="\d*" minlength="2" maxlength="17"
                >
            </div>
        </div>

        <span class="form-error">
			<span class="badInput">Please enter a valid number</span>
			<span class="customError">
				<?php if (wt_is_user_logged_in()) { ?>
                    This mobile number has already been registered by another member.
                <?php } else { ?>
                    This mobile number has already been registered. <a data-open="login-modal"
                                                                       aria-controls="login-modal" aria-haspopup="true"
                                                                       tabindex="0">Log in here.</a>
                <?php } ?>
			</span>
			<span class="patternMismatch">Please enter only numbers</span>
			<span class="tooLong">Mobile number is too long</span>
			<span class="tooShort">Mobile number is too short</span>
			<span class="valueMissing">Please enter mobile number</span>
		</span>
    </fieldset>

    <?php
}


function wt_notify_checkboxes($member = [])
{
    $contact_preferences = [];

	// if (!$member)
    $contact_preferences['NotifyEmail'] = 'Email';

    $contact_preferences = array_merge($contact_preferences, [
        'NotifySMS' => 'SMS',
        'NotifyPost' => 'Mail',
        'NotifyCall' => 'Call',
        'WhatsApp' => 'WhatsApp',
    ]);
    $mailing_lists       = wt_crm_get_mailing_lists();

    foreach ($contact_preferences as $name => $label) {
        if ($name === 'NotifyCall') {
            $prefix = 'DynamicFieldLists';
            $value = 'Yes';
        } elseif ($name === 'WhatsApp') {
            $prefix = 'DynamicFieldLists';
            $value = 'Yes';
        } else {
            $prefix = 'DynamicColumnLists';
            $value  = 'true';
        }

        $checked = '';

        if ($name === 'NotifyEmail') {
            if (!empty($member['MailingLists']) && is_array($member['MailingLists'])) {
                foreach ($member['MailingLists'] as $list) {
                    if (!empty($list['Name']) && in_array($list['Name'], $mailing_lists)) {
                        $checked = 'checked';
                        break;
                    }
                }
            }
        } elseif (!empty($member[$prefix])) {
            $v = wt_crm_get_list_field_value($member[$prefix], $name);

            switch ($name)
            {
                case 'NotifySMS':
                    if ($v && $v !== 'False')
                        $checked = 'checked';
                    break;
                case 'NotifyCall':
                    if ($v && $v !== '')
                        $checked = 'checked';
                    break;
                case 'WhatsApp':
                    if ($v && $v !== 'No')
                        $checked = 'checked';
                    break;
                default:
                    $checked = '';
            }
        }
        ?>

        <input name="<?php echo $prefix; ?>[<?php echo $name; ?>]" id="<?php echo $name; ?>" type="checkbox"
               value="<?php echo $value; ?>" <?php echo $checked; ?>><label
                for="<?php echo $name; ?>"><?php echo $label; ?></label>

        <?php
    }
}


function wt_crm_qr_code($str)
{
    require_once get_template_directory() . '/vendor/autoload.php';

    $qrCode = new Endroid\QrCode\QrCode($str);

    $qrCode->setEncoding('ISO 8859-1');

    return $qrCode->writeDataUri();
}

// function wt_get_voucher_image($member_type, $voucher_code)
// {
// 	$vouchers = get_transient('wt_' . $member_type . '_vouchers');
//
// 	if (!$vouchers)
// 		return;
//
// 	$vouchers = json_decode($vouchers, true);
//
// 	foreach ($vouchers as $voucher)
// 	{
// 		if ($voucher['VoucherCode'] == $voucher_code)
// 		{
// 			if (!empty($voucher['Image']))
// 				return '<img src="data:image/gif;base64, ' . esc_attr($voucher['Image']) . '">';
//
// 			break;
// 		}
// 	}
// }
//
// function wt_get_membership_colors()
// {
// 	return [
// 		'Silver' => '#A5A09A',
// 		'Gold' => '#A4947C',
// 		'Platinum' => '#413F3B',
// 	];
// }

/*
 * function wt_gender_field($selected = '')
{
	$genders = wt_get_genders()
	?>
	
	<label>
		Gender*
		<select name="GenderCode" class="wt-select">
			<option value="">Please select your gender</option>
			
			<?php foreach ($genders as $value => $label) { ?>
				<option value="<?php echo $value; ?>" <?php selected($selected, $value); ?>><?php echo $label; ?></option>
			<?php } ?>
		</select>
	</label>
	
	<?php
}*/

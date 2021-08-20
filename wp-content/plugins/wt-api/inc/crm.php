<?php
defined('ABSPATH') || exit;

register_rest_route('wt/v1', '/crm/exists', array(
    'methods' => 'POST',
    'callback' => function ($request) {
        $params = $request->get_params();

        $customer = wt_crm_search($params['nric_or_email'], empty($params['exclude']) ? null : $params['exclude']);

        if ($customer) {
            // wt_log($customer, 'crm');
            return true;
        }

        return false;
    },
    'args' => array(
        'nric_or_email' => array(
            'required' => true,
            'validate_callback' => function ($param, $request, $key) {
                return !empty($param);
            },
            'sanitize_callback ' => function ($param, $request, $key) {
                return sanitize_text_field($param);
            }
        ),
    ),
));

function wt_crm_mobile_exists($params, $exclude = '')
{
    $args     = [
        'body' => $params
    ];
    $response = wt_crm_api('profile/search-simple', $args);

    if (is_wp_error($response))
        return $response;

    if (!isset($response[0]['CustomerNumber']))
        return false;

    if ($exclude) {
        foreach ($response as $i => $customer) {
            if ($customer['CustomerNumber'] == $exclude) {
                unset($response[$i]);
                break;
            }
        }
    }

    if (!isset($response[0]['CustomerNumber']))
        return false;

    return true;
}

register_rest_route('wt/v1', '/crm/mobile-exists', array(
    'methods' => 'POST',
    'callback' => function ($request) {
        $params  = $request->get_params();
        $exclude = empty($params['exclude']) ? '' : $params['exclude'];

        return wt_crm_mobile_exists($params, $exclude);
    },
    'args' => array(
        'MobileNumberCountryCode' => array(
            'required' => true,
            'validate_callback' => function ($param, $request, $key) {
                return !empty($param);
            },
            'sanitize_callback ' => function ($param, $request, $key) {
                return sanitize_text_field($param);
            }
        ),
        'MobileNumber' => array(
            'required' => true,
            'validate_callback' => function ($param, $request, $key) {
                return !empty($param);
            },
            'sanitize_callback ' => function ($param, $request, $key) {
                return sanitize_text_field($param);
            }
        ),
        'exclude' => array(
            'required' => false,
            'validate_callback' => function ($param, $request, $key) {
                return !empty($param);
            },
            'sanitize_callback ' => function ($param, $request, $key) {
                return sanitize_text_field($param);
            }
        ),
    ),
));

function wt_crm_is_receipt($params)
{
    // return ['Status' => 'VALID'];
    $earliest = strtotime('-7 days');

    if ($params['TransactionDate'] < wt_format_date(strtotime('-7 days'), 'input'))
        return new WP_Error('wt_invalid_date', 'Receipt date has to be on/after ' . wt_format_date($earliest, 'display'));

    $args = [
        'body' => $params
    ];
    // wt_dump($args);
    $response = wt_crm_api('customize/validate-receipt', $args);

    return $response;
}

register_rest_route('wt/v1', '/crm/is-receipt', array(
    'methods' => 'POST',
    'callback' => function ($request) {
        $params = $request->get_params();

        return wt_crm_is_receipt($params);
    },
    'args' => array(
        'ReceiptId' => array(
            'required' => true,
            'validate_callback' => function ($param, $request, $key) {
                return !empty($param);
            },
            'sanitize_callback ' => function ($param, $request, $key) {
                return sanitize_text_field($param);
            }
        ),
        'TransactionDate' => array(
            'required' => true,
            'validate_callback' => function ($param, $request, $key) {
                return !empty($param);
            },
            'sanitize_callback ' => function ($param, $request, $key) {
                return wt_crm_format_date($param);
            }
        ),
        'SalesAmount' => array(
            'required' => false,
            'validate_callback' => function ($param, $request, $key) {
                return is_numeric($param);
            },
            'sanitize_callback ' => function ($param, $request, $key) {
                return (float)$param;
            }
        ),
    ),
));

register_rest_route('wt/v1', '/crm/login', array(
    'methods' => 'POST',
    'callback' => function ($request) {
        if (function_exists('wt_verify_captcha')) {
            $verified = wt_verify_captcha();

            if (is_wp_error($verified))
                return $verified;
        }

        $params = $request->get_params();

        $customers = wt_crm_search($params['nric_or_email'], '', false);

        if (!$customers) //Your NRIC or email address does not match
            return new WP_Error('wt_error', 'Login incorrect. Kindly re-enter the fields or please contact Customer Service at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>.', ['status' => 401]);

        foreach ($customers as $customer) {

            $args = array(
                'body' => array(
                    'Password' => $params['password']
                )
            );

            $response = wt_crm_api('profile/' . $customer['CustomerNumber'] . '/signin', $args);

            if (!is_wp_error($response)) {
                wt_crm_set_session($response);

                if ($response['NeedPasswordChange']) {
                    wt_crm_email_verified($customer['CustomerNumber']);

                    return home_url('my-account/profile/#password');
                } else {
                    return home_url('my-account/');
                }
            }
        }

        return $response;
    },
    'args' => array(
        'nric_or_email' => array(
            'required' => true,
            'validate_callback' => function ($param, $request, $key) {
                return !empty($param);
            },
            'sanitize_callback ' => function ($param, $request, $key) {
                return sanitize_text_field($param);
            }
        ),
        'password' => array(
            'required' => true,
            'validate_callback' => function ($param, $request, $key) {
                return !empty($param);
            },
        ),
    ),
));

// add_action('wt_crm_created_profile', function($CustomerNumber, $Password, $fb_user_id)
// {
// 	$response = wt_crm_api( 'profile/' . $CustomerNumber . '/password/webportal/create', [
// 		'body' => [
// 			'Password' => $Password,
// 			'PasswordQuestion' => '',
// 			'PasswordAnswer' => ''
// 		]
// 	]);
//
// 	if (!is_wp_error($response))
// 	{
// 		wt_crm_set_session($response);
// 	}
//
// 	if (!empty($fb_user_id))
// 	{
// 		$response = wt_crm_api('profile/' . $CustomerNumber . '/social-profile', [
// 			'body' => [
// 				'MediaCode' => 'facebook',
// 				'identifier' => "www.facebook.com/" . $fb_user_id,
// 			]
// 		]);
// 	}
// }, 10, 3);

$fields = array(
    'FirstName',
    'LastName',
    // 'IC',
    'Email',
    'MobileCountryCode',
    'Mobile',
    'DOB',
    'GenderCode',
    'Password',
    'pass2',
);
$args   = array();

foreach ($fields as $field) {
    $args[$field] = array(
        'required' => true,
        'validate_callback' => function ($param, $request, $key) {
            return !empty($param);
        },
        'sanitize_callback ' => function ($param, $request, $key) {
            return sanitize_text_field($param);
        }
    );
}

foreach (['FirstName', 'LastName'] as $field) {
    $args[$field]['sanitize_callback'] = function ($param, $request, $key) {
        return substr($param, 0, 100);
    };
}

$args['Email']['validate_callback'] = function ($param, $request, $key) {
    return is_email($param);
};

// $args['Mobile']['sanitize_callback'] = function($param, $request, $key)
// {
// 	return preg_replace('/\D/', '', $param);
// };

$passwords = array(
    'Password',
    'pass2',
);

foreach ($passwords as $field) {
    $args[$field]['sanitize_callback'] = null;
}

function wt_is_possible_phone_number($number)
{
    require_once get_template_directory() . '/vendor/autoload.php';

    $phoneNumberUtil = libphonenumber\PhoneNumberUtil::getInstance();
    $phoneNumber     = $phoneNumberUtil->parse($number, 'SG', null, true);
    return $phoneNumberUtil->isPossibleNumber($phoneNumber);
}

register_rest_route('wt/v1', '/crm/customers', array(
    'methods' => 'POST',
    'callback' => function ($request) {
        if (function_exists('wt_verify_captcha')) {
            $verified = wt_verify_captcha();

            if (is_wp_error($verified))
                return $verified;
        }

        $params = $request->get_params();

        if ($params['Password'] != $params['pass2'])
            return new WP_Error('wt_password_mismatch', 'Passwords must match.', array('status' => 400));

        if (function_exists('wt_check_password')) {
            $check_password = wt_check_password($params['Password']);

            if (is_wp_error($check_password))
                return $check_password;
        }

        if (empty($params['interests']['INTE038']))
            return new WP_Error('wt_missing_field', 'Kindly accept the terms and conditions to join us as a member.', array('status' => 400));

        if ($params['properties']['NRIC_FIN_NO'] == 'NO' && empty($params['IC']))
            $params['IC'] = $params['passport'];

        if (empty($params['IC']))
            return new WP_Error('wt_missing_field', 'Please enter NRIC/FIN/Passport No.', array('status' => 400));

        $now     = wt_crm_format_date(time());
        $fb_user = wt_get_fb_user('id,email');

        if (!empty($fb_user['email']))
            $params['Email'] = $fb_user['email'];

        if (!empty($params['referral'])) {
            $response = wt_crm_search($params['IC']);

            if (empty($response))
                $response = wt_crm_search($params['Email']);

            if (empty($response)) {
                $response = wt_crm_mobile_exists([
                    'MobileNumberCountryCode' => $params['MobileCountryCode'],
                    'MobileNumber' => $params['Mobile'],
                ]);
            }

            if ($response)
                return new WP_Error('wt_conflict', 'You have an existing membership with us. <a data-open="login-modal">Please login here</a> or contact Customer Service at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>', array('status' => 409));

            if (!empty($params['referral']['promotion_code'])) {
                $purchase = [
                    'TransactionDate' => $now,
                    // 'PromotionCodes' => $params['referral']['promotion_code'],
                    'PromotionCodes' => [['Code' => $params['referral']['promotion_code']]],
                    'POSId' => empty($params['referral']['pos_id']) ? 'ZALORA' : $params['referral']['pos_id']
                ];
            }
        }

        if (!wt_is_possible_phone_number("+{$params['MobileCountryCode']}{$params['Mobile']}"))
            return new WP_Error('wt_invalid_value', 'Please enter a valid mobile no.', array('status' => 400));

        if ($params['DOB'] > current_time('Y-m-d'))
            return new WP_Error('wt_invalid_value', 'Please enter a valid Date of Birth.', array('status' => 400));

        if (!empty($params['Purchase']['ReceiptNumber']) && !empty($params['Purchase']['TransactionDate']) && !empty($params['Purchase']['Payments'][0]['Amount'])) {
            // $receipt = wt_crm_is_receipt([
            // 	'ReceiptId' => $params['Purchase']['ReceiptNumber'],
            // 	'TransactionDate' => $params['Purchase']['TransactionDate'],
            // 	'SalesAmount' => $params['Purchase']['Payments'][0]['Amount'],
            // ]);
            //
            // if (!is_wp_error($receipt) && isset($receipt['Status']))
            // {
            // 	if ($receipt['Status'] == 'INVALID')
            // 		return new WP_Error('wt_invalid_value', 'Receipt is invalid', array('status' => 400));
            //
            // 	if ($receipt['Status'] == 'TAGGED')
            // 		return new WP_Error('wt_conflict', 'Receipt has already been registered', array('status' => 409));

            $purchase                       = $params['Purchase'];
            $purchase['TransactionDate']    = wt_crm_format_date($purchase['TransactionDate']);
            $purchase['Items'][0]['Amount'] = $purchase['Payments'][0]['Amount'];
            // 	$purchase['Payments'][0]['Currency'] = 'SGD';
            // 	$purchase = array_replace_recursive($default_purchase, $purchase);
            // }
        } else if (empty($params['referral']) && empty($params['no_receipt'])) {
            return new WP_Error('wt_missing_value', 'Please acknowledge that you are not uploading any receipt.', ['status' => 400]);
        }


        $profile = $params;

        unset($profile['Password'], $profile['pass2'], $profile['properties'], $profile['contact_preferences'], $profile['interests'], $profile['Purchase'], $profile['referral']);

        // if (!empty($profile['DOB']))
        $profile['DOB'] = wt_crm_format_date($profile['DOB']);

        // if (empty($profile['GenderCode']))
        // 	unset($profile['GenderCode']);
        // wt_log($profile, 'crm');
        // if (!empty($params['DOB']))
        // 	$profile['DOB'] = date('Y-m-d', strtotime($params['DOB']));

        $default_profile = [
            "JoinLocation" => "LWEB",
            "DateJoined" => $now,
            "Addresses" => [
                [
                    "AddressType" => "HOME",
                    "Address1" => "",
                    "Address2" => "",
                    "Address3" => "",
                    "Address4" => "",
                    "State" => "",
                    "StateValue" => null,
                    "City" => "",
                    "CityCode" => null,
                    "District" => "",
                    "DistrictCode" => null,
                    "SubDistrict" => "",
                    "SubDistrictCode" => null,
                    "PostalCode" => "",
                ]
            ],
            "Properties" => [
                [
                    "Name" => "NRIC/FIN NO.",
                    "Value" => empty($params['properties']['NRIC_FIN_NO']) ? 'YES' : $params['properties']['NRIC_FIN_NO']
                ],
                [
                    "Name" => "Children Age 14",
                    "Value" => empty($params['properties']['Children_Age_14']) ? 'NO' : $params['properties']['Children_Age_14']
                ],
                [
                    "Name" => "Contact Consent",
                    "Value" => empty($params['properties']['Contact_Consent']) ? 'NO' : 'YES'
                ],
                [
                    "Name" => "EmailVerification",
                    "Value" => empty($fb_user['email']) ? 'NO' : 'YES'
                ],
            ],
            "Interests" => [
                [
                    "Code" => "INTE038",
                    "Value" => "Y"
                ]
            ]
            // "ContactPreferences" => [
            // 	[
            // 		"Code" => "WTR_CALL",
            // 		"Value" => $consent[0]
            // 	],
            // 	[
            // 		"Code" => "EMAIL",
            // 		"Value" => $consent[0]
            // 	],
            // 	[
            // 		"Code" => "MAIL",
            // 		"Value" => $consent[0]
            // 	],
            // 	[
            // 		"Code" => "SMS",
            // 		"Value" => $consent[0]
            // 	]
            // ],
        ];

        $args['body'] = [
            'Profile' => array_replace_recursive($default_profile, $profile),
            'Membership' => [
                "MemberType" => "Silver",
                "LocationCode" => "LWEB",
                "Date" => $now,
                // "CardName" => "",
                // "MemberNumber" => "",
                // "Description" => "",
                // "Registrator" => "",
                // "SalePerson" => "",
                // "PromoCode" => null
            ],
        ];

        if (isset($purchase)) {
            $default_purchase = [
                "StoreCode" => "LHQS",
                "ReceiptNumber" => "ORDER123456",
                "DeviceType" => "",
                "DeviceId" => "",
                "POSId" => "",
                "OperatorId" => "",
                "Description" => "",
                "PromotionCodes" => "",
                'Payments' => [
                    [
                        "Currency" => "SGD",
                        "Amount" => 0
                    ]
                ],
                'Items' => [
                    [
                        "Currency" => "SGD",
                        "ItemCode" => "PUR01",
                        "Amount" => 0
                    ]
                ]
            ];

            $args['body']['Purchase'] = array_replace_recursive($default_purchase, $purchase);
        }

        // wt_log(json_encode($args['body']), 'crm');

        if (empty($params['referral'])) {
            $response = wt_crm_api('customize/signup-with-receipt', $args);
        } else {
            $response = wt_crm_api('profile/create-with-membership', $args);
        }

        if (is_wp_error($response))
            return $response;
        // wt_log($response, 'crm');
        // wt_crm_set_session($response['Profile']);
        $customer_number = $response['Profile']['CustomerNumber'];

        try {
            if (!empty($params['referral']['voucher_code']) || !empty($params['referral']['template_id'])) {
                $response = wt_crm_api("profile/$customer_number/memberships", ['method' => 'GET']);

                if (!is_wp_error($response) && is_array($response)) {
                    foreach ($response as $membership) {
                        if ($membership['Status'] == 'ACTIVE') {
                            if (!empty($params['referral']['voucher_code'])) {
                                $args = [
                                    'MemberNumber' => $membership['MemberNo'],
                                    "LocationCode" => "LWEB",
                                    "VoucherDetails" => [
                                        [
                                            "VoucherCode" => $params['referral']['voucher_code'],
                                            "VoucherSource" => "Web",
                                            "ReferenceNumber" => "",
                                            "VoucherText" => "",
                                            "Description" => ""
                                        ]
                                    ],
                                ];

                                if (!empty($params['referral']['voucher_qty']) && is_numeric($params['referral']['voucher_qty'])) {
                                    if ($params['referral']['voucher_qty'] > 99) {
                                        $params['referral']['voucher_qty'] = 99;
                                    }

                                    for ($j = 1; $j < $params['referral']['voucher_qty']; $j++) {
                                        $args['VoucherDetails'][] = $args['VoucherDetails'][0];
                                    }
                                }

                                $response = wt_crm_api('voucher/redeem', ['body' => $args]);
                            }

                            if (
                                !empty($params['referral']['template_id']) && (
                                    empty($params['referral']['voucher_code']) ||
                                    (!empty($params['referral']['voucher_code']) && !is_wp_error($response))
                                )
                            ) {
                                $args = [
                                    'Subject' => empty($params['referral']['subject']) ? 'Welcome To The WT+ Fashion Community!' : $params['referral']['subject'],
                                    "SenderId" => "wt+",
                                    "TemplateId" => $params['referral']['template_id'],
                                    "Content" => "",
                                    "Data" => [],
                                    "IncludeSignature" => "true",
                                    "IncludeBrowserView" => "true",
                                ];

                                if (!empty($params['referral']['voucher_code']) && !empty($response[0]['ReferenceNumber'])) {
                                    $args['Data'] = [
                                        [
                                            'Key' => 'Code',
                                            'Value' => $response[0]['ReferenceNumber'],
                                        ]
                                    ];
                                }

                                // wt_log(json_encode($args), 'crm');
                                $response = wt_crm_api("member/{$membership['MemberNo']}/send-email", ['body' => $args]);
                            }

                            break;
                        }
                    }
                }
            }


            $response = wt_crm_api('profile/' . $customer_number . '/password/webportal/create', [
                'body' => [
                    'Password' => $params['Password'],
                    'PasswordQuestion' => '',
                    'PasswordAnswer' => ''
                ]
            ]);

            // if (!is_wp_error($response))
            // {
            // 	wt_crm_set_session($response);
            // }

            if (!empty($fb_user['id'])) {
                $response = wt_crm_api('profile/' . $customer_number . '/social-profile', [
                    'body' => [
                        'MediaCode' => 'facebook',
                        'identifier' => "www.facebook.com/" . $fb_user['id'],
                    ]
                ]);
            }

            if (!empty($params['properties']['Contact_Consent']) && is_array($params['contact_preferences'])) {
                $contact_preferences = get_transient('wt_contact_preferences');

                foreach ($contact_preferences as $i => $preference) {
                    if (empty($params['contact_preferences'][$preference['Code']]))
                        $contact_preferences[$i]['Value'] = 'N';
                    else
                        $contact_preferences[$i]['Value'] = 'Y';
                }

                $args = [
                    'method' => 'PUT',
                    'body' => $contact_preferences
                ];
                // wt_log($params['contact_preferences'], 'crm');
                // wt_log($contact_preferences, 'crm');
                $response = wt_crm_api('profile/' . $customer_number . '/contact-preferences', $args);
            }
        } catch (Exception $e) {
            wt_log($e, 'crm');
        }

        return home_url('signup-confirmation');
        // return home_url('wt/') . '#benefits';
    },
    'args' => $args
));

unset($args['Password'], $args['pass2'], $args['Email'], $args['MobileCountryCode'], $args['Mobile']);

// $args['Interests'] = [
// 	'required' => true,
// 	'validate_callback' => function($param, $request, $key)
// 	{
// 		return !empty( $param ) && is_array($param);
// 	}
// ];

register_rest_route('wt/v1', '/crm/customers/(?P<CustomerNumber>\d+)', array(
    'methods' => 'POST',
    'callback' => function ($request) {
        $params = $request->get_params();

        if ($params['properties']['NRIC_FIN_NO'] == 'NO' && empty($params['IC']))
            $params['IC'] = $params['passport'];

        if (empty($params['IC']))
            return new WP_Error('wt_missing_field', 'Please enter NRIC/FIN/Passport No.', array('status' => 400));


        if (empty($params['Phones']))
            return new WP_Error('wt_missing_field', 'Please enter mobile no.', array('status' => 400));

        $mobile = reset($params['Phones']);

        if (empty($mobile['CountryCode']))
            return new WP_Error('wt_missing_field', 'Please select mobile country code.', array('status' => 400));

        if (empty($mobile['Number']))
            return new WP_Error('wt_missing_field', 'Please enter mobile no.', array('status' => 400));


        if (empty($params['Addresses']))
            return new WP_Error('wt_missing_field', 'Please select Country of Residence.', array('status' => 400));

        $address = reset($params['Addresses']);

        if (empty($address['CountryCode']))
            return new WP_Error('wt_missing_field', 'Please select Country of Residence.', array('status' => 400));

        if (empty($address['PostalCode']))
            return new WP_Error('wt_missing_field', 'Please enter Postal Code.', array('status' => 400));


        $response = wt_crm_search($params['IC'], $params['CustomerNumber']);

        if (empty($response)) {
            if (empty($params['Phones']) || !is_array($params['Phones']))
                return new WP_Error('wt_missing_field', 'Please enter your mobile no.', array('status' => 400));

            $response = wt_crm_mobile_exists([
                'MobileNumberCountryCode' => $mobile['CountryCode'],
                'MobileNumber' => $mobile['Number'],
            ], $params['CustomerNumber']);
        }

        if ($response)
            return new WP_Error('wt_conflict', "Your data is conflicting with another member's. Please contact Customer Service at <a href='mailto:help@wtplus.com.sg'>help@wtplus.com.sg</a>.", array('status' => 409));


        $profile = wt_crm_api('profile/' . $params['CustomerNumber'], ['method' => 'GET']);

        if (is_wp_error($profile)) {
            return $profile;
        }

        $new_profile = $params;

        if (!empty($new_profile['DOB']))
            $new_profile['DOB'] = wt_crm_format_date($new_profile['DOB']);


        if (empty($params['contact_preferences']))
            $params['contact_preferences'] = [];

        foreach ($profile['ContactPreferences'] as $i => $preference) {
            if (array_key_exists($preference['Code'], $params['contact_preferences']))
                $profile['ContactPreferences'][$i]['Value'] = 'Y';
            else
                $profile['ContactPreferences'][$i]['Value'] = 'N';
        }


        if (empty($params['interests']))
            $params['interests'] = [];

        foreach ($profile['Interests'] as $i => $interest) {
            if ($interest['Code'] == 'INTE038')
                continue;

            if (array_key_exists($interest['Code'], $params['interests']))
                $profile['Interests'][$i]['Value'] = 'Y';
            else
                $profile['Interests'][$i]['Value'] = 'N';
        }

        $have_children = empty($params['properties']['Children_Age_14']) ? 'NO' : $params['properties']['Children_Age_14'];
        $properties    = [
            'NRIC_FIN_NO' => 'NRIC/FIN NO.',
            'Children_Age_14' => 'Children Age 14',
            'Employment_Status' => 'Employment Status',
            'Income_Level' => 'Income Level',
        ];

        foreach ($profile["CustomProperties"] as $i => $property) {
            $key = array_search($property['Name'], $properties);

            if ($key === false || !isset($params['properties'][$key]))
                continue;

            $profile["CustomProperties"][$i]['Value'] = $params['properties'][$key];
        }

        // $new_profile["CustomProperties"] = [
        // 	[
        // 		"Name" => "NRIC/FIN NO.",
        // 		"Value" => empty($params['properties']['NRIC_FIN_NO']) ? 'Yes' : $params['properties']['NRIC_FIN_NO']
        // 	],
        // 	[
        // 		"Name" => "Children Age 14",
        // 		"Value" => $have_children
        // 	],
        // 	[
        // 		"Name" => "Employment Status",
        // 		"Value" => empty($params['properties']['Employment_Status']) ? '' : $params['properties']['Employment_Status']
        // 	],
        // 	[
        // 		"Name" => "Income Level",
        // 		"Value" => empty($params['properties']['Income_Level']) ? 'INCO000' : $params['properties']['Income_Level']
        // 	],
        // ];

        if (!empty($params['ContactPersons']) && is_array($params['ContactPersons'])) {
            $default_child = [
                "Type" => "CHILD",
                "RefNumber" => "",
                "FirstName" => "",
                "LastName" => "",
                "IC" => "",
                "DOB" => "",
                "Gender" => ""
            ];

            foreach ($new_profile['ContactPersons'] as $i => $child) {
                if (empty($child['FirstName']) && empty($child['LastName']) && empty($child['DOB'])) {
                    unset($new_profile['ContactPersons'][$i]);
                    continue;
                }

                $new_profile['ContactPersons'][$i]              = wp_parse_args($child, $default_child);
                $new_profile['ContactPersons'][$i]['FirstName'] = substr($child['FirstName'], 0, 50);
                $new_profile['ContactPersons'][$i]['LastName']  = substr($child['LastName'], 0, 50);
                $new_profile['ContactPersons'][$i]['DOB']       = $child['DOB'] ? wt_crm_format_date($child['DOB']) : '';
            }

            $new_profile['ContactPersons'] = array_values($new_profile['ContactPersons']);
        }

        $args = [
            'method' => 'DELETE',
        ];

        foreach ($profile['ContactPersons'] as $i => $old) {
            $exists = false;

            foreach ($new_profile['ContactPersons'] as $j => $new) {
                if ($old['RefNumber'] == $new['RefNumber']) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                wt_crm_api('profile/' . $params['CustomerNumber'] . '/contact-person/' . $old['RefNumber'], $args);
            }
        }

        if (!isset($new_profile['ContactPersons']))
            $new_profile['ContactPersons'] = [];

        unset($new_profile['Email'], $new_profile['passport'], $new_profile['contact_preferences'], $new_profile['interests'], $new_profile['properties'], $profile['ContactPersons']);

        $profile = array_replace_recursive($profile, $new_profile);
        // unset($profile[0], $profile['Mobile']);
        // wt_log($profile, 'crm');
        // wt_log(json_encode($profile), 'crm');

        $args = [
            'method' => 'PUT',
            'body' => $profile
        ];

        $response = wt_crm_api('profile/' . $params['CustomerNumber'], $args);
        // wt_log($response);
        if (is_wp_error($response))
            return $response;

        // if ($have_children != 'YES')
        // 	return;
        //
		// $args = [];
		// if (!empty($params['children']) && is_array($params['children']))
		// {
		// 	$default_child = [
		// 		"Type" => "CHILD",
		// 		"RefNumber" => "",
		// 		"FirstName" => "",
		// 		"LastName" => "",
		// 		"IC" => "",
		// 		"DOB" => "",
		// 		"Gender" => ""
		// 	];
        //
		// 	foreach ($params['children'] as $i => $child)
		// 	{
		// 		$params['children'][$i] = wp_parse_args($child, $default_child);
		// 		$params['children'][$i]['DOB'] = wt_crm_format_date($params['children'][$i]['DOB']);
		// 	}
        //
		// 	$args = $params['children'];
		// }

        // $response = wt_crm_api( 'profile/' . $params['CustomerNumber'] . '/contact-person', $args );

        // return $response;
    },
    'args' => $args
));

register_rest_route('wt/v1', '/crm/customers/(?P<CustomerNumber>\d+)/contact-preferences', [
    'methods' => 'POST',
    'callback' => function ($request) {
        $params = $request->get_url_params();
        $new    = $request->get_body_params();
        // $new = [];
        //
		// if ($body)
		// {
		// 	foreach ($body as $code => $value)
		// 	{
		// 		$new[] = [
		// 			'Code' => $code,
		// 			'Value' => 'Y'
		// 		];
		// 	}
		// }

        $current = wt_crm_api('profile/' . $params['CustomerNumber'] . '/contact-preferences', ['method' => 'GET']);

        if (!is_wp_error($current)) {
            // $current = ['contacts' => $current];
            // $new = array_replace_recursive($current, $new);

            foreach ($current as $i => $contact) {
                if (array_key_exists($contact['Code'], $new))
                    $current[$i]['Value'] = 'Y';
                else
                    $current[$i]['Value'] = 'N';
            }
        }

        // wt_log($current);
        $args = [
            'method' => 'PUT',
            'body' => $current
        ];

        $response = wt_crm_api('profile/' . $params['CustomerNumber'] . '/contact-preferences', $args);

        if (is_wp_error($response))
            return $response;

        return true;
    },
]);

$passwords[] = "OldPassword";
$args        = [];

foreach ($passwords as $field) {
    $args[$field] = array(
        'required' => true,
        'validate_callback' => function ($param, $request, $key) {
            return !empty($param);
        },
    );
}

register_rest_route('wt/v1', '/crm/customers/(?P<CustomerNumber>\d+)/password', [
    'methods' => 'POST',
    'callback' => function ($request) {
        $params = $request->get_params();

        if ($params['Password'] != $params['pass2'])
            return new WP_Error('wt_password_mismatch', 'Passwords must match.', array('status' => 400));

        $check_password = wt_check_password($params['Password']);

        if (is_wp_error($check_password))
            return $check_password;

        $args = [
            'body' => $params
        ];

        $response = wt_crm_api('profile/' . $params['CustomerNumber'] . '/password/webportal/change', $args);

        if (is_wp_error($response))
            return $response;

        return true;
    },
    'args' => $args
]);

register_rest_route('wt/v1', '/crm/customers/(?P<CustomerNumber>\d+)/custom-property/(?P<Name>\w+)', [
    'methods' => 'POST',
    'callback' => function ($request) {
        $params = $request->get_params();

        $body = [
            "Name" => $params['Name'],
            "DisplayText" => '',
            "Value" => '',
            "IsMandatory" => false
        ];

        if ($params['Name'] == 'DoNotShow') {
            $body = wp_parse_args([
                "DisplayText" => "Do not show me this again",
                "Value" => 'YES',
            ], $body);
        }

        $args = [
            'method' => 'PUT',
            'body' => $body
        ];

        $response = wt_crm_api('profile/' . $params['CustomerNumber'] . '/custom-property/' . $params['Name'], $args);

        // if (is_wp_error($response))
        // 	return $response;
        //
        // return true;
    },
    // 'args' => [
    // 	'Value' => [
    // 		'required' => true,
    // 		'validate_callback' => function($param, $request, $key)
    // 		{
    // 			return !empty( $param );
    // 		},
    // 		'sanitize_callback ' => function($param, $request, $key)
    // 		{
    // 			return sanitize_text_field( $param );
    // 		}
    // 	],
    // ],
]);

register_rest_route('wt/v1', '/crm/forgot-password', [
    'methods' => 'POST',
    'callback' => function ($request) {
        if (function_exists('wt_verify_captcha')) {
            $verified = wt_verify_captcha();

            if (is_wp_error($verified))
                return $verified;
        }

        $params   = $request->get_params();
        $args     = [
            'body' => $params
        ];
        $response = wt_crm_api('profile/search-simple', $args);

        if (is_wp_error($response) || !isset($response[0]['CustomerNumber'])) {
            $min_age = get_option('options_wt_password_min_age');

            if ($min_age && is_numeric($min_age))
                $min_age = (int)abs($min_age);
            else
                $min_age = 3;

            return new WP_Error('wt_error', "User account cannot be found or password can only be changed after $min_age " . _n('day', 'days', $min_age, 'wt') . '. Please contact Customer Service at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>.', ['status' => 400]);
            // return new WP_Error('wt_not_found', 'Record not found/matched. Please contact Customer Service at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>. <br><a href="' . home_url('signup') . '">Not a member?</a>', ['status' => 404]); //Please try again or <a href="' . home_url('contact') . '">contact us</a> for assistance.
        }

        $args = [
            'body' => [
                "CommunicationType" => "Email",
                "CreatePassword" => true
            ]
        ];

        $response = wt_crm_api('profile/' . $response[0]['CustomerNumber'] . '/password/webportal/forget', $args);
        // wt_log($response, 'crm');
        if (is_wp_error($response))
            return $response;

        return true;
    },
    'args' => array(
        'NRIC' => array(
            'required' => true,
            'validate_callback' => function ($param, $request, $key) {
                return !empty($param);
            },
            'sanitize_callback ' => function ($param, $request, $key) {
                return sanitize_text_field($param);
            }
        ),
        'EmailAddress' => array(
            'required' => true,
            'validate_callback' => function ($param, $request, $key) {
                return is_email($param);
            },
            'sanitize_callback ' => function ($param, $request, $key) {
                return sanitize_email($param);
            }
        ),
    ),
]);

register_rest_route('wt/v1', '/crm/members/(?P<MemberNumber>\w+)/vouchers/redeem', [
    'methods' => 'POST',
    'callback' => function ($request) {
        $params = array_replace_recursive([
            "LocationCode" => "LWEB",
        ], $request->get_params());

        foreach ($params['VoucherDetails'] as $i => $voucher) {
            $params['VoucherDetails'][$i]["VoucherSource"]   = "Web";
            $params['VoucherDetails'][$i]["ReferenceNumber"] = "";
            $params['VoucherDetails'][$i]["VoucherText"]     = "";
            $params['VoucherDetails'][$i]["Description"]     = "";

            if (!empty($params['qty']) && is_numeric($params['qty'])) {
                for ($j = 1; $j < $params['qty']; $j++)
                    $params['VoucherDetails'][] = $params['VoucherDetails'][$i];
            }
        }
        // wt_log($params, 'crm');
        $args = [
            'body' => $params
        ];

        $response = wt_crm_api('voucher/redeem', $args);

        if (is_wp_error($response))
            return $response;

        // return $response;
    },
    'args' => array(
        'VoucherDetails' => array(
            'required' => true,
            'validate_callback' => function ($param, $request, $key) {
                return !empty($param) && is_array($param);
            },
        ),
    ),
]);

register_rest_route('wt/v1', '/crm/members/(?P<MemberNumber>\w+)/emails/(?P<TemplateId>[A-Z0-9-]+)', [
    'methods' => 'POST',
    'callback' => function ($request) {
        $params = $request->get_url_params();
        $args   = array_replace_recursive([
            'Subject' => 'Welcome To The WT+ Fashion Community!',
            "SenderId" => "wt+",
            "TemplateId" => $params['TemplateId'],
            "Content" => "",
            "Data" => [
                0 => [
                    'Key' => "EncryptCustomerNo",
                ]
            ],
            "IncludeSignature" => "true",
            "IncludeBrowserView" => "true",
        ], $request->get_body_params());

        $response = wt_crm_api("member/{$params['MemberNumber']}/send-email", ['body' => $args]);
        // wt_log($args, 'crm');
        if (is_wp_error($response))
            return $response;

        // return $response;
    },
    'args' => array(
        'Data' => array(
            'required' => true,
            'validate_callback' => function ($param, $request, $key) {
                return !empty($param) && is_array($param);
            },
        ),
    ),
]);

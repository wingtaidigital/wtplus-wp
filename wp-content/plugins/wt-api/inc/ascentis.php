<?php
defined('ABSPATH') || exit;

register_rest_route('wt/v1', '/crm/postal-codes/(?P<PostalCode>\d+)', [
	'methods' => WP_REST_Server::READABLE,
	'callback' => function($request)
	{
		$params = $request->get_params();

		$response = wt_crm_api(wp_parse_args($params, [
			'Command' => 'ADDRESS LOOKUP',
		]));

		return $response;
	},
]);

register_rest_route('wt/v1', '/crm/friends', [
	'methods' => WP_REST_Server::CREATABLE,
	'callback' => function($request)
	{
		$params = $request->get_params();
		$friends = [];
//		$emails = [];

		foreach ($params['NameList'] as $friend)
		{
			if (empty($friend['Name']) || empty($friend['Email']))
				continue;

			$friend['Email'] = sanitize_email($friend['Email']);

			if (wt_crm_exists('email', $friend['Email'], true))
				continue;

			$friends[] = [
				'Name'  => sanitize_text_field($friend['Name']),
				'Email' => $friend['Email'],
			];

//			$emails[] = $friend['Email'];
		}

		if (empty($friends))
			return new WP_Error('wt_400', "Please invite a friend that hasn't registered", ['status' => 400]);

		$response = wt_crm_api([
			'Command'           => 'FRIENDS INVITATION',
			'EmailTemplateName' => 'FRIENDS INVITATION TEMPLATE',
			'MemberID'          => $_SESSION['wt_crm']['MemberID'],
			'CardNo'            => $_SESSION['wt_crm']['CardNo'],
			'NameList'          => $friends,
			'EmailAddressLists' => [],
			"NoOfDigits"        => 4,
			"SMSMaskName"       => "Ascentis",
			"SendSMS"           => "False",
			"MobileNoLists"     => [""],
			"SMSMessage"        => "",
			"SendEmail"         => "True"
		]);

		return $response;
	},
	'args' => array(
		'NameList' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param ) && is_array($param);
			},
			/*'sanitize_callback' => function($param, $request, $key)
			{
				return array_unique( $param );
			},*/
		),
	),
]);

register_rest_route('wt/v1', '/crm/customers/qr-code', [
	'methods' => WP_REST_Server::READABLE,
	'callback' => function($request)
	{
		$response = wt_crm_api([
			'Command' => 'CARD ENQUIRY',
			'CardNo'  => $_SESSION['wt_crm']['CardNo'],
		]);

		if (is_wp_error($response))
			return $response;

		return wt_crm_qr_code($_SESSION['wt_crm']['CardNo']);//. $response['CardInfo']['CVCInfo']['CVC']
	},
]);

register_rest_route('wt/v1', '/crm/exists', array(
	'methods' => WP_REST_Server::READABLE,
	'callback' => function($request)
	{
		$params = $request->get_query_params();

		return wt_crm_exists($params['field'], $params['value'], true);
	},
	'args' => array(
		'field' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param );
			},
			'sanitize_callback ' => function($param, $request, $key)
			{
				return sanitize_text_field( $param );
			}
		),
		'value' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param );
			},
			'sanitize_callback ' => function($param, $request, $key)
			{
				return sanitize_text_field( $param );
			}
		),
	),
));

register_rest_route('wt/v1', '/crm/login', array(
	'methods' => 'POST',
	'callback' => function($request)
	{
		/*if (function_exists('wt_verify_captcha'))
		{
			$verified = wt_verify_captcha();

			if (is_wp_error($verified))
				return $verified;
		}*/

		$params = $request->get_params();
		/*$token = wt_crm_get_token(true, 'AuthenticateByMember', $body = [
			"UserIDisCardNo" => false,
			"userID"         => $params['email_or_mobile'],
			"userPassword"   => $params['Password']
		]);

		if (is_wp_error($token))
			return $token;*/

		if (is_email($params['email_or_mobile']))
			$member = wt_crm_search(['Email' => $params['email_or_mobile']], '', false);
		else
			$member = wt_crm_search(['MobileNo' => $params['email_or_mobile']], '', false);

		if (is_wp_error($member))
		{
			if ($member->get_error_code() === 'as_54')
			{
				if (is_email($params['email_or_mobile']))
					return new WP_Error('wt_500', 'Invalid email address. Please try again with your mobile number.', ['status' => 500]);
				else
					return new WP_Error('wt_500', 'Invalid mobile number. Please try again with your email address.', ['status' => 500]);
			}

			return $member;
		}

		if (!empty($member['MemberLists']) && count($member['MemberLists']) > 1)
		{
			if (is_email($params['email_or_mobile']))
				return new WP_Error('wt_500', 'Invalid email address. Please try again with your mobile number.', ['status' => 500]);
			else
				return new WP_Error('wt_500', 'Invalid mobile number. Please try again with your email address.', ['status' => 500]);
		}

		if (!empty($member['CardLists']) && is_array($member['CardLists']))
		{
			foreach ($member['CardLists'] as $card)
			{
				if (empty($card['MembershipStatusCode']) || $card['MembershipStatusCode'] === 'INACTIVE')
					continue;

				$auth = wt_crm_api([
					'Command'        => 'MEMBER AUTHENTICATION',
					'Password'       => $params['Password'],
					'UserID'         => $card['CardNo'],
					'UserIDisCardNo' => true,
					'GetToken'       => true
				]);

				if (is_wp_error($auth))
					return $auth;

				if (!empty($auth['MemberToken']))
				{
					wt_crm_set_session($card, $member, $auth['MemberToken']);

					return wt_login_redirect();
				}

				if (!empty($auth['ReturnStatus']) && $auth['ReturnStatus'] == 242)
				{
					return new WP_Error('as_242', 'Please <a href="' . home_url('forgot-password') . '">reset password</a>.');
				}
			}
		}

		return new WP_Error('wt_401', 'Login incorrect. Kindly re-enter the fields or please contact Customer Service at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>.', ['status' => 401]);
	},
	'args' => array(
		'email_or_mobile' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param );
			},
			'sanitize_callback' => function($param, $request, $key)
			{
				return trim( $param );
			}
		),
		'Password' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param );
			},
		),
	),
));

$fields = array(
	'Email',
	'MobileNo',
	'DOB',
	// 'Password',
	// 'pass2',
);
$args = array();

foreach ($fields as $field)
{
	$args[$field] = array(
		'required' => true,
		'validate_callback' => function($param, $request, $key)
		{
			return !empty( $param );
		},
		'sanitize_callback ' => function($param, $request, $key)
		{
			return sanitize_text_field( $param );
		}
	);
}

/*foreach (['DOB', 'GenderCode'] as $field)
{
	$args[$field]['required'] = false;
	unset($args[$field]['validate_callback']);
}*/

foreach (['FirstName', 'LastName'] as $field)
{
	$args[$field]['sanitize_callback'] = function($param, $request, $key)
	{
		return substr ( $param, 0, 100 );
	};
}

$args['Email']['validate_callback'] = function($param, $request, $key)
{
	return is_email( $param );
};

//$args['Mobile']['sanitize_callback'] = function($param, $request, $key)
//{
//	return preg_replace('/\D/', '', $param);
//};

/*$passwords = array(
	'Password',
	'pass2',
);

foreach ($passwords as $field)
{
	$args[$field]['sanitize_callback'] = null;
}*/

function wt_format_array(&$array, $keys)
{
	foreach ($keys as $k1)
	{
		if (empty($array[$k1]) || !is_array($array[$k1]))
			continue;

		foreach ($array[$k1] as $k2 => $value)
		{
			$array[$k1][] = [
				'Name'     => $k2,
				'ColValue' => is_array($value) ? implode(',', $value) : sanitize_text_field($value)
			];
			// wt_log($k2);
			// wt_log($array[$k1]);
			unset($array[$k1][$k2]);
		}
	}
}

function wt_is_possible_phone_number($number)
{
	require_once get_template_directory() . '/vendor/autoload.php';

	if (!class_exists('libphonenumber\PhoneNumberUtil'))
		return true;

	if ($number[0] !== '+')
		$number = '+' . $number;

	if (strpos($number, '+65') !== 0)
		return true;

	$phoneNumberUtil = libphonenumber\PhoneNumberUtil::getInstance();
	$phoneNumber = $phoneNumberUtil->parse($number, 'SG', null, true);
	$is_possible = $phoneNumberUtil->isValidNumber($phoneNumber);

	// if (!$is_possible)
		return $is_possible;

	// return in_array($phoneNumberUtil->getNumberType($phoneNumber), [libphonenumber\PhoneNumberType::MOBILE, libphonenumber\PhoneNumberType::FIXED_LINE_OR_MOBILE, libphonenumber\PhoneNumberType::VOIP, libphonenumber\PhoneNumberType::PERSONAL_NUMBER]);
}

register_rest_route('wt/v1', '/crm/customers', array(
	'methods' => WP_REST_Server::CREATABLE,
	'callback' => function($request)
	{
		if (function_exists('wt_verify_captcha'))
		{
			$verified = wt_verify_captcha();

			if (is_wp_error($verified))
				return $verified;
		}

		$params = $request->get_params();

		/*if ( $params['Password'] != $params['pass2'] )
			return new WP_Error( 'wt_password_mismatch', 'Passwords must match.', array('status' => 400) );

		if (function_exists('wt_check_password'))
		{
			$check_password = wt_check_password($params['Password']);

			if (is_wp_error($check_password))
				return $check_password;
		}*/

		if (!empty($params['confirm_email']) && $params['confirm_email'] !== $params['Email'])
			return new WP_Error('wt_400', 'Emails must match.', array('status' => 400));

		$required = [
			'DynamicFieldLists' => ['FirstName', 'LastName'/*, 'HaveChild'*/],
			'DynamicColumnLists' => ['Gender', 'CountryCode', 'Country', 'PostalCode']
		];

		foreach ($required as $list => $fields)
		{
			foreach ($fields as $field)
			{
				if (empty($params[$list][$field]))
				{
					return new WP_Error('wt_400', 'Please fill in all required fields.', array('status' => 400));
				}
			}
		}
		
		if (!preg_match('/\d{6}/', $params['DynamicColumnLists']['PostalCode']))
			return new WP_Error('wt_400', 'Invalid postal code', array('status' => 400));

		if (empty($params['DynamicFieldLists']['TnC']))
			return new WP_Error('wt_missing_field', 'Kindly accept the terms and conditions to join us as a member.', array('status' => 400));

		if (!empty($params['DOB']) && $params['DOB'] > current_time('Y-m-d'))
			return new WP_Error('wt_400', 'Please enter a valid Date of Birth.', array('status' => 400));

		$profile = $params;
		$fb_user = wt_get_fb_user('id,email');

		if (!empty($fb_user['email']))
			$profile['Email'] = $fb_user['email'];

		if (wt_crm_exists('email', $params['Email']) || wt_crm_exists('mobile', /*'+' . $profile['DynamicColumnLists']['CountryCode'] .*/ $params['MobileNo']))
			return new WP_Error('wt_409', 'You have an existing membership with us. <a data-open="login-modal">Please login here</a> or contact Customer Service at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>', array('status' => 409));

		$profile['DynamicColumnLists']['CountryCode'] = '+' . ltrim($profile['DynamicColumnLists']['CountryCode'], '+');
		$profile['MobileNo'] = $profile['DynamicColumnLists']['CountryCode'] . $profile['MobileNo'];

		if (!wt_is_possible_phone_number($profile['MobileNo']))
			return new WP_Error('wt_400', 'Please enter a valid mobile no.', array('status' => 400));

        $params['DynamicFieldLists']['FirstName'] = substr($params['DynamicFieldLists']['FirstName'], 0, 40);
        $params['DynamicFieldLists']['LastName'] = substr($params['DynamicFieldLists']['LastName'], 0, 59);
		$profile['Name'] = $params['DynamicFieldLists']['FirstName'] . ' ' . $params['DynamicFieldLists']['LastName'];
		$keys = ['DynamicFieldLists', 'DynamicColumnLists'];

		unset($profile['confirm_email'], $profile['g-recaptcha-response']);

		if (!empty($fb_user['id']))
			$profile['FacebookID'] = $fb_user['id'];

		if (!empty($params['DynamicColumnLists']['NotifyEmail']))
		{
			$profile['MailingLists'] = [];
			$lists = wt_crm_get_mailing_lists();

			foreach($lists as $list)
				$profile['MailingLists'][] = ['Name' => $list];

			unset($profile['DynamicColumnLists']['NotifyEmail']);
		}

		foreach ($keys as $k1)
		{
			if (!empty($profile[$k1]) && is_array($profile[$k1]))
			{
				foreach ($profile[$k1] as $k2 => $value)
				{
					$profile[$k1][] = [
						'Name'     => $k2,
						'ColValue' => is_array($value) ? implode(',', $value) : sanitize_text_field($value)
					];

					unset($profile[$k1][$k2]);
				}
			}
		}

		$default_profile = [
			"Command"                                => "MEMBERSHIP REGISTRATION2",
			"MembershipTypeCode"                     => "WTPlus",
			"TierCode"                               => "Silver",
			// 'Password'           => '1!Qqqqqq',//$params['MobileNo'],
			'Nric'                                   => function_exists('com_create_guid ') ? com_create_guid() : uniqid('', true),
			"RunRegistrationCampaign"                => true,
			"RegistrationCampaignType"               => "Registration Campaign",
			"CheckQualificationRulesForRegistration" => true,
		];
		
		if (!empty($fb_user['id']))
			$default_profile['OutletCode'] = 'Facebook';

		$args = array_replace_recursive($default_profile, $profile);

		if (empty($args['ReferrerCode']))
		{
			unset($args['ReferrerCode']);
		}
		else
		{
			$response = wt_crm_api([
				"Command"      => "REFERRER CODE VALIDATION",
				'ReferrerCode' => $args['ReferrerCode']
			]);

			if (is_wp_error($response))
			{
				return $response;
				// unset($args['ReferrerCode']);
			}
			elseif (empty($response['IsValid']))
			{
				return new WP_Error('wt_400', 'Invalid referrer code', ['status' => 400]);
			}

			$args['ReferrerCampaignType']               = 'Referrer Campaign';
			$args['ReferrerCampaignCode']               = 'Registration_Referrer';
			$args['CheckQualificationRulesForReferrer'] = true;
			$args['RefereeCampaignType']                = 'Referee Campaign';
			$args['RefereeCampaignCode']                = 'Registration_Referee';
			$args['CheckQualificationRulesForReferee']  = true;
		}

		$response = wt_crm_api($args);

		if (is_wp_error($response))
		{
			if ($response->get_error_code() === 'as_50')
				return new WP_Error(409, 'You have an existing membership with us. <a data-open="login-modal">Please login here</a> or contact Customer Service at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>', array('status' => 409));

			return $response;
		}

		return home_url('signup-confirmation');
	},
	'args' => $args
));

unset($args['Email'], $args['DOB']);

register_rest_route('wt/v1', '/crm/customers/(?P<MemberID>\w+)', array(
	'methods' => 'POST',
	'callback' => function($request)
	{
		if (empty($_SESSION['wt_crm']['MemberID']))
			return wt_session_error();


		$params = $request->get_params();


		$required = [
			'DynamicFieldLists' => ['FirstName', 'LastName'],
			'DynamicColumnLists' => ['Gender', 'CountryCode', 'Country']
		];

		foreach ($required as $list => $fields)
		{
			foreach ($fields as $field)
			{
				if (empty($params[$list][$field]))
				{
					return new WP_Error('wt_400', 'Please fill in all required fields.' . $field, array('status' => 400));
				}
			}
		}

        $params['DynamicFieldLists']['FirstName'] = substr($params['DynamicFieldLists']['FirstName'], 0, 40);
        $params['DynamicFieldLists']['LastName'] = substr($params['DynamicFieldLists']['LastName'], 0, 59);
		$params['Name'] = $params['DynamicFieldLists']['FirstName'] . ' ' . $params['DynamicFieldLists']['LastName'];

		$exists = wt_crm_exists('mobile', /*'+' . $params['DynamicColumnLists']['CountryCode'] .*/ $params['MobileNo']);

		if ($exists === true)
			return new WP_Error('wt_409', "Your data is conflicting with another member's. Please contact Customer Service at <a href='mailto:help@wtplus.com.sg'>help@wtplus.com.sg</a>.", array('status' => 409));

		$params['DynamicColumnLists']['CountryCode'] = '+' . ltrim($params['DynamicColumnLists']['CountryCode'], '+');
		$params['MobileNo'] = $params['DynamicColumnLists']['CountryCode'] . $params['MobileNo'];

		if (!wt_is_possible_phone_number($params['MobileNo']))
			return new WP_Error('wt_400', 'Please enter a valid mobile no.', array('status' => 400));


		$current_profile =  wt_crm_api([
			'Command'                    => 'CARD ENQUIRY',
			'CardNo'                     => $_SESSION['wt_crm']['CardNo'],
			'RetrieveActiveVouchersList' => false,
			'RetrieveReceiptMessage'     => false
		]);
		$member = $current_profile['MemberInfo'];

		$new_profile = $params;

		unset($new_profile['children']/*, $new_profile['MailingLists']*/);


		if (/*!empty($new_profile['Email']) ||*/ !empty($new_profile['DOB']))
		{
			if (!is_wp_error($current_profile) && !empty($current_profile['MemberInfo']) && is_array($current_profile['MemberInfo']))
			{
				if ($member['MemberID'] == $_SESSION['wt_crm']['MemberID'])
				{
					/*if (!empty($member['Email']) && function_exists('wt_is_dummy_email') && !wt_is_dummy_email($member['Email']))
					{
						unset($new_profile['Email']);
					}*/

					if (!empty($member['DOB']))
					{
						unset($new_profile['DOB']);
					}
				}
			}
			else
			{
				unset(/*$new_profile['Email'],*/ $new_profile['DOB']);
			}
		}


		// if (!empty($new_profile['Email']))
		if (!empty($member['Email']) && $member['Email'] !== $new_profile['Email'])
		{
			$exists = wt_crm_exists('Email', $new_profile['Email']);

			if ($exists === true)
				return new WP_Error('wt_409', "Your data is conflicting with another member's. Please contact Customer Service at <a href='mailto:help@wtplus.com.sg'>help@wtplus.com.sg</a>.", array('status' => 409));
		}


		if (empty($params['DynamicColumnLists']['NotifyEmail']))
		{
			$l = [];

			for ($i = 0; $i < 4; $i++)
				$l[] = ['Name' => ''];

			$new_profile['MailingLists'] = $l;
		}
		else
		{
			$new_profile['MailingLists'] = [];
			$lists = wt_crm_get_mailing_lists();

			foreach($lists as $list)
				$new_profile['MailingLists'][] = ['Name' => $list];

			unset($new_profile['DynamicColumnLists']['NotifyEmail']);
		}


		$fields = ['NotifySMS', 'NotifyPost'];

		foreach ($fields as $field)
		{
			if (!isset($new_profile['DynamicColumnLists'][$field]))
				$new_profile['DynamicColumnLists'][$field] = 'False';
		}


		$fields = ['NotifyCall', 'WhatsApp'];

		foreach ($fields as $field)
		{
			if (!isset($new_profile['DynamicFieldLists'][$field]))
				$new_profile['DynamicFieldLists'][$field] = 'No';
		}


		wt_format_array($new_profile, ['DynamicFieldLists', 'DynamicColumnLists']);


		/*if (!empty($params['MailingLists']) && is_array($params['MailingLists']))
		{
			foreach ($params['MailingLists'] as $list)
			{
				$new_profile['MailingLists'][] = ['Name' => $list];
			}
		}*/


		$child_index = 1;
		$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);

		if (!empty($params['children']))
		{
			foreach ($params['children'] as $child)
			{
				$suffix = ucfirst($f->format($child_index));

				foreach ($child as $field => $value)
				{
					$new_profile['DynamicFieldLists'][] = [
						'Name'     => 'Child' . sanitize_text_field($field) . $suffix,
						'ColValue' => empty($value) ? '' : sanitize_text_field($value)
					];
				}

				if ($child_index > 5)
					break;

				$child_index++;
			}
		}

		if ($child_index < 6)
		{
			$fields = wt_crm_get_child_fields();

			for ($i = $child_index; $i < 7; $i++)
			{
				$suffix = ucfirst($f->format($i));

				foreach ($fields as $field)
				{
					$new_profile['DynamicFieldLists'][] = [
						'Name'     => 'Child' . sanitize_text_field($field) . $suffix,
						'ColValue' => ''
					];
				}
			}
		}


		$default = [
			"Command"          => "UPDATE PROFILE3",
			'FilterByMemberID' => $_SESSION['wt_crm']['MemberID'],
			'SendUpdateProfileNotification' => true
		];

		$args = array_merge($default, $new_profile);

		$response = wt_crm_api($args);

		if (is_wp_error($response))
			return $response;


		if (!is_wp_error($current_profile) && !empty($current_profile['CardInfo']) && is_array($current_profile['CardInfo']) && $current_profile['CardInfo']['MembershipStatusCode'] !== 'ACTIVE')
		{
			$response = wt_crm_api([
				"Command"              => "UPDATE CARD INFO",
				"CardNo"               => $_SESSION['wt_crm']['CardNo'],
				"PrintedName"          => $current_profile['CardInfo']['PrintedName'],
				"TierCode"             => $current_profile['CardInfo']['TierCode'],
				"MembershipStatusCode" => "ACTIVE",
			]);
		}

		return $response;
	},
	'args' => wp_parse_args([
		'children' => [
			'validate_callback' => function($param, $request, $key)
			{
				return is_array( $param );
			},
		]
	], $args)
));

$passwords= ["OldPassword", 'NewPassword'];
$args = [];

foreach ($passwords as $field)
{
	$args[$field] = array(
		'required' => true,
		'validate_callback' => function($param, $request, $key)
		{
			return !empty( $param );
		},
	);
}

register_rest_route('wt/v1', '/crm/customers/(?P<MemberID>\w+)/password', [
	'methods' => 'POST',
	'callback' => function($request)
	{
		$params = $request->get_body_params();


		if ( $params['NewPassword'] != $params['pass2'] )
			return new WP_Error( 'wt_400', 'Passwords must match.', array('status' => 400) );


		if (!wt_is_user_logged_in())
			return new WP_Error('wt_401', 'Please login first', ['status' => 401]);


		if (function_exists('wt_check_password'))
		{
			$check_password = wt_check_password($params['NewPassword']);

			if (is_wp_error($check_password))
			{
				return $check_password;
			}
		}

		unset($params['pass2']);

		$args = wp_parse_args($params, [
			'UserIDisCardNo' => true,
			"UserID"         => $_SESSION['wt_crm']['CardNo'],
			"Command"        => "CHANGE PASSWORD",
		]);

		$response = wt_crm_api( $args );

		if (is_wp_error($response))
		{
			return $response;
		}

		return true;
	},
	'args' => $args
]);

register_rest_route('wt/v1', '/crm/forgot-password', [
	'methods' => 'POST',
	'callback' => function($request)
	{
		if (function_exists('wt_verify_captcha'))
		{
			$verified = wt_verify_captcha();

			if (is_wp_error($verified))
				return $verified;
		}

		$params = $request->get_params();

		if (empty($params['MobileNo']) && empty($params['Email']))
			return new WP_Error('wt_400', 'Please enter either Email Address or Mobile.');

		$default_args = [
			"Command" => "MEMBER ENQUIRY",
		];
		$args = array_replace_recursive($default_args, $params);

		/*if (!empty($params['MobileNo']) && !empty($params['DynamicColumnLists']['CountryCode']))
		{
			$args['MobileNo'] = '+' . sanitize_text_field($params['DynamicColumnLists']['CountryCode']) . $params['MobileNo'];
		}

		unset($args['DynamicColumnLists']);*/

		$response = wt_crm_api($args);

		if (is_wp_error($response) && $response->get_error_code() === 'as_51' && !empty($params['MobileNo']) && !empty($params['Email']))
		{
			unset($args['MobileNo']);

			$response = wt_crm_api($args);

			if (is_wp_error($response))
			{
				unset($args['Email']);

				$args['MobileNo'] = $params['MobileNo'];

				$response = wt_crm_api($args);
			}
		}

		if (is_wp_error($response))
		{
			if ($response->get_error_code() === 'as_54')
			{
				if (empty($params['MobileNo']) && !empty($params['Email']))
				{
					return new WP_Error('wt_500', 'Invalid email address. Please try again with your mobile number.', ['status' => 500]);
				}
				elseif (!empty($params['MobileNo']) && empty($params['Email']))
				{
					return new WP_Error('wt_500', 'Invalid mobile number. Please try again with your email address.', ['status' => 500]);
				}
			}

			return $response;
		}

		if (!empty($response['MemberLists']) && is_array($response['MemberLists']))
		{
			foreach ($response['MemberLists'] as $member)
			{
				// if (empty($member['NRIC']))
				// 	return new WP_Error('wt_404', 'Please contact Customer Service at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>.');

				if (!empty($params['Email']))
				{
					$password = wt_crm_api([
						'Command'  => 'FORGOT PASSWORD',
						'UniqueID' => $member['MobileNo'],
					// 'UniqueID' => $member['NRIC'],
					]);

					if (is_wp_error($password))
						return $password;

					return $response;
				}

				if (!empty($params['MobileNo']))
				{
					/*$messages = wt_crm_get_messages();

					if (!empty($messages['SMS_ResetPassword(Portal)']))
					{
						$password = wt_crm_api([
							"Command"       => "SEND SMS",
							"MemberID"      => $member['MemberID'],
							"SMSMaskedName" => "WTPlus",
							"SMSMessage"    => $messages['SMS_ResetPassword(Portal)'],
						]);

						if (is_wp_error($password))
							return $password;

						return;
					}*/

					$request_id = time() . $member['MobileNo'];
					$data = "{$member['MemberID']},{$member['MobileNo']}";
					$payload = json_encode([
						'requestid' => $request_id,
						'signature' => hash('sha256', "{$request_id}@{$data}@CGFItsuAjCm060LN"),
						'data' => $data
					]);

					$token = wp_remote_post('http://wtotp.ascentis.com.sg/encryptpayload.ashx', ['body' => $payload]);

					if (is_wp_error($token))
					{
						return $token;
					}

					$token_body = json_decode($token['body']);

					if (!empty($token_body->Token))
					{
						return 'http://wtotp.ascentis.com.sg/confirmotp?token=' . $token_body->Token;
					}
				}
			}
		}

		return $response;
		// if (isset($response['ReturnMessage']))
		// 	return new WP_Error('wt_500', $response['ReturnMessage']);
	},
	'args' => array(
		'MobileNo' => array(
			// 'required' => true,
			'sanitize_callback ' => function($param, $request, $key)
			{
				return sanitize_text_field( $param );
			}
		),
		'Email' => array(
			// 'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return empty($param) || is_email( $param );
			},
			'sanitize_callback ' => function($param, $request, $key)
			{
				return sanitize_email( $param );
			}
		),
	),
]);

register_rest_route('wt/v1', '/crm/vouchers/(?P<VoucherTypeCode>\w+)', [
	'methods' => WP_REST_Server::CREATABLE,
	'callback' => function($request)
	{
		if (empty($_SESSION['wt_crm']['CardNo']))
			return wt_session_error();

        $transaction_lists = wt_has_transactions_today(3);
        $has_purchased = count($transaction_lists) > 0 ? true : false;

        $params = $request->get_params();

		if ((!$has_purchased) && (!wt_has_flash_deal_transactions($transaction_lists, $params['VoucherTypeCode'])))
			return new WP_Error('wt_400', 'You have reached your daily redemption limit, each member is limited to 1 redemption per day.');

 		$issue = wt_crm_api([
			"Command"          => "VOUCHER ISSUANCE WITH POINTS",
			"CardNo"           => $_SESSION['wt_crm']['CardNo'],
			'PointsUsage'      => 'COMBINE',
			'RetrieveIssuedVouchersList' => true,
			'VoucherTypeLists' => [
				[
					'VoucherTypeCode' => $params['VoucherTypeCode'],
					'VoucherQty'      => 1
				]
			],
            'Ref7' => $params['VoucherTypeCode']
		]);

		if (is_wp_error($issue))
			return $issue;

		if (!isset($issue['TransactionReferenceInfo']['MovementLists'][0]['PointsRebateValue']))
			return wt_generic_error();

		$member = wt_crm_api([
			"Command"  => "MEMBER ENQUIRY",
			'MemberID' => $_SESSION['wt_crm']['MemberID'],
		]);

		if (is_wp_error($member))
			return $member;

		if (empty($member['MemberLists'][0]['Email']))
			return wt_generic_error();

		$email = wt_crm_api([
			"Command"           => "SEND EMAIL",
			"EmailTemplateName" => str_replace('ProductRef1|-|', '', $params['EmailTemplate']),
			'MemberID'          => $_SESSION['wt_crm']['MemberID'],
			'Email'             => $member['MemberLists'][0]['Email'],
			'CPH1'              => abs($issue['TransactionReferenceInfo']['MovementLists'][0]['PointsRebateValue']),
			'CPH2'              => current_time('d F Y'),
			'CPH3'				=> isset($issue['IssuedVoucherLists'][0]['VoucherTypeName']) ? $issue['IssuedVoucherLists'][0]['VoucherTypeName'] : null,
			'CPH4'				=> isset($issue['IssuedVoucherLists'][0]['TypeValue']) ? $issue['IssuedVoucherLists'][0]['TypeValue'] : null
		]);

		if (is_wp_error($email)) {
            return $email;
        }

		return $member;
	},
	/*'args' => array(
		'VoucherQty' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty($param) && is_numeric( $param );
			},
			'sanitize_callback' => function($param, $request, $key)
			{
				return absint($param);
			},
		),
	),*/
]);

register_rest_route('wt/v1', '/crm/vouchers/(?P<VoucherTypeCode>\w+)/send', [
	'methods' => WP_REST_Server::CREATABLE,
	'callback' => function($request)
	{
		if (empty($_SESSION['wt_crm']['CardNo']))
			return wt_session_error();

		if (wt_has_transactions_today(20))
			return new WP_Error('wt_400', 'You have reached your daily redemption limit, each member is limited to 1 redemption per day.');

		$params = $request->get_params();
		$gift_voucher_type_code = preg_replace('/^RED/', 'GIFT', $params['VoucherTypeCode']);

		$args = [
			"Command"                    => "REWARD CAMPAIGN",
			"CampaignType"               => "Gifting Campaign",
			'CampaignCode'               => $gift_voucher_type_code,
			"CardNo"                     => '9999999',
			'Remarks'                    => "RecipientName:{$params['RecipientName']}|Recipient:{$params['Recipient']}|SenderMemberID:{$_SESSION['wt_crm']['MemberID']}",
			'RetrieveActiveVouchersList' => true,
			'RetrieveIssuedVoucherLists' => true,
			'SortBy_VoucherNo'           => true,
			'SortOrder'                  => 'DESC',
			"CheckQualificationRules"    => true,
		];
		wt_log(json_encode($args), 'crm');
		$issue = wt_crm_api($args);

		if (is_wp_error($issue))
			return $issue;

		wt_log(json_encode($issue), 'crm');
		
		if (empty($issue['IssuedVoucherLists'][0]['VoucherNo']))
			return new WP_Error('wt_500', 'Empty VoucherNo');

		$vouchers = [];

		foreach ($issue['IssuedVoucherLists'] as $voucher)
		{
			$vouchers[$voucher['VoucherIssuedOn']] = $voucher;
		}

		krsort($vouchers);

		$voucher = end($vouchers);

		/*foreach ($vouchers as $voucher)
		{
			if (empty($voucher['VouRemarks']))
				continue;

			$remarks = wt_get_voucher_remarks($voucher['VouRemarks']);

			if (!$remarks || empty($remarks['SenderMemberID']) || $remarks['SenderMemberID'] != $_SESSION['wt_crm']['MemberID'])
				continue;
		}*/

		$redeem = wt_crm_api([
			"Command"             => "ITEM REDEMPTION",
			"CardNo"              => $_SESSION['wt_crm']['CardNo'],
			'PointsUsage'         => 'COMBINE',
			'RedemptionItemLists' => [
				[
					'RedemptionCatalogCode' => 'RewardsBoutique',
					'RedemptionItemCode'    => $params['VoucherTypeCode'],
					'RedemptionItemQty'     => 1,
				]
			],
			'Ref1'                => $params['Recipient'],
			'Ref2'                => $_SESSION['wt_crm']['MemberID'],
			'Ref3'                => $voucher['VoucherNo'],
			'Ref4'                => $voucher['TypeValue'],
		]);

		if (is_wp_error($redeem))
			return $redeem;

		$member = wt_crm_api([
			"Command"  => "MEMBER ENQUIRY",
			'MemberID' => $_SESSION['wt_crm']['MemberID'],
		]);
		$sender_name = is_wp_error($member) || empty($member['MemberLists'][0]['Name']) ? '' : $member['MemberLists'][0]['Name'];

		$email = wt_crm_api([
			"Command"           => "SEND EMAIL",
			"EmailTemplateName" => "WTPLUS YOUR GIFT AWAITS",
			'MemberID'          => $_SESSION['wt_crm']['MemberID'],
			'Email'             => $params['Recipient'],
			'CPH1'              => $sender_name,
			'CPH2'              => $voucher['VoucherTypeName'],
			'CPH3'              => $voucher['TypeValue'],
		]);

		if (is_wp_error($email))
			return $email;
	},
	'args' => array(
		'Recipient' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty($param) && is_email( $param );
			},
			'sanitize_callback' => function($param, $request, $key)
			{
				return sanitize_email($param);
			},
		),
		'RecipientName' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty($param);
			},
			'sanitize_callback' => function($param, $request, $key)
			{
				return sanitize_text_field($param);
			},
		),
	),
]);

register_rest_route('wt/v1', '/crm/voucher/receive', [
	'methods' => WP_REST_Server::CREATABLE,
	'callback' => function($request)
	{
		if (empty($_SESSION['wt_crm']['CardNo']))
			return wt_session_error();

		$params = $request->get_params();

		$voucher = wt_crm_api([
			"Command"                  => "GET VOUCHERS",
			"CardNo"                   => '9999999',
			'FilterBy_TypeValue'       => $params['VoucherNo'],
//			'FilterBy_VoucherNo'     => $params['VoucherNo'],
			'RetrieveActiveVouchers'   => true,
			'RetrieveRedeemedVouchers' => true,
			'RetrieveExpiredVouchers'  => true,
//			'RetrieveVoidedVouchers' => true,
//			'RetrieveDelayVouchers"' => true,
		]);

		if (is_wp_error($voucher))
			return $voucher;

		if (empty($voucher['ActiveVoucherLists']) || !is_array($voucher['ActiveVoucherLists']))
		{
			$statuses = ['redeemed', 'expired'];

			foreach ($statuses as $status)
			{
				$list = ucfirst($status) . 'VoucherLists';

				if (!empty($voucher[$list]))
					return new WP_Error('wt_404', 'Voucher ' . $status);
			}

			return new WP_Error('wt_404', 'Voucher not found');
		}


		$member = wt_crm_api([
			"Command"  => "MEMBER ENQUIRY",
			'MemberID' => $_SESSION['wt_crm']['MemberID'],
		]);

		if (is_wp_error($member))
			return $member;

		if (empty($member['MemberLists'][0]['Email']) || empty($member['MemberLists'][0]['Name']))
			return wt_generic_error();

		foreach ($voucher['ActiveVoucherLists'] as $voucher)
		{
			if (empty($voucher['VouRemarks']))
				continue;

			$a1 = explode('|', $voucher['VouRemarks']);

			if (!$a1)
				continue;

			$remarks = [];

			foreach ($a1 as $s)
			{
				$a2 = explode(':', $s);

				if (!$a2 || count($a2) < 2)
				{
					continue;
				}

				$remarks[$a2[0]] = $a2[1];
			}

//			if (empty($remarks['Recipient']) || empty($remarks['RecipientName']) || empty($remarks['SenderMemberID']) || $remarks['Recipient'] !== $member['MemberLists'][0]['Email'])
			if (empty($remarks['SenderMemberID']))
				continue;

//			$remarks = "RecipientName:{$remarks['RecipientName']}|Recipient:{$remarks['Recipient']}|SenderMemberID:{$remarks['SenderMemberID']}";
			$remarks = "RecipientName:{$member['MemberLists'][0]['Name']}|Recipient:{$member['MemberLists'][0]['Email']}|SenderMemberID:{$remarks['SenderMemberID']}";

			$redeem = wt_crm_api([
				"Command"                => "VOUCHER REDEMPTION",
				"CardNo"                 => '9999999',
				'Remarks'                => $remarks,
				'RedemptionVoucherLists' => [
					[
						'VoucherNo' => $voucher['VoucherNo'],
					]
				],
			]);

			if (is_wp_error($redeem))
				return $redeem;

			$actual_voucher_type_code = preg_replace('/^GIFT/', 'RED', strtoupper($voucher['VoucherTypeCode']));

			$issue = wt_crm_api([
				"Command"                    => "VOUCHER ISSUANCE",
				"CardNo"                     => $_SESSION['wt_crm']['CardNo'],
				'RetrieveActiveVouchersList' => false,
				'FilterBy_VoucherType'       => $actual_voucher_type_code,
				'VoucherTypeLists'           => [
					[
						'VoucherTypeCode' => $actual_voucher_type_code,
						'VoucherQty'      => 1,
						'Remarks'         => $remarks
					]
				]
			]);

			if (is_wp_error($issue))
				return $issue;

			return;
		}

		return new WP_Error('wt_400', 'Invalid voucher code');
	},
	'args' => array(
		'VoucherNo' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty($param);
			},
			'sanitize_callback' => function($param, $request, $key)
			{
				return sanitize_text_field($param);
			},
		),
	),
]);

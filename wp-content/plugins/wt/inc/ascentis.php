<?php
defined('ABSPATH') || exit;



function wt_is_user_logged_in()
{
//		wt_dump($_SESSION['wt_crm']);
//	if (!empty($_SESSION['wt_crm']->TokenExpiryDate))
//	{
//		if (strtotime($_SESSION['wt_crm']->TokenExpiryDate) > time())
//			return true;
//	}
//	wt_log($_SESSION, 'crm');
	if (empty($_SESSION['wt_crm']['CardNo']) || empty($_SESSION['wt_crm']['MemberID']))
		return false;
	
	return sanitize_text_field($_SESSION['wt_crm']['CardNo']);
}



function wt_login_redirect()
{
	if (empty($_SESSION['wt_crm']['MembershipStatusCode']))
		return home_url();
	
//	if ($_SESSION['wt_crm']['MembershipStatusCode'] === 'ACTIVE')
//		return home_url('my-account/profile/#password');
	
	if ($_SESSION['wt_crm']['MembershipStatusCode'] === 'PENDING PROFILE UPDATE')
		return home_url('my-account/profile/');
	
	return home_url('my-account/');
}



add_filter('acf/update_value/type=password', function($value, $post_id, $field)
{
	$prefix = '||';
	
	if (empty($field['wrapper']['class']) || strpos($field['wrapper']['class'], 'wt-encrypt') === false || substr($value, 0, 2) === $prefix)
		return $value;
	
	return $prefix . base64_encode($value);
}, 10, 3);

function wt_descrypt($str)
{
	return base64_decode(substr($str, 2));
}

function wt_crm_encrypt($str)
{
	return openssl_encrypt($str, 'AES-256-CBC', 'abcd1234abcd1234abcd1234abcd1234', 0, 'abcd1234abcd1234');
}

function wt_crm_decrypt($str)
{
	return openssl_decrypt($str, 'AES-256-CBC', 'abcd1234abcd1234abcd1234abcd1234', 0, 'abcd1234abcd1234');
}

function wt_crm_get_service($command)
{
	return 'APIsService';
	/*if (empty($_SESSION['wt_crm']['token']))
		return 'APIsService';
//		return in_array($command, ['MEMBER ENQUIRY']) ? 'APIsCommandServiceRest' : 'APIsService';
	else
		return $command === 'MEMBER ENQUIRY' ? 'APIsService' : 'APIsCommandServiceRest';*/
}

function wt_crm_get_url($command)
{
	return get_option('options_wt_ascentis_url') . wt_crm_get_service($command) . '.svc/';
}

function wt_crm_get_token($command, $rerequest = false)
{
	$service = wt_crm_get_service($command);
	
	if ($rerequest)
	{
		if (!empty($_SESSION['wt_crm']))
		{
			unset($_SESSION['wt_crm']);
		}
	}
	else
	{
		if (!empty($_SESSION['wt_crm']['token']))
			return $_SESSION['wt_crm']['token'];
		
		$token = get_transient('wt_crm_token_' . $service);
		
		if ($token)
			return $token;
	}
	
	$url = wt_crm_get_url($command) . 'Authenticate';
	$args = [
		'cusCode' => get_option('options_wt_ascentis_cus_code'),
	];
	$username = base64_encode(wt_descrypt(get_option('options_wt_ascentis_username')));
	$password = base64_encode(wt_descrypt(get_option('options_wt_ascentis_password')));
	
	if ($service === 'APIsCommandServiceRest')
	{
		$args = array_merge($args, [
			'username'  => $username,
			'password'  => $password,
			'reqSource' => ''
		]);
	}
	else
	{
		$args = array_merge($args, [
			'apiUsername' => wt_crm_encrypt($username),
			'apiPassword' => wt_crm_encrypt($password),
			'reqSource'   => 'Cleargo'
		]);
	}
	
	$args = [
		'body' => json_encode($args, JSON_UNESCAPED_SLASHES)
	];
	wt_log($url , 'crm');
//	wt_log($args, 'crm');// TODO comment
	$response = wp_remote_post( $url, $args );
	
	if (is_wp_error($response))
	{
		wt_log($response , 'crm');
		return $response;
	}
	
	if (isset($response['body']))
	{
		$token = trim($response['body'], '"');
//		wt_log($token, 'crm');// TODO comment
		
		if ($token !== 'Unauthorized')
		{
//			if ($endpoint === 'Authenticate')
			set_transient('wt_crm_token_' . $service, $token, 900);
//			else
//				$_SESSION['wt_crm']['token'] = $token;
			
			return $token;
		}
	}

//	wt_log($response, 'crm');
	
	if (!$rerequest)
		return wt_crm_get_token($command, true);
}

function wt_crm_api($request_cmd, $rerequest = false)
{
	$command = isset($request_cmd['Command']) ? $request_cmd['Command'] : '';
	$token = wt_crm_get_token($command, $rerequest);
	
	if (is_wp_error($token))
		return $token;
	
	if (!$token)
		return new WP_Error('wt_500', 'Error occurred. Please try again.');
	
	$url = wt_crm_get_url($command) . 'RequestJson';
	$service = wt_crm_get_service($command);
	$data = wp_parse_args($request_cmd, [
		"DB"          => get_option('options_wt_ascentis_cus_code'),
		"EnquiryCode" => "PORTAL",
		"OutletCode"  => "PORTAL",
		"PosID"       => "POSID",
		"CashierID"   => "CASHIERID"
	]);
	
	wt_log($url . ' ' . $command, 'crm');
	
	if ($service === 'APIsService')
	{
		if (empty($data['MemberID']) && !empty($_SESSION['wt_crm']['MemberID']))
			$data['MemberID'] = $_SESSION['wt_crm']['MemberID'];
		
		$data = json_encode($data);
        // wt_log($data, 'crm'); // TODO comment
		$data = openssl_encrypt($data, 'AES-256-CBC', 'abcd1234abcd1234abcd1234abcd1234', 0, 'abcd1234abcd1234');//OPENSSL_RAW_DATA
	}
	
	$args = [
		'body' => json_encode([
			'token' => $token,
			'requestCmd' => $data
		], JSON_HEX_AMP | JSON_UNESCAPED_SLASHES)
	];

    // wt_log($args, 'crm'); // TODO comment
	$response = wp_remote_post($url, $args);

	if (is_wp_error($response))
	{
		wt_log($response, 'crm');
		return $response;
	}
	
	if (!isset($response['body']))
	{
		wt_log($response, 'crm');
		return wt_generic_error();
	}
	
	if ($response['body'] === 'Unauthorized' && !$rerequest)
	{
		wt_log($response['body'], 'crm');
		return wt_crm_api($request_cmd, true);
	}
	
	if (in_array($response['body'], ['Session Timeout!', 'Multiple Logins!']))
	{
		wt_log($response['body'], 'crm');
		
		unset($_SESSION['wt_crm']);
		
		$message = $response['body'];
		
		if ($message === 'Multiple Logins!')
			$message = '<p>Maximum concurrent sessions exceeded. Please login again to resume your session.</p><p>Need help? Please contact <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a></p>';
		else if ($message === 'Session Timeout!')
			$message = 'Session timeout. Please login again.';
		
		wp_redirect(add_query_arg('alert', rawurlencode($message), home_url('login')));
		exit;
	}
	
	if ($service === 'APIsService')
	{
		$decrypted = wt_crm_decrypt($response['body']);
		
		if (!$decrypted)
		{
			wt_log($response['body'], 'crm');
			return wt_generic_error();
		}
		
		$response['body'] = $decrypted;
	}
	
	$body = json_decode($response['body'], true);
	
	if ($body)
	{
		// wt_log($response['body'], 'crm'); // TODO comment

		if (!isset($body['ReturnStatus']))
			return wt_generic_error();
		
		if ($body['ReturnStatus'] != 1 && $body['ReturnStatus'] != 242)
			return wt_crm_error($body);
		
		return $body;
	}

	wt_log($response['body'], 'crm');
	
	return new WP_Error('wt_500', 'Error occurred. Please try again.');
}



function wt_generic_error()
{
	return new WP_Error('wt_500', 'Error occurred. Please try again');
}

function wt_crm_error($response)
{
	if (empty($response['ReturnMessage']))
		return wt_generic_error();
	
	wt_log($response['ReturnMessage'], 'crm');
	
	return new WP_Error('as_' . (int) $response['ReturnStatus'], sanitize_text_field($response['ReturnMessage']));
}

function wt_session_error()
{
	return new WP_Error('wt_403', 'Session timeout. Please login and try again.', ['status' => 403]);
}



function wt_crm_get_card($response)
{
	if (empty($response['CardLists']) || !is_array($response['CardLists']))
		return [];
	
	foreach ($response['CardLists'] as $card)
	{
		if (in_array($card['MembershipStatusCode'], ['ACTIVE', 'PENDING PROFILE UPDATE']))
		{
			return $card;
		}
	}
	
	return $response['CardLists'][0];
}

function wt_crm_get_member($response)
{
	if (empty($response['MemberLists']) || !is_array($response['MemberLists']))
		return [];
	
	$card = wt_crm_get_card($response);
	
	if (!$card)
		return [];
	
	foreach ($response['MemberLists'] as $member)
	{
		if ($member['MemberID'] == $card['MemberID'])
		{
			return $member;
		}
	}
	
	return [];
}



function wt_crm_get_child_fields()
{
	return ['First', 'Last', 'DOB'];
}



function wt_crm_get_mailing_lists()
{
	return ['wt+'];
//	return ['Promotion', 'Events', 'Statement', 'wt+'];
}



function wt_crm_exists($field, $value, $check_for_friend = false)
{
	$response = wt_crm_api([
		'FieldName'   => $field,
		'FieldValue'  => $value,
		'Command'     => "UNIQUE FIELD VALUE VALIDATION",
	]);
	
	if (is_wp_error($response))
		return $response;
	
	if (!isset($response['IsValid']))
		return wt_crm_error($response);
	
	$exists = ! (bool) $response['IsValid'];
	
	if ($check_for_friend || !wt_is_user_logged_in() || !$exists)
		return $exists;
	
	if ($field === 'mobile')
		$field = 'MobileNo';
	
	$member = wt_crm_search([$field => $value]);
	
	if (is_wp_error($member))
	{
		$error_code = $member->get_error_code();
		
		if ($error_code === 'as_51')
			return false;
		
		if ($error_code === 'as_54')
			return true;
		
		return $member;
	}
	
	if (empty($member['MemberLists'][0]['MemberID']))
		return false;
	
	return ! $member['MemberLists'][0]['MemberID'] == $_SESSION['wt_crm']['MemberID'];
}



function wt_crm_search($args)
{
	if (empty($args))
		return;
	
	$args = wp_parse_args($args, ['Command' => 'MEMBER ENQUIRY']);
	$response = wt_crm_api($args);
	
	if (is_wp_error($response))
		return $response;
	
	if ($response['ReturnStatus'] != 1 || $response['ReturnMessage'] !== 'SUCCESS')
		return wt_crm_error($response);
	
	return $response;
}



function wt_crm_set_session($card, $response, $token = '')
{
	$_SESSION['wt_crm']['CardNo'] = $card['CardNo'];
	$_SESSION['wt_crm']['MembershipStatusCode'] = $card['MembershipStatusCode'];
	$_SESSION['wt_crm']['token'] = $token;
	
	if (!empty($response['MemberLists']) && is_array($response['MemberLists']))
	{
		foreach ($response['MemberLists'] as $member)
		{
			if ($member['MemberID'] == $card['MemberID'])
			{
				$_SESSION['wt_crm']['MemberID'] = $member['MemberID'];
				
				return;
			}
		}
	}
}







/*function wt_crm_email_verified($customer_number)
{
	$args = [
		'method' => 'PUT',
		'body' => [
			"Name" => "EmailVerification",
			"Value" => 'YES',
			"DisplayText" => 'Email Verification',
			"IsMandatory" => false
		]
	];
//	wt_log(json_encode($args), 'crm');
	return wt_crm_api('profile/' . $customer_number . '/custom-property/EmailVerification', $args);
}*/
//function wt_crm_set_social_profile($fb_user, $customer_number)
//{
////	$fields = [
////		'firstName' => 'first_name',
////		'lastName' => 'last_name',
////		'email' => 'email',
////		'gender' => 'gender',
////	];
////
////	foreach ($fields as $crm_key => $fb_key)
////		$data[$crm_key] = $fb_user[$fb_key];
////
////	$response = wt_crm_api('social/facebook/profile/?identifier=http://www.facebook.com/' . $fb_user['id'], [
////		'method' => 'PUT',
////		'body' => [
////			'mediaCode' => 'facebook',
////			'identifier' => "http://www.facebook.com/profile.php?id=" . $fb_user['id'],
////			"customerNumber" => $customer_number,
////			'data' => $data
////		]
////	]);
////
////	wt_dump($response);
////
////	if (is_wp_error($response))
////		return $response;
////	wt_dump($customer_number);
////	wt_dump($fb_user['id']);
//	$response = wt_crm_api('profile/' . $customer_number . '/social-profile/create', [
//		'body' => [
//			'MediaCode' => 'facebook',
//			'Identifier' => "www.facebook.com/" . $fb_user['id'],
//		]
//	]);
//
//	wt_dump($response);
//}

/*function wt_crm_api($properties, $command = '')
{
	$xml = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>
    <SOAPAuthHeader xmlns="http://MatrixAPIs/">' . get_option('options_wt_ascentis_header_properties') . '</SOAPAuthHeader>
  </soap:Header>
  <soap:Body>
    <JSONCommand xmlns="http://MatrixAPIs/">
      <requestJSON>{' . rtrim($properties, ',') . ', ' . rtrim(get_option('options_wt_ascentis_body_properties'), ',') . '}</requestJSON>
    </JSONCommand>
  </soap:Body>
</soap:Envelope>';
	
	$args = [
		'headers' => [
//			'Content-Length' => strlen($xml),
			'Content-Type' => 'text/xml; charset=utf-8',
			'SOAPAction'   => 'http://MatrixAPIs/JSONCommand'
		],
		'body'    => $xml
	];
	
	$response = wp_remote_post(get_option('options_wt_ascentis_url'), $args);
	
	if ($command)
		wt_log($command, 'crm');
	
	if (is_wp_error($response))
		wt_log($response, 'crm');
	
	if (!isset($response['body']))
	{
		wt_log($response, 'crm');
		return new WP_Error('wt', 'Error occurred.');
	}
	
	$xml = simplexml_load_string($response['body']);
	
	if ($xml === false)
	{
		wt_log('simplexml_load_string() returns false', 'crm');
		wt_log($response['body'], 'crm');
		return new WP_Error('wt', 'Error occurred.');
	}
	
	$xml->registerXPathNamespace('m', 'http://MatrixAPIs/');
	$array = $xml->xpath('//m:JSONCommandResult');
	
	if (empty($array[0]))
	{
		wt_log('Empty registerXPathNamespace()', 'crm');
		wt_log($response['body'], 'crm');
		return new WP_Error('wt', 'Error occurred.');
	}
	
	$array = json_decode($array[0], true);
	
	if (empty($array))
	{
		wt_log('Empty json_decode', 'crm');
		wt_log($response['body'], 'crm');
		return new WP_Error('wt', 'Error occurred.');
	}
	
	wt_log($array, 'crm');
	
	return $array;
}*/

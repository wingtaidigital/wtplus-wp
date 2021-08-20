<?php
function wt_is_user_logged_in()
{
    // 	wt_dump($_SESSION['wt_crm']);
    // if (!empty($_SESSION['wt_crm']->TokenExpiryDate))
    // {
    // 	if (strtotime($_SESSION['wt_crm']->TokenExpiryDate) > time())
    // 		return true;
    // }

    if (empty($_SESSION['wt_crm']['CustomerNumber']))
        return false;

    return sanitize_text_field($_SESSION['wt_crm']['CustomerNumber']);
}

function wt_crm_get_token($rerequest = false)
{
    if (!$rerequest) {
        $token = get_option('options_wt_crm_token');

        if ($token)
            return $token;
    }

    $url      = get_option('options_wt_crm_api_url');
    $args     = array(
        'headers' => array(
            'SvcAuth' => get_option('options_wt_crm_svcauth'),
        ),
        'body' => array(
            'UserName' => get_option('options_wt_crm_username'),
            'Password' => get_option('options_wt_crm_password'),
        )
    );
    $response = wp_remote_post($url . 'user/authenticate', $args);
    // wt_log($response, 'crm');
    if (is_array($response)) {
        $token = sanitize_text_field(trim($response['body'], '"'));

        update_option('options_wt_crm_token', $token);

        return $token;
    }

    // wt_log($response, 'crm');

    if (!$rerequest)
        return wt_crm_get_token(true);

    return $response;
}

function wt_crm_get_headers($rerequest = false)
{
    $token = wt_crm_get_token($rerequest = false);

    if (is_wp_error($token))
        return;

    $headers = array(
        'Content-Type' => 'application/json',
        'Cache-Control' => 'no-cache',
        'SvcAuth' => get_option('options_wt_crm_svcauth'),
        'Token' => $token,
    );

    if (wt_is_user_logged_in()) {
        $headers['ProfileToken'] = $_SESSION['wt_crm']['Token'];
    }
    // wt_log($headers, 'crm');
    return $headers;
}

function wt_crm_api($route, $args = [], $rerequest = false)
{
    $url  = get_option('options_wt_crm_api_url') . $route;
    $args = array_replace_recursive(['headers' => wt_crm_get_headers($rerequest)], $args);

    if (!empty($args['body']))
        $args['body'] = json_encode($args['body']);

    $response = wp_remote_post($url, $args);

    wt_log((empty($args['method']) ? 'POST' : $args['method']) . ' ' . $url, 'crm');
    // wt_log($args, 'crm');

    if (is_array($response)) {
        // wt_log($response, 'crm');
        $body = json_decode($response['body'], true);

        if (substr($response['response']['code'], 0, 1) == 2) {
            // wt_log($response['body'], 'crm');

            if ($body)
                return $body;

            return $response['body'];
        }

        if ($response['response']['code'] == 504) {
            wt_log($response, 'crm');

            return new WP_Error('wt_timeout', 'Gateway timeout. Please try again.', ['status' => $response['response']['code']]);
        }

        wt_log($response['body'], 'crm');

        if (!empty($body['ErrorCode'])) {
            switch ($body['ErrorCode']) {
                case 2:
                    if (!$rerequest)
                        wt_crm_api($route, $args, true);

                    break;

                case 9:
                    unset($_SESSION['wt_crm']);

                    wt_redirect(add_query_arg('timeout', '', home_url('login')));

                    break;

                case 1001:
                    if (strpos($route, 'signin') !== false)
                        return new WP_Error('wt_crm_error', 'Login incorrect. Kindly re-enter the fields or please contact Customer Service at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>.', array('status' => $response['response']['code']));

                    break;

                case 1002:
                case 1004:
                    return new WP_Error('wt_crm_error', 'Please <a href="' . home_url('forgot-password') . '">reset your password.</a>', array('status' => $response['response']['code']));

                    break;

                case 9000:
                    return new WP_Error('wt_crm_error', 'Error occurred. Please try again or contact Customer Service at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>.', array('status' => $response['response']['code']));

                    break;

            }
        }

        $message = $body ? $body['Message'] : $response['response']['message'];

        // wt_log(json_encode($args),'crm');
        // wt_log($response['body'], 'crm');
        // wt_log($response['response'], 'crm');

        return new WP_Error('wt_crm_error', $message, array('status' => $response['response']['code']));

        // if (isset($body->Message))
        // {
        // 	wt_log($body, 'crm');
        //
        // 	return new WP_Error('wt_crm_error', $body->Message, array('status' => $response['response']['code']));
        // }
        // else
        // {
        // 	wt_log($response, 'crm');
        // 	return new WP_Error('wt_crm_error', 'Error occurred. Please inform admin or try again.');
        // }
    }

    // wt_log(json_encode($args),'crm');
    wt_log($response, 'crm');

    if ($response->get_error_code() == 'http_request_failed')
        return new WP_Error('http_request_failed', 'Server timed out. Please try again.');

    return $response;
}

function wt_crm_search($search, $exclude = '', $single = true)
{
    if (empty($search))
        return;

    if (is_email($search))
        $args['body']['EmailAddress'] = $search;
    // else if (is_numeric($search))
    // 	$args['body']['Mobile'] = $search;
    else
        $args['body']['NRIC'] = $search;

    $response = wt_crm_api('profile/search-simple', $args);
    // wt_log($response, 'crm');
    if (is_wp_error($response) || !isset($response[0]['CustomerNumber'])) {
        // wt_log($response, 'crm');

        return;
    }

    if ($exclude) {
        foreach ($response as $i => $customer) {
            if ($customer['CustomerNumber'] == $exclude) {
                unset($response[$i]);
                break;
            }
        }
    }

    if (!$response)
        return;

    if ($single)
        return $response[0]['CustomerNumber'];

    return $response;
}

function wt_crm_set_session($response)
{
    $_SESSION['wt_crm'] = [
        'CustomerNumber' => $response['CustomerNumber'],
        'Token' => $response['Token'],
        'ExpiryDate' => empty($response['ExpiryDate']) ? $response['TokenExpiryDate'] : $response['ExpiryDate'],
    ];
    // wt_log($_SESSION);
}

function wt_crm_email_verified($customer_number)
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
    // wt_log(json_encode($args), 'crm');
    return wt_crm_api('profile/' . $customer_number . '/custom-property/EmailVerification', $args);
}

// function wt_crm_set_social_profile($fb_user, $customer_number)
// {
// //	$fields = [
// //		'firstName' => 'first_name',
// //		'lastName' => 'last_name',
// //		'email' => 'email',
// //		'gender' => 'gender',
// //	];
// //
// //	foreach ($fields as $crm_key => $fb_key)
// //		$data[$crm_key] = $fb_user[$fb_key];
// //
// //	$response = wt_crm_api('social/facebook/profile/?identifier=http://www.facebook.com/' . $fb_user['id'], [
// //		'method' => 'PUT',
// //		'body' => [
// //			'mediaCode' => 'facebook',
// //			'identifier' => "http://www.facebook.com/profile.php?id=" . $fb_user['id'],
// //			"customerNumber" => $customer_number,
// //			'data' => $data
// //		]
// //	]);
// //
// //	wt_dump($response);
// //
// //	if (is_wp_error($response))
// //		return $response;
// //	wt_dump($customer_number);
// //	wt_dump($fb_user['id']);
// 	$response = wt_crm_api('profile/' . $customer_number . '/social-profile/create', [
// 		'body' => [
// 			'MediaCode' => 'facebook',
// 			'Identifier' => "www.facebook.com/" . $fb_user['id'],
// 		]
// 	]);
//
// 	wt_dump($response);
// }

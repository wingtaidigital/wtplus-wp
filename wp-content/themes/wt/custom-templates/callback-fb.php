<?php
defined('ABSPATH') || exit;



$fb = wt_get_fb();
$helper = $fb->getRedirectLoginHelper();
$url = add_query_arg('alert', rawurlencode('Error logging in to Facebook. Please try again.'), home_url('login'));

if (!empty($_GET['state']))
	$_SESSION['FBRLH_state'] = $_GET['state'];

try
{
//	$u = 'http://wingtai.cleargo.sg/revamp/?fb';
//	wt_dump($u);
	$accessToken = $helper->getAccessToken();
}
catch(Facebook\Exceptions\FacebookResponseException $e)
{
//	wt_dump($e);
	// When Graph returns an error
	wt_log('Graph returned an error: ' . $e->getMessage(), 'fb');
	wp_redirect($url);
	exit;
}
catch(Facebook\Exceptions\FacebookSDKException $e)
{
	// When validation fails or other local issues
	wt_log('Facebook SDK returned an error: ' . $e->getMessage(), 'fb');
	wp_redirect($url);
	exit;
}

if (! isset($accessToken))
{
	if ($helper->getError())
	{
		wt_log('Unauthorized', 'fb');
		wt_log("Error: " . $helper->getError(), 'fb');
		wt_log("Error Code: " . $helper->getErrorCode(), 'fb');
		wt_log("Error Reason: " . $helper->getErrorReason(), 'fb');
		wt_log("Error Description: " . $helper->getErrorDescription(), 'fb');
	}
	else
	{
		wt_log('Bad request', 'fb');
	}
	
	wp_redirect($url);
	exit;
}

$fb_token = $accessToken->getValue();
$response = wt_crm_api([
	'FacebookAccessToken' => $fb_token,
	'Command' => 'WINGTAI FACEBOOK LOGIN',
	'reqSource' => 'ClearGo'
]);

if (is_wp_error($response))
{
	wp_redirect(add_query_arg([
		'rerequest' => '',
		'alert' => rawurlencode($response->get_error_message())
	], home_url('login')));
	exit;
}

if (!empty($response['MemberToken']))
{
	if (in_array($response['MembershipCardLists'][0]['MembershipStatusCode'], ['ACTIVE', 'PENDING PROFILE UPDATE']))
	{
		wt_crm_set_session($response['MembershipCardLists'][0], ['MemberLists' => [$response['MemberInfo']]], $response['MemberToken']);
		wp_redirect(wt_login_redirect());
	}
	else
	{
		wp_redirect(add_query_arg([
			'rerequest' => '',
			'alert' => rawurlencode('Membership status is ' . $response['MembershipCardLists'][0]['MembershipStatusCode'])
		], home_url('login')));
	}
	
	exit;
}

$_SESSION['wt_fb'] = [
	'access_token' => $fb_token,
];

wp_redirect(home_url('signup'));

exit;

$fb_user = wt_get_fb_user('id,first_name,last_name,email,gender', $accessToken);

if (empty($fb_user))
{
	wp_redirect($url);
	exit;
}

if (empty($fb_user['email']))
{
	wp_redirect(add_query_arg([
		'rerequest' => '',
		'alert' => rawurlencode('Please share your Facebook email with us.')
	], home_url('login')));
	exit;
}

$_SESSION['wt_fb'] = [
	'access_token' => (string) $accessToken,
//	'user_id' => $fb_user['id']
];

$customer = wt_crm_search(['Email' => $fb_user['email']]);

if (is_wp_error($customer) || empty($customer['CardLists']) || !is_array($customer['CardLists']))
{
	wp_redirect(home_url('signup'));
	exit;
}

foreach ($customer['CardLists'] as $card)
{
	if (in_array($card['MembershipStatusCode'], ['ACTIVE', 'PENDING PROFILE UPDATE']))
	{
		wt_crm_set_session($card, $customer);
		wp_redirect(wt_login_redirect());
		
		return;
	}
}

wp_redirect(home_url('signup'));


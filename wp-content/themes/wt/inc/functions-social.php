<?php
function wt_get_fb()
{
	require_once get_template_directory() . '/vendor/autoload.php';

	return new Facebook\Facebook([
		'app_id' => get_option('options_wt_fb_app_id'),
		'app_secret' => get_option('options_wt_fb_app_secret'),
//		'default_graph_version' => 'v2.10',
//		'persistent_data_handler'=> 'session'
	]);
}

function wt_get_fb_user($fields = 'id,first_name,last_name,email', $access_token = '')
{
	if (empty($access_token))
	{
		if (empty($_SESSION['wt_fb']['access_token']))
			return;
		
		$access_token = $_SESSION['wt_fb']['access_token'];
	}
	
	try
	{
		$fb = wt_get_fb();
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->get('/me?fields=' . $fields, $access_token);
		return $response->getGraphUser();
	}
	catch (Facebook\Exceptions\FacebookResponseException $e)
	{
		wt_log('Graph returned an error: ' . $e->getMessage(), 'fb');
//		return new WP_Error('fb_error', 'Graph returned an error: ' . $e->getMessage());
	}
	catch (Facebook\Exceptions\FacebookSDKException $e)
	{
		wt_log('Facebook SDK returned an error: ' . $e->getMessage(), 'fb');
//		return new WP_Error('fb_error', 'Graph returned an error: ' . $e->getMessage());
	}
}

<?php
/*
Plugin Name: wt+ Captcha
*/

defined('ABSPATH') || exit;



function wt_captcha()
{
	wp_enqueue_script('wt-captcha');
	?>
	
	<div class="g-recaptcha" data-sitekey="6LfHYCwUAAAAAEAocWrmCaHKW5qwh3u3tapcegOB"></div>
	
	<?php
}
//add_action('login_form', 'wt_captcha');
//add_action('lostpassword_form', 'wt_captcha');



function wt_verify_captcha()
{
	if (empty($_REQUEST['g-recaptcha-response']))
		return new WP_Error('captcha-missing-input-response', 'Please check "I\'m not a robot".');
	
	$response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', array(
		'body' => array(
			'secret' => '6LfHYCwUAAAAAMirYfBNGyU5XHzK29FDO_r_-HPA',
			'response' => $_REQUEST['g-recaptcha-response']
		)
	));
	
	if (is_wp_error($response))
	{
		wt_log($response, 'captcha');
		return $response;
	}
	
	$body = json_decode($response['body'], true);
	
	if (isset($body['success']) && $body['success'])
		return true;
	
	if (!isset($body['error-codes']))
	{
		wt_log($body, 'captcha');
		return new WP_Error('captcha-error', 'Captcha verification failed. Please try again or contact Customer Service at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>.');
	}
	
	if (in_array('missing-input-response', $body['error-codes']))
	{
		return new WP_Error('captcha-missing-input-response', 'Please check "I\'m not a robot".');
	}
	
	if (in_array('invalid-input-response', $body['error-codes']))
	{
		return new WP_Error('captcha-invalid-input-response', 'Invalid captcha. Please try again.');
	}
	
	if (in_array('timeout-or-duplicate', $body['error-codes']))
	{
		return true;
	}
	
	wt_log($body['error-codes'], 'captcha');
	return new WP_Error('captcha-error', 'Captcha verification failed. Please try again or contact Customer Service at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>.');
}



add_action('init', function()
{
	wp_register_script( 'wt-captcha', 'https://www.google.com/recaptcha/api.js', [], null );
});



add_filter('authenticate', function($user)
{
//	$verified = wt_verify_captcha();
//
//	if (is_wp_error($verified))
//		return $verified;
	
	return $user;
}, 99); // avoid overwriting



add_filter('allow_password_reset', function($allow)
{
//	$verified = wt_verify_captcha();
//
//	if (is_wp_error($verified))
//		return $verified;
	
	return $allow;
}, 99);



//if (is_admin())
//{
	add_action('login_enqueue_scripts', function()
	{
		?>

		<style>
			.g-recaptcha > div
			{
				transform: scale(.9);
				transform-origin: left;
				margin-bottom: 1rem;
			}
		</style>
		
		<?php
	});
//}

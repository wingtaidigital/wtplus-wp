<?php
defined('ABSPATH') || exit;



function wt_email_exists($email/*, $exclude = null*/)
{
	$exists = email_exists( $email );
	
	if ($exists && is_user_logged_in() && $exists == get_current_user_id())
		return false;
	
//	if (!$exists)
//		return $exists;
//
//	if (!empty($exclude) && $exclude == $exists)
//		return false;
	
	return $exists;
}



function wt_user_meta_exists($key, $value)
{
	global $wpdb;
	
	$sql = "
		SELECT user_id
		FROM $wpdb->usermeta
		WHERE meta_key = %s AND meta_value = %s
	";
	
	if (is_user_logged_in())
		$sql .= " AND user_id <> " . get_current_user_id();
	
	return $wpdb->get_var($wpdb->prepare($sql, $key, $value));
}



//function wt_username_exists($username/*, $exclude = null*/)
//{
//	$exists = username_exists( sanitize_user($username) );
//
//	if ($exists && is_user_logged_in() && $exists == get_current_user_id())
//		return false;
//
////	if (!$exists)
////		return $exists;
////
////	if (!empty($exclude) && $exclude == $exists)
////		return false;
//
//	return $exists;
//}



//	wp/v2/users needs username
register_rest_route('wt/v1', '/users', [
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
		
		if (wt_user_meta_exists( 'wt_nric', $params['meta']['wt_nric'] ))
			return new WP_Error('wt_exists', "This NRIC/FIN/Passport No has already been registered. <a data-open='showcase-login-modal'>Log in here.</a>", array('status' => 400));
		
		if (wt_user_meta_exists( 'wt_mobile', $params['meta']['wt_mobile'] ))
			return new WP_Error('wt_exists', "This mobile number has already been registered. <a data-open='showcase-login-modal'>Log in here.</a>", array('status' => 400));
		
		if (!wt_is_possible_phone_number($params['meta']['wt_mobile']))
			return new WP_Error('wt_invalid_value', 'Please enter a valid mobile no.', array('status' => 400));
		
		if ( $params['user_pass'] != $params['pass2'] )
			new WP_Error( 'wt_password_mismatch', 'Passwords must match.' );
		
		$check_password = wt_check_password($params['user_pass']);
		
		if (is_wp_error($check_password))
			return $check_password;
		
		$params['user_login'] = sanitize_user($params['meta']['wt_organization'], true);
		$params['display_name'] = $params['meta']['wt_organization'];
		$i = 1;

		while (username_exists($params['user_login']))
		{
			$params['user_login'] .= $i;

			$i++;
		}
		
		$user_id = wp_insert_user($params);
		
		if (is_wp_error($user_id))
			return $user_id;
		
		foreach ($params['meta'] as $key => $value)
			update_user_meta( $user_id, $key, sanitize_text_field($value) );
		
		if (function_exists('wt_mail'))
		{
			$fields = [
				'first_name' => $params['first_name'],
				'last_name' => $params['last_name'],
				'user_email' => $params['user_email'],
				'forgot_password_url' => wp_lostpassword_url()
			];
			
			wt_mail('welcome', $fields, $params['user_email']);
		}
		
		if (empty($params['event']))
			return;
		
		wt_register_event($params['event'], $user_id);
		
		return add_query_arg('success', rawurlencode('Your profile has been created successfully.<br>Log in now:'), wp_login_url());
		
//		$post = get_post($params['event']);
//
//		if (!$post || $post->post_type != 'wt_event')
//			return;
//
//		$can_register = get_post_meta($post->ID, 'wt_can_register', true);
//
//		if (!$can_register)
//			return;
//
//		add_row( 'wt_registrations', [
//			'user' => $user_id
//		], $post->ID );
//
//		update_user_meta($user_id, 'wt_registrations', [$post->ID => -1]);
	},
	'args' => [
		'agreed' => [
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param );
			},
		],
		'user_email' => [
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return is_email( $param );
			},
		],
		'user_pass' => [
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param );
			},
		],
		'pass2' => [
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param );
			},
		],
		'meta' => [
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				$fields = ['nric', 'mobile', 'organization', 'nature'];
				
				foreach ($fields as $field)
				{
					if (empty($fields))
						return false;
				}
				
				return true;
			},
		]
	]
]);



register_rest_route('wt/v1', '/users/(?P<ID>\d+)', [
	'methods' => 'POST',
	'callback' => function($request)
	{
		$params = $request->get_params();
		
		if ($params['ID'] != get_current_user_id())
			new WP_Error('wt_forbidden', 'Forbidden', ['status' => 403]);
		
		if (wt_email_exists($params['user_email']/*, $params['ID']*/))
			new WP_Error('wt_conflict', 'Email is registered by another user.', ['status' => 409]);
		
		if (wt_user_meta_exists( 'wt_nric', $params['meta']['wt_nric'] ))
			return new WP_Error('wt_exists', "This NRIC/FIN/Passport No has already been registered by another user.", array('status' => 400));
		
		if (wt_user_meta_exists( 'wt_mobile', $params['meta']['wt_mobile'] ))
			return new WP_Error('wt_exists', "This mobile number has already been registered by another user.", array('status' => 400));
		
		if (!wt_is_possible_phone_number($params['meta']['wt_mobile']))
			return new WP_Error('wt_invalid_value', 'Please enter a valid mobile no.', array('status' => 400));
		
		if (empty($params['user_pass']))
		{
			unset($params['user_pass']);
		}
		else
		{
			if ($params['user_pass'] != $params['pass2'])
				new WP_Error('wt_password_mismatch', 'Passwords must match.', ['status' => 400]);
			
			$check_password = wt_check_password($params['user_pass'], $params['ID']);

			if (is_wp_error($check_password))
				return $check_password;
		}
	
//		$user = get_userdata($params['ID']);
//		$params['user_login'] = sanitize_user($params['meta']['wt_organization'], true);
//
//		if ($user->user_login != $params['user_login'])
//		{
//			if (wt_username_exists($params['user_login']))
//				new WP_Error('wt_conflict', 'Company/Organization is registered by another user.', ['status' => 409]);
//
//			global $wpdb;
//
//			$wpdb->update($wpdb->users, array('user_login' => $params['user_login']), array('ID' => $params['ID']));
//
//			wp_set_current_user( $user->ID, $params['user_login'] );
//			wp_set_auth_cookie( $user->ID );
//			do_action( 'wp_login', $params['user_login'], $user );
//
//			$params['display_name'] = $params['nickname'] = $params['user_nicename'] = $params['meta']['wt_organization'];
//		}
		
		$params['display_name'] = $params['nickname'] = $params['meta']['wt_organization'];
		
		$user_id = wp_update_user($params);
		
		if (is_wp_error($user_id))
			return $user_id;
		
		foreach ($params['meta'] as $key => $value)
			update_user_meta($user_id, $key, sanitize_text_field($value));
		
//		if ($user->user_login != $params['user_login'])
//		{
//			$url = add_query_arg([
//				'profile' => '',
//				'updated' => '',
//			], get_author_posts_url($user_id));
//
//			return $url;
//		}
	},
	'args' => [
		'user_email' => [
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return is_email( $param );
			},
		],
		'meta' => [
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param['wt_organization'] );
			},
		]
	]
]);



register_rest_route('wt/v1', '/email-exists', array(
	'methods' => 'POST',
	'callback' => function($request)
	{
		$params = $request->get_params();
		
		return wt_email_exists( $params['email'], empty($params['exclude']) ? null : $params['exclude'] );
	},
	'args' => array(
		'email' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return is_email( $param );
			}
		),
	),
));



register_rest_route('wt/v1', '/user-meta-exists', array(
	'methods' => 'POST',
	'callback' => function($request)
	{
		$params = $request->get_params();
		
		return wt_user_meta_exists( $params['key'], $params['value'] );
	},
	'args' => array(
		'key' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param );
			},
			'sanitize_callback' => function($param, $request, $key)
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
			'sanitize_callback' => function($param, $request, $key)
			{
				return sanitize_text_field( $param );
			}
		),
	),
));



//register_rest_route('wt/v1', '/username-exists', array(
//	'methods' => 'POST',
//	'callback' => function($request)
//	{
//		$params = $request->get_params();
//
//		return wt_username_exists( $params['username'], empty($params['exclude']) ? null : $params['exclude'] );
//	},
//	'args' => array(
//		'username' => array(
//			'required' => true,
//			'validate_callback' => function($param, $request, $key)
//			{
//				return !empty( $param );
//			}
//		),
//	),
//));



register_rest_route('wt/v1', '/login', array(
	'methods' => 'POST',
	'callback' => function($request)
	{
		$params = $request->get_params();
		
		$user = wp_signon($params);
		
		if (is_wp_error($user))
		{
			return wt_login_error($user);
		}
		
//		wp_set_current_user( $user->ID, $params['user_login'] );
////		do_action( 'wp_login', $params['user_login'], $user );
//
//		$sessions = WP_Session_Tokens::get_instance( $user->ID );
//		wt_log(wp_get_session_token());
//		$sessions->destroy_others( wp_get_session_token() );
		
		$url = empty($params['redirect_to']) ? get_author_posts_url($user->ID) : $params['redirect_to'];
		$url = add_query_arg('loggedin', '', $url);
		
		if (!empty($params['anchor']))
			$url .= '#' . $params['anchor'];
		
		return esc_url($url);
	},
	'args' => array(
		'user_login' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return is_email( $param );
			}
		),
		'user_password' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param );
			}
		),
	),
));



register_rest_route('wt/v1', '/forgot-password', array(
	'methods' => 'POST',
	'callback' => function($request)
	{
//		if (function_exists('wt_verify_captcha'))
//		{
//			$verified = wt_verify_captcha();
//
//			if (is_wp_error($verified))
//				return $verified;
//		}
		
		$params = $request->get_params();
		$_POST['user_login'] = $params['user_login'];
		
		ob_start();
		
		require_once( ABSPATH . 'wp-login.php' );
		
		ob_end_clean();
		
		$result = retrieve_password();
		
		if (is_wp_error($result))
		{
			/*$code = $result->get_error_code();

			if ($code == 'invalid_email' || $code == 'invalidcombo')
			{
				$min_age = get_option('options_wt_password_min_age');
				
				if ($min_age && is_numeric($min_age))
					$min_age = (int) abs($min_age);
				else
					$min_age = 3;
				
				return new WP_Error('wt_error', "User account cannot be found or password can only be changed after $min_age " . _n('day', 'days', $min_age, 'wt') . '. Please contact Customer Service at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>.', ['status' => 400]);
			}*/
			
			return $result;
		}
		
		return new WP_REST_Response(array(
			'message' => 'Check your email for the confirmation link.'
		));
	},
	'args' => array(
		'user_login' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param );
			}
		),
	),
));



register_rest_route('wt/v1', '/reset-password', array(
	'methods' => 'POST',
	'callback' => function($request)
	{
		$params = $request->get_params();
		
		if ( $params['user_pass'] != $params['pass2'] )
			return new WP_Error('wt_password_mismatch', 'Passwords must match.', ['status' => 400]);
		
//		list( $rp_path ) = explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) );
		$rp_path = '/';
		
		$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
		
		if ( isset( $_COOKIE[ $rp_cookie ] ) && 0 < strpos( $_COOKIE[ $rp_cookie ], ':' ) ) {
			list( $rp_login, $rp_key ) = explode( ':', wp_unslash( $_COOKIE[ $rp_cookie ] ), 2 );
			$user = check_password_reset_key( $rp_key, $rp_login );
			if ( isset( $params['user_pass'] ) && ! hash_equals( $rp_key, $params['rp_key'] ) ) {
				$user = false;
			}
		} else {
			$user = false;
		}
		
		if ( ! $user || is_wp_error( $user ) ) {
//			setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
			
			if ( $user && $user->get_error_code() === 'expired_key' )
				$error = 'Your password reset link has expired.';
			else
				$error = 'Your password reset link appears to be invalid.';
			
			$error .= ' <a href="' . home_url('showcase/forgot-password') . '">Please request a new link.</a>';
			
			return new WP_Error('wt_invalid_key', $error, ['status' => 400]);
		}
		
		
		if (function_exists('wt_check_password'))
		{
			$check_password = wt_check_password($params['user_pass'], $user->ID, false);
			
			if (is_wp_error($check_password))
			{
				return $check_password;
			}
		}
		
		
		$errors = new WP_Error();
		
		/**
		 * Fires before the password reset procedure is validated.
		 *
		 * @since 3.5.0
		 *
		 * @param object           $errors WP Error object.
		 * @param WP_User|WP_Error $user   WP_User object if the login and reset key match. WP_Error object otherwise.
		 */
		do_action( 'validate_password_reset', $errors, $user );
		
		if ( ! $errors->get_error_code() ) {
			reset_password($user, $params['user_pass']);
			setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
			
			return true;
		}
		
		return $errors;
	},
	'args' => array(
		'user_pass' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param );
			}
		),
		'pass2' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param );
			}
		),
		'rp_key' => array(
			'required' => true,
			'validate_callback' => function($param, $request, $key)
			{
				return !empty( $param );
			}
		),
	),
));

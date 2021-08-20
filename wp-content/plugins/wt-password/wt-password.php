<?php
/*
Plugin Name: wt+ Password Policy
*/

defined('ABSPATH') || exit;



function wt_get_password_min_length()
{
	$min_length = get_option('options_wt_password_min_length');
	
	if ($min_length && is_numeric($min_length))
	{
		$min_length = (int) abs($min_length);
		
		return [
			'len' => $min_length,
			'error' => "Password must consist of a minimum of $min_length characters"
		];
	}
}



function wt_get_password_complexity()
{
	if (!function_exists('get_field'))
		return;
	
	$complexity = get_field('wt_password_complexity', 'option');
	
	if (!$complexity || !is_array($complexity))
		return;
	
	$regex = '';
	$description = [];
	
	foreach ($complexity as $c)
	{
		if (empty($c['value']))
			return;
		
		if ($c['value'] === 'd')
			$c['value'] = '\d';
		else if ($c['value'] === 'W')
			$c['value'] = '[\W_]';
			
		$regex .= "(?=.*{$c['value']})";
		$description[] = $c['label'];
	}
	
	return [
		'regex' => "{$regex}",
		'error' => "Password must consist of: " . implode(', ', $description)
	];
}



function wt_check_password_min_age($user_id)
{
	$min_age = get_option('options_wt_password_min_age');
	
	if ($min_age && is_numeric($min_age))
	{
		$min_age = (int) abs($min_age);
		$modified = get_user_meta($user_id, 'wt_password_modified', true);
		
		if ($modified)
		{
			$modified = new DateTime($modified);
			$now = new DateTime();
			$interval = $modified->diff($now);
			
			if ($interval->days <= $min_age)
				return new WP_Error('wt_error', "User account cannot be found or password can only be changed after $min_age " . _n('day', 'days', $min_age, 'wt') . '. Please contact Customer Service at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>.', ['status' => 400]);
//				return new WP_Error('wt_password_too_new', "Password can only be changed after {$min_age} " . _n('day', 'days', $min_age, 'wt'), ['status' => 400]);
		}
	}
}



function wt_check_password($password, $user_id = 0, $check_min_age = true)
{
	$min_length = wt_get_password_min_length();
	
	if ($min_length)
	{
		if (strlen($password) < $min_length['len'])
			return new WP_Error('wt_password_too_short', $min_length['error'], ['status' => 400]);
	}
	
	$complexity = wt_get_password_complexity();
	
	if ($complexity)
	{
		$matched = preg_match("({$complexity['regex']})", $password);
		
		if (!$matched)
			return new WP_Error('wt_password_too_simple', $complexity['error'], ['status' => 400]);
	}
	
	if (!$user_id)
		return;
	
	
	$history = get_option('options_wt_password_history');
	
	if ($history && is_numeric($history))
	{
		$passwords = get_user_meta($user_id, 'wt_passwords', true);
		
		if ($passwords && is_array($passwords))
		{
//			$hashed_password = wp_hash_password($password);
			$history = (int) abs($history);
			
			if (count($passwords) > $history)
			{
				$passwords = array_slice($passwords, -$history, $history);
				update_user_meta($user_id, 'wt_passwords', $passwords);
			}
			
			foreach ($passwords as $p)
			{
				if (wp_check_password($password, $p))
					return new WP_Error('wt_password_reused', "You used this password recently. Please choose a different one.", ['status' => 400]);
			}
		}
	}
	
	
	if (!$check_min_age)
		return;
	
	$min_age = wt_check_password_min_age($user_id);
	
	if (is_wp_error($min_age))
		return $min_age;
}



add_action('wp_login', function($user_login, $user)
{
	update_user_meta($user->ID, 'wt_failed_logins', 0);
}, 10, 2);



/*add_filter( 'allow_password_reset', function($allow, $user_id)
{
	$min_age = wt_check_password_min_age($user_id);
	
	if (is_wp_error($min_age))
		return $min_age;
	
	return $allow;
}, 10, 2);*/



add_action('user_profile_update_errors', function( &$errors, $update, &$user )
{
	if (empty($_POST['pass1']))
		return;
	
	$result = wt_check_password($_POST['pass1'], $user->ID);
	
	if (is_wp_error($result))
		$errors->add($result->get_error_code(), $result->get_error_message());
	
}, 10, 3);

add_action('validate_password_reset', function($errors, $user)
{
	if (empty($_POST['pass1']) || !function_exists('wt_check_password'))
		return;
	
	$result = wt_check_password($_POST['pass1'], $user->ID, false);
	
	if (is_wp_error($result))
		$errors->add($result->get_error_code(), $result->get_error_message());
	
}, 10, 2);

add_action('password_reset', function($user, $new_pass)
{
	$result = wt_check_password($new_pass, $user->ID, false);
	
	if (is_wp_error($result))
		wp_die($result);
//		return $result;

}, 10, 2);



function wt_update_password_meta($user, $password = '')
{
	update_user_meta($user->ID, 'wt_failed_logins', 0);
	update_user_meta($user->ID, 'wt_password_modified', current_time('mysql'));
	
	$max_age = get_option('options_wt_password_max_age');
	
	if (is_numeric($max_age))
	{
		$max_age = (int) abs($max_age);
		$time = strtotime("+" . $max_age . " days", current_time('timestamp'));

		update_user_meta($user->ID, 'wt_password_expiry', date('Y-m-d H:i:s', $time));
	}
	
	$password = empty($password) ? $user->user_pass : $password;
	
	if (!$password)
		return;
	
	$history = get_option('options_wt_password_history');
	
	if (!$history || !is_numeric($history))
		return;
	
	$history = (int) abs($history);
	$passwords = get_user_meta($user->ID, 'wt_passwords', true);
	
	if (!is_array($passwords))
		$passwords = [];
	
	$passwords[] = wp_hash_password($password);
	
	if (count($passwords) > $history)
		$passwords = array_slice($passwords, -$history, $history);
	
	update_user_meta($user->ID, 'wt_passwords', $passwords);
}

add_action('user_register', function($user_id)
{
	$user = get_userdata($user_id);
	wt_update_password_meta($user);
});

add_action('profile_update', function($user_id, $old_user_data)
{
	$user = get_userdata($user_id);
	wt_update_password_meta($user);

}, 10, 2);

add_action('after_password_reset', function($user, $new_pass)
{
	wt_update_password_meta($user, $new_pass);
	
//	update_user_meta($user->ID, 'wt_failed_logins', 0);

}, 10, 2);



//add_filter('authenticate', function($user, $username, $password)
add_filter('wp_authenticate_user', function($user, $password)
{
	if (!$user || is_wp_error($user))
		return $user;
	
	$threshold = get_option('options_wt_lockout_threshold');
	
	if (!$threshold)
		return $user;
	
	$failed_logins = get_user_meta($user->ID, 'wt_failed_logins', true);
	
	if (!$failed_logins)
		return $user;
	
	if ((int) $failed_logins > (int) $threshold)
		return new WP_Error('wt_lockout', 'Your account has been locked. Please <a href="' . add_query_arg('reset', '', home_url('showcase/forgot-password')) . '">reset your password</a>.', ['status' => 400]);
	
	return $user;
}, 10, 2);



add_action('wp_login_failed', function($user_login)
{
	$user = get_user_by(is_email($user_login) ? 'email' : 'login', $user_login);
	
	if (!$user)
		return;
	
	$failed_logins = get_user_meta($user->ID, 'wt_failed_logins', true);
	
	if (!is_numeric($failed_logins))
		$failed_logins = 0;
	
	update_user_meta($user->ID, 'wt_failed_logins', ++$failed_logins);
});



if (is_admin() && function_exists('acf_add_options_page'))
{
	acf_add_options_sub_page(array(
			'page_title' => 'wt+ Password Policy',
			'menu_title' => 'wt+ Password Policy',
			'parent_slug' => 'options-general.php',
			'capability' => 'manage_options',
		));
}

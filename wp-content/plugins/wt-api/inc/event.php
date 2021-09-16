<?php
defined('ABSPATH') || exit;



function wt_acf_get_repeater_row($repeater, $post_id, $field_name, $field_value)
{
	if (!function_exists('get_field_object'))
		return false;
	
	$object = get_field_object($repeater, $post_id, false);
	
	if (!$object)
		return false;
	
	$registrations = $object['value'];
	$fields = $object['sub_fields'];
	
	foreach ($fields as $field)
	{
		if ($field['name'] == $field_name)
			$key = $field['key'];
	}
	
	if (!isset($key))
		return false;

	if ($registrations && is_array($registrations))
	{
		foreach ($registrations as $i => $registration)
		{
			if ($registration[$key] == $field_value)
			{
				return $i + 1;
			}
		}
	}
	
	return false;
}



function wt_register_event($event_id, $user_id = null)
{
	if (!$user_id)
		$user_id = get_current_user_id();
	
	if (!$user_id)
		return new WP_Error('wt_forbidden', 'Please login to register event.', ['status' => 403]);
	
	$post = get_post($event_id);
	
	if (!$post || $post->post_type != 'wt_event')
		return new WP_Error('wt_not_found', 'Event not found.', ['status' => 404]);
	
	$can_register = get_post_meta($post->ID, 'wt_can_register', true);
	
	if (!$can_register)
		return new WP_Error('wt_forbidden', 'Event is not open for registration.', ['status' => 403]);
	
	$registrations = get_user_meta($user_id, 'wt_registrations', true);
	
	if (!is_array($registrations))
		$registrations = [];
	
	$exists = array_key_exists($event_id, $registrations);
	
	if ($exists && $registrations[$event_id] != 2)
		return;
	
	$registrations[$event_id] = -1;
	
	update_user_meta($user_id, 'wt_registrations', $registrations);
	
	if ($exists)
	{
		$row = wt_acf_get_repeater_row('wt_registrations', $event_id, 'user', $user_id);
		
		if ($row !== false)
		{
			update_row('wt_registrations', $row, [
				'user' => $user_id,
				'status' => -1
			], $event_id);
			
			$exists = true;
		}
	}
	
	if (!$exists)
	{
		add_row('wt_registrations', [
			'user' => $user_id
		], $event_id);
	}
	
	
	
	if (function_exists('wt_mail'))
	{
		$user = get_userdata($user_id);
		
		$fields = [
			'first_name' => $user->first_name,
			'last_name' => $user->last_name,
			'event' => $post->post_title,
			'login_url' => wp_login_url()
		];
		
		wt_mail('event', $fields, $user->user_email, ['From: showcase@wtplus.com.sg']);
	}
}



register_rest_route('wt/v1', '/events/(?P<ID>\d+)/register', [
	'methods' => 'POST',
	'callback' => function($request)
	{
		$params = $request->get_params();
		
		wt_register_event($params['ID']);
	},
]);



register_rest_route('wt/v1', '/events/(?P<ID>\d+)/cancel', [
	'methods' => 'POST',
	'callback' => function($request)
	{
		if (!function_exists('get_field_object'))
			return;

		$params = $request->get_params();
		$user_id = get_current_user_id();
		$registrations = get_user_meta($user_id, 'wt_registrations', true);
		
		if (!$registrations || empty($registrations[$params['ID']]) || $registrations[$params['ID']] != -1)
			return;
		
		$registrations[$params['ID']] = 2;
		update_user_meta($user_id, 'wt_registrations', $registrations);
	
		$row = wt_acf_get_repeater_row('wt_registrations', $params['ID'], 'user', $user_id);
		
		if ($row === false)
			return;
		
		update_row( 'wt_registrations', $row, [
			'user' => $user_id,
			'status' => 2
		], $params['ID'] );
	},
]);

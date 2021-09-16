<?php
namespace WT;

use WP_Error;

class OM
{
	const API_URL = 'https://developers.onemap.sg/';
	
	static function request($endpoint, $method = 'GET', $args = [])
	{
		if (strpos($endpoint, 'privateapi') === 0)
		{
			$transient = 'wt_om_token';
			$token     = get_transient($transient);
			
			if (!$token)
			{
				$token_args = [
					'headers' => ['Content-Type' => 'application/json'],
					'body'    => json_encode([
						'email'    => 'yeetien@gmail.com',
						'password' => '0nemap',
					])
				];
				$response   = wp_remote_post(self::API_URL . 'privateapi/auth/post/getToken', $token_args);
				
				if (is_wp_error($response))
				{
					wt_log($response, 'om');
					
					return $response;
				}
				
				$data  = json_decode($response['body']);
				$token = $data->access_token;
				
				set_transient($transient, $token, 3 * DAY_IN_SECONDS);
			}
			
			$args['token'] = $token;
		}
		
		$response = wp_remote_request(self::API_URL . $endpoint, [
			'method' => $method,
			'body' => $args
		]);
		
		if (is_wp_error($response))
		{
			wt_log($response, 'om');
			
			return $response;
		}
		
		$data = json_decode($response['body'], true);
		
		if ($response['response']['code'] != 200)
		{
			wt_log($response['response'], 'om');
			wt_log($response['body'], 'om');
			
			return new WP_Error('om-' . $response['response']['code'], $response['response']['message'] . ': ' . $data['error'], ['status' => $response['response']['code']]);
		}
		
		return $data;
	}
}

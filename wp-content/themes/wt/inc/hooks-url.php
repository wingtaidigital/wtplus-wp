<?php
add_filter('register_url', function($register_url)
{
	return home_url('showcase/signup/');
}, 10, 2 );



add_filter('login_url', function( $login_url, $redirect )
{
	$url = home_url('showcase/login/');
	
	if ($redirect)
		$url = add_query_arg('redirect_to', $redirect, $url);
	
	return $url;
}, 10, 2);



add_filter('logout_redirect', function($redirect_to, $requested_redirect_to, $user)
{
	return add_query_arg('loggedout', '', wp_login_url());
}, 10, 3);



add_filter('lostpassword_url', function($lostpassword_url, $redirect)
{
	return home_url('showcase/forgot-password/');
	
}, 10, 2);



function wt_login_form_resetpass()
{
	if ('GET' == $_SERVER['REQUEST_METHOD'])
	{
		$redirect_to = home_url('showcase/reset-password/');
		$redirect_to = add_query_arg( 'login', $_GET['login'], $redirect_to );
		$redirect_to = add_query_arg( 'key', $_GET['key'], $redirect_to );
		
		wp_redirect($redirect_to);
		exit;
	}
}
add_action( 'login_form_rp', 'wt_login_form_resetpass' );
add_action( 'login_form_resetpass', 'wt_login_form_resetpass' );

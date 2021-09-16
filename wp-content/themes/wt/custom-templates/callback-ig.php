<?php
if (!empty($_REQUEST['hub_challenge']) || !empty($_REQUEST['hub.challenge'])) // "." is converted to "_"
{
	echo sanitize_text_field($_REQUEST['hub_challenge']);
	exit;
}

if (isset($_GET['callback']))
{
	wt_log($_REQUEST, 'ig');
//	wt_dump($_REQUEST);
	exit;
}

$client_id = sanitize_text_field(get_option('options_wt_ig_client_id'));
$client_secret = get_option('options_wt_ig_client_secret');
$redirect_uri = add_query_arg('ig', '', home_url('/'));

if (isset($_GET['subscribe']))
{
	$response = wp_remote_post('https://api.instagram.com/v1/subscriptions', [
		'body' => [
			'client_id' => $client_id,
			'client_secret' => $client_secret,
			'object' => 'user',
			'aspect' => 'media',
			'verify_token' => 'wt',
			'callback_url' => home_url('/?ig&callback')
		]
	]);
	wt_dump($response);
	exit;
}

get_header();
?>

<?php
if (!empty($_GET['code']))
{
	$response = wp_remote_post('https://api.instagram.com/oauth/access_token', [
		'body' => [
			'client_id' => $client_id,
			'client_secret' => $client_secret,
			'grant_type' => 'authorization_code',
			'redirect_uri' => $redirect_uri,
			'code' => $_GET['code']
		]
	]);
	
	if (!is_wp_error($response))
	{
//		wt_dump($response);
		
		$body = json_decode($response['body'], true);
		
		if (!empty($body['access_token']))
		{
			update_option('wt_ig_user_' . $body['user']['username'], $body);
			?>
			
			<div class="callout success"><?php echo sanitize_text_field($body['user']['username']); ?> authenticated.</div>
			
			<?php
		}
	}
}
?>

<div class="text-center">
	<a href="https://api.instagram.com/oauth/authorize/?client_id=<?php echo $client_id; ?>&redirect_uri=<?php echo $redirect_uri; ?>&response_type=code" class="button">
		<i class="fa fa-instagram" aria-hidden="true"></i> Authorize
	</a>
</div>

<?php get_footer();

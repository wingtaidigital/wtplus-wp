<?php
function wt_fb_button($is_login = false, $label = 'Log in with Facebook')
{
	if (!$is_login)
	{
		$fb_user = wt_get_fb_user('id');
		
		if ($fb_user)
		{
			$label = 'Cancel Facebook login';
			$url = add_query_arg('logout_fb', '');
		}
	}
	
	if (!isset($url))
	{
		$fb = wt_get_fb();
		$helper = $fb->getRedirectLoginHelper();
		$permissions = [/*'public_profile ', */'email'];
		$callback_url = add_query_arg('fb', '1', home_url('/')); // must have a value otherwise would hit fb error: Error validating verification code. Please make sure your redirect_uri is identical to the one you used in the OAuth dialog request
		
		if (isset($_GET['rerequest']))
			$url = $helper->getReRequestUrl($callback_url, $permissions);
		else
			$url = $helper->getLoginUrl($callback_url, $permissions);
	}
	?>
	
	<a href="<?php echo $url; ?>" class="button wt-background-fb wt-margin-bottom <?php echo $is_login ? 'expanded' : ''; ?>">
		<i class="icon icon-facebook-official" title="Facebook"></i>
		<?php echo $label; ?>
	</a>
	
	<?php
}



function wt_ig($brand_id = null)
{
	$transient = 'wt_ig';
	
	if ($brand_id)
	{
		$transient .= '_' . $brand_id;
		$username = get_post_meta($brand_id, 'wt_ig_username', true);
	}
	else
	{
		$username = get_option('options_wt_ig_username');
	}
	
	if (empty($username))
		return;
	
	$username = sanitize_text_field($username);
//	$url = esc_url('https://www.instagram.com/' . $username . '/');
	$url = esc_url('https://apinsta.herokuapp.com/u/' . $username . '/');
	
	if (isset($_GET['clear_cache']))
		$json = '';
	else
		$json = get_transient($transient);
	
	if ($json)
	{
		$json = json_decode($json, true);
	}
	else
	{
//		$response = wp_remote_get($url . '?__a=1'); // not working on js because of cors
		$response = wp_remote_get($url);
	
		if (!is_wp_error($response))
		{
//			$json = $response['body'];
			$json = json_decode($response['body'], true);
			
			if (empty($json['graphql']['user']['edge_owner_to_timeline_media']['edges']))
				return;
			
			$json = $json['graphql']['user']['edge_owner_to_timeline_media']['edges'];
			
			set_transient($transient, json_encode($json), DAY_IN_SECONDS);

//			$expiration = empty($cache_max_time) ? get_option('options_wt_ig_expiration') : $cache_max_time;
//
//			set_transient('wt_ig', $json, $expiration ? (int) $expiration : DAY_IN_SECONDS);
		}
	}
	
	/*$json = json_decode($json, true);
//	wt_dump($json);
//	if (empty($json['user']['media']['nodes']))
	if (empty($json['graphql']['user']['edge_owner_to_timeline_media']['edges']))
		return;*/

//	wt_dump($json['graphql']['user']['edge_owner_to_timeline_media']['edges']);
	if ($json && is_array($json))
	{
		?>
		
		<section id="instagram" class="row collapse">
			<div class="column wt-slick-container">
				<h1 class="text-center wt-h2 wt-gutter-half wt-gutter-bottom">What's on @<?php echo $username; ?></h1>
				
				<div class="text-center wt-slick-arrows wt-gutter">
					<?php
					//				foreach ($json['user']['media']['nodes'] as $ig_item)
					foreach ($json as $ig_item)
					{
						$ig_item = $ig_item['node'];
//					wt_dump($ig_item);
//					$caption = esc_attr($ig_item['caption']);
//					$caption = esc_attr($ig_item['edge_media_to_caption'][0]['node']['text']);
						$caption = '';
						?>
						
						<article class="wt-gutter-half" itemscope itemtype="http://schema.org/SocialMediaPosting">
							<a href="https://www.instagram.com/p/<?php echo $ig_item['shortcode']; ?>/" target="_blank" rel="noreferrer" itemprop="url" title="<?php echo $caption; ?>" class="wt-background-cover" style="background-image: url(<?php echo $ig_item['display_url']; ?>)">
								<img src="<?php echo $ig_item['thumbnail_src']; ?>" alt="<?php echo $caption; ?>" itemprop="image" width="<?php echo $ig_item['dimensions']['width']; ?>" height="<?php echo $ig_item['dimensions']['width']; ?>">
							</a>
						</article>
						
						<?php
						/*<article class="wt-gutter-half" itemscope itemtype="http://schema.org/SocialMediaPosting">
							<a href="https://www.instagram.com/p/<?php echo $ig_item['code']; ?>/" target="_blank" rel="noreferrer" itemprop="url" title="<?php echo $caption; ?>" class="wt-background-cover" style="background-image: url(<?php echo $ig_item['display_src']; ?>)">
								<img src="<?php echo $ig_item['thumbnail_src']; ?>" alt="<?php echo $caption; ?>" itemprop="image" width="<?php echo $ig_item['dimensions']['width']; ?>" height="<?php echo $ig_item['dimensions']['width']; ?>">
							</a>
						</article>*/
						//$ig_item['images']['standard_resolution']['width'] < $ig_item['images']['standard_resolution']['height'] ?
						//class="<?php echo $ig_item['images']['standard_resolution']['width'] < $ig_item['images']['standard_resolution']['height'] ? 'wt-portrait' : ''; ?"
					}
					?>
				</div>
				
				<div class="text-right wt-gutter">
					<a href="<?php echo esc_url('https://www.instagram.com/' . $username . '/'); ?>" target="_blank" rel="noreferrer" class="wt-text-hover wt-gutter-half">VIEW MORE</a>
				</div>
			</div>
		</section>
		
		<?php
	}
}

<?php
/*
Plugin Name: wt+ Email Templates
*/

defined('ABSPATH') || exit;



if (is_admin() && function_exists('acf_add_options_page'))
{
	acf_add_options_sub_page(array(
		'page_title' => 'wt+ Email Templates',
		'menu_title' => 'wt+ Email Templates',
		'parent_slug' => 'options-general.php',
		'capability' => 'manage_options',
	));
}



function wt_mail($key, $fields = [], $to, $headers = [])
{
	$subject = get_option("options_wt_{$key}_subject");
	
	if (!$subject)
		return;
	
	$message = get_option("options_wt_{$key}_message");
	
	if (!$message)
		return;
	
	foreach ($fields as $key => $value)
	{
		$subject = str_replace('{' . $key . '}', $value, $subject);
		$message = str_replace('{' . $key . '}', $value, $message);
	}
	
	ob_start();
	?>
	
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width"/>
		</head>
		
		<body>
			<table align="center" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="center" style="background-color: #0E14AD; padding: 40px 0; color: #fff">
						<?php
						$logo = get_theme_mod( 'custom_logo' );
						
						if ( $logo )
						{
							?>
							
							<a href="<?php echo home_url(); ?>" target="_blank">
								<img src="<?php echo wp_get_attachment_url($logo); ?>" alt="<?php echo sanitize_text_field(get_option('blogname')); ?>" width="106">
							</a>
							
							<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<td style="background-color: #F7F7F7; padding: 40px 10px">
						<table align="center" cellpadding="0" cellspacing="0" width="600px">
							<tr>
								<td style="background-color: #fff; padding: 40px; font-family: Helvetica, Arial, sans-serif; font-size: 14px; color: #000; line-height: 1.4">
									<?php echo nl2br(wp_kses_post($message)); ?>
									
									<br><br><br><br>
									<table align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td style="background-color: #ff4178; padding: 10px 40px;">
												<a href="<?php echo wp_login_url(); ?>" style="padding: 5px; font-family: Helvetica, Arial, sans-serif; font-size: 14px; color: #fff; text-decoration: none">LOG IN</a>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center"style="background-color: #0E14AD; padding: 30px 0;">
						<table align="center" cellpadding="0" cellspacing="0">
							<tr>
								<td align="center" valign="top" style="padding: 5px; font-family: Helvetica, Arial, sans-serif; font-size: 14px; color: #fff">
									Follow us on
								</td>
								
								<?php
								$items = wp_get_nav_menu_items( 'social');
								
								foreach ($items as $item)
								{
									$icon = wt_get_social_icon($item->url);
									
									if (!$icon)
										continue;
									?>
									
									<td align="center" valign="top" style="padding: 5px; color: #fff">
										<a href="<?php echo esc_url($item->url); ?>" target="_blank"><img src="<?php echo get_theme_file_uri('/assets/img/social/' . $icon . '.png'); ?>" alt="<?php echo sanitize_text_field($item->title); ?>" height="31"></a>
									</td>
									
									<?php
								}
								?>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</body>
	</html>
	
	<?php
	$html = ob_get_clean();
	$headers = wp_parse_args($headers, ['Content-Type: text/html; charset=UTF-8']);
	
	wp_mail($to, sanitize_text_field($subject), $html, $headers);
}



add_filter( 'wp_mail_from', function ( $from_email )
{
	if (strpos($from_email, 'wordpress@') === false)
		return $from_email;
	
	$email = get_option('options_wt_email');
	
	if (!$email)
		$email = get_option('admin_email');
	
	return sanitize_email($email);
} );

add_filter( 'wp_mail_from_name', function ( $from_name )
{
	if ($from_name == 'WordPress')
		return sanitize_text_field(get_option('blogname'));
	
	return $from_name;
} );


//add_action('admin_post_preview_email', function()
//{
//	wt_mail(null, null, 'qwe');
//});

<?php
$title = '';
$content = '';

if (is_page('showcase/login'))
{
	$title = get_the_title();
	$content = get_the_content();
}
else
{
	$login_page = get_page_by_path('showcase/login');
	
	if ($login_page)
	{
		$title = get_the_title($login_page);
		$content = $login_page->post_content;
	}
}
?>

<div class="wt-container-small wt-account">
	<div class="text-center">
		<h1 class="wt-h3 wt-upper wt-margin-bottom"><?php echo $title; ?></h1>
		
		<?php if (isset($_GET['loggedout'])) { ?>
			<p>You are logged out.</p>
		<?php } else { ?>
			<?php echo wpautop(wp_kses_post($content)); ?>
		<?php } ?>
	</div>
	
	<form data-route="wt/v1/login" autocomplete="off">
		<div class="callout <?php echo empty($_GET['success']) ? 'hide' : 'success'; ?>" data-success="Logged in. Redirecting...">
			<?php
			if (!empty($_GET['success']))
				echo wp_kses_post($_GET['success']);
			?>
		</div>
		
		<?php
		//		wt_dump($fb_user);
		$fields = array(
			'user_login' => array(
				'label' => 'Email',
				'type' => 'email',
//				'autocomplete' => 'email',
			),
			'user_password' => array(
				'label' => 'Password',
				'type' => 'password',
				'autocomplete' => 'off',
			),
		);
		
		foreach ($fields as $name => $field)
		{
//			$value = empty($field['fb']) || empty($fb_user[$field['fb']]) ? '' : sanitize_text_field($fb_user[$field['fb']]);
			?>
			
			<label>
				<?php echo $field['label']; ?>
				<input type="<?php echo $field['type']; ?>" name="<?php echo $name; ?>" value="<?php //echo $value; ?>" <?php echo empty($field['autocomplete']) ? '' : 'autocomplete="' . $field['autocomplete'] . '"'; ?> required>
			</label>
			
			<?php
		}
		
		do_action( 'login_form' );
		?>
		
		<button type="submit" class="button secondary expanded" data-submit="Log in" data-submitting="Logging in..." data-submitted="Logged in. Redirecting...">Log in</button>
		
		<input type="hidden" name="redirect_to" value="<?php echo isset($_GET['redirect_to']) ? esc_url($_GET['redirect_to']) : get_permalink(); ?>">
		<input type="hidden" name="anchor" value="">
	</form>
	
	<a href="<?php echo wp_lostpassword_url(); ?>">Forgot your password?</a><br>
	Not a Showcase Partner yet? <a href="<?php echo wp_registration_url(); ?>" class="wt-registration-url">Sign up now.</a>
</div>

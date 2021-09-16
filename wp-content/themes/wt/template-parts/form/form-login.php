<?php
defined('ABSPATH') || exit;

global $verified;

$title = '';
$content = '';

if (is_page('login'))
{
	$title = get_the_title();
	$content = get_the_content();
}
else
{
	$login_page = get_page_by_path('login');
	
	if ($login_page)
	{
		$title = get_the_title($login_page);
		$content = $login_page->post_content;
	}
}
?>

<div class="wt-container-small wt-account">
	<div class="text-center">
		<h1 class="wt-h3 wt-margin-bottom"><?php echo $title; ?></h1>
		
		<?php //<p style="color: #f00; font-weight: bold">Our member portal will be temporarily down for maintenance today from 6am to 10am. We apologize for the inconvenience caused.</p> ?>
		
		<?php if (isset($_GET['loggedout'])) { ?>
			<p>You are logged out.</p>
		<?php } else if (isset($_GET['timeout'])) { ?>
			<p>Session timeout. Please login again.</p>
		<?php } else { ?>
			<?php echo wpautop(wp_kses_post($content)); ?>
		<?php } ?>
	</div>
	
	<?php if (!empty($_GET['alert'])) { ?>
		<div class="callout alert">
			<?php echo wp_kses_post($_GET['alert']); ?>
		</div>
	<?php } ?>
	
	<div class="text-center">
		<?php wt_fb_button(true); ?>
		
		<div class="wt-horizontal-lines wt-gutter-vertical-x2">OR</div>
	</div>
	
	<form data-route="wt/v1/crm/login" method="post" autocomplete="off">
		<div class="callout <?php echo $verified ? 'success' : 'hide'; ?>">
			<?php echo $verified ? 'Your email has been verified. Please log in to your account.' : ''; ?>
		</div>
		
		<label>
			Email or Mobile Number
			<input type="text" name="email_or_mobile" required>
		</label>
		
		<label>
			Password
			<input type="password" name="Password" autocomplete="off" required>
		</label>
		
		<?php
//		if (function_exists('wt_captcha'))
//			wt_captcha();
		?>
		
		<div class="text-center">
			<button type="submit" class="button secondary expanded wt-upper" data-submit="Log in" data-submitting="Logging in..." data-submitted="Logged in. Redirecting...">Log in</button>
		</div>
	</form>
	
	<a href="<?php echo home_url('forgot-password'); ?>" class="wt-text-hover">Retrieve Password<!--Forgot Password / Don’t know password?--></a><br>
	Not a member yet? <a href="<?php echo home_url('signup'); ?>" class="wt-text-hover">Sign up now.</a>
</div>

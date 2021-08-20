<?php
wt_crm_logged_out_only();

get_header();
?>

<div class="wt-gutter-vertical-x2 wt-gutter-half wt-account wt-container-small">
	<?php
	while (have_posts())
	{
		the_post();
		?>
		
		<h1 class="wt-margin-bottom wt-h3 wt-margin-bottom">
			<?php /*if (isset($_GET['reset'])) { ?>
				Reset Password
			<?php } else { ?>
			<?php }*/ ?>
			<?php the_title(); ?>
		</h1>
		
		<?php the_content(); ?>
		
		<form data-route="wt/v1/crm/forgot-password" method="post" autocomplete="off">
			<div class="callout hide" data-success="An e-mail/SMS has been sent to you to retrieve your password"></div>
			
			<label>
				Email Address
				<input type="email" name="Email" placeholder="Please fill in your email address" -autocomplete="email">
			</label>
			
			<label>
				Mobile
				<input type="tel" name="MobileNo" title="Mobile Number" placeholder="Please fill in your mobile number" pattern="\d*" minlength="2" maxlength="17" -autocomplete="tel">
			</label>
			
			
			<?php //wt_mobile_fieldset([], false); ?>
			
			<?php
			if (function_exists('wt_captcha'))
				wt_captcha();
			?>
			
			<div class="text-center">
				<button type="submit" class="button secondary">Enter</button>
			</div>
			
			<small>Didn't receive an email? Check your spam folder, or reach out to us at <a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a></small>
		</form>
		
		<?php
	}
	?>
</div>

<?php get_footer();

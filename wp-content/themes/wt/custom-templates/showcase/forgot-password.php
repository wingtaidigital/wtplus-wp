<?php
defined('ABSPATH') || exit;

wt_logged_out_only();

get_header();
?>

<div class="wt-gutter-vertical-x2 wt-gutter-half wt-account wt-container-small">
	<?php
	while (have_posts())
	{
		the_post();
		?>
		
		<h1 class="wt-h3 wt-margin-bottom">
			<?php if (isset($_GET['reset'])) { ?>
				Reset Password
			<?php } else { ?>
				<?php the_title(); ?>
			<?php } ?>
		</h1>
		
		<form data-route="wt/v1/forgot-password" method="post" autocomplete="off">
			<div class="callout <?php echo empty($_GET['alert']) ? 'hide' : 'alert' ?>" data-success="Please check your email for the confirmation link.">
				<?php echo empty($_GET['alert']) ? '' : sanitize_text_field($_GET['alert']); ?>
			</div>
			
			<label>
				Email
				<input type="email" name="user_login" -autocomplete="email" required>
			</label>
			
			<?php do_action( 'lostpassword_form' ); ?>
			
			<div class="text-center">
				<button type="submit" class="button secondary expanded">Submit</button>
			</div>
		</form>
		
		<?php
	}
	?>
</div>

<?php get_footer();

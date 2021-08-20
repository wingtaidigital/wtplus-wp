<?php
defined('ABSPATH') || exit;

wt_logged_out_only();

//list( $rp_path ) = explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) );
$rp_path = '/';

$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
if ( isset( $_GET['key'] ) ) {
	$value = sprintf( '%s:%s', wp_unslash( $_GET['login'] ), wp_unslash( $_GET['key'] ) );
	setcookie( $rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
	wp_safe_redirect( remove_query_arg( array( 'key', 'login' ) ) );
	exit;
}

if ( isset( $_COOKIE[ $rp_cookie ] ) && 0 < strpos( $_COOKIE[ $rp_cookie ], ':' ) ) {
	list( $rp_login, $rp_key ) = explode( ':', wp_unslash( $_COOKIE[ $rp_cookie ] ), 2 );
	$user = check_password_reset_key( $rp_key, $rp_login );
} else {
	$user = false;
}

if ( ! $user || is_wp_error( $user ) ) {
	setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
	
	if ( $user && $user->get_error_code() === 'expired_key' )
		$error = 'Your password reset link has expired. Please request a new link below.';
	else
		$error = 'Your password reset link appears to be invalid. Please request a new link below.';
	
	wp_redirect(add_query_arg('alert', urlencode($error), wp_lostpassword_url()));
	
	exit;
}

get_header();
?>

<div class="wt-gutter-vertical-x2 wt-gutter-half wt-account wt-container-small">

<?php
while (have_posts())
{
	the_post();
	?>
	
	<h1 class="wt-h3 wt-margin-bottom"><?php the_title(); ?></h1>
			
	<form data-route="wt/v1/reset-password" method="post" autocomplete="off">
		<div class="callout hide" data-success="Your password has been updated. <a data-open='showcase-login-modal'>Log in now.</a>"></div>
		
		<?php get_template_part('template-parts/form/field-password') ?>
		
		<div class="text-center">
			<button type="submit" class="button secondary expanded">Reset Password</button>
		</div>
		
		<input type="hidden" name="rp_key" value="<?php echo esc_attr($rp_key); ?>">
	</form>
	
	<?php
}
?>

</div>

<?php get_footer();

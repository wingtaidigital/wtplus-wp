<?php
if ( !function_exists('wp_password_change_notification') ) :
	/**
	 * Notify the blog admin of a user changing password, normally via email.
	 *
	 * @since 2.7.0
	 *
	 * @param WP_User $user User object.
	 */
	function wp_password_change_notification( $user ) {
		// send a copy of password change notification to the admin
		// but check to see if it's the admin whose password we're changing, and skip this
//		if ( 0 !== strcasecmp( $user->user_email, get_option( 'admin_email' ) ) ) {
//			/* translators: %s: user name */
//			$message = sprintf( __( 'Password changed for user: %s' ), $user->user_login ) . "\r\n";
//			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
//			// we want to reverse this for the plain text arena of emails.
//			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
//			/* translators: %s: site title */
//			wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] Password Changed' ), $blogname ), $message );
//		}
	}
endif;



if ( !function_exists('wp_new_user_notification') ) :
	/**
	 * Email login credentials to a newly-registered user.
	 *
	 * A new user registration notification is also sent to admin email.
	 *
	 * @since 2.0.0
	 * @since 4.3.0 The `$plaintext_pass` parameter was changed to `$notify`.
	 * @since 4.3.1 The `$plaintext_pass` parameter was deprecated. `$notify` added as a third parameter.
	 * @since 4.6.0 The `$notify` parameter accepts 'user' for sending notification only to the user created.
	 *
	 * @global wpdb         $wpdb      WordPress database object for queries.
	 * @global PasswordHash $wp_hasher Portable PHP password hashing framework instance.
	 *
	 * @param int    $user_id    User ID.
	 * @param null   $deprecated Not used (argument deprecated).
	 * @param string $notify     Optional. Type of notification that should happen. Accepts 'admin' or an empty
	 *                           string (admin only), 'user', or 'both' (admin and user). Default empty.
	 */
	function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
		if ( $deprecated !== null ) {
			_deprecated_argument( __FUNCTION__, '4.3.1' );
		}
		
		global $wpdb, $wp_hasher;
		$user = get_userdata( $user_id );
		
		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		
//		if ( 'user' !== $notify ) {
//			$switched_locale = switch_to_locale( get_locale() );
//			$message  = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
//			$message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
//			$message .= sprintf( __( 'Email: %s' ), $user->user_email ) . "\r\n";
//
//			@wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), $blogname ), $message );
//
//			if ( $switched_locale ) {
//				restore_previous_locale();
//			}
//		}
		
		// `$deprecated was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notification.
		if ( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) {
			return;
		}
		
		// Generate something random for a password reset key.
		$key = wp_generate_password( 20, false );
		
		/** This action is documented in wp-login.php */
		do_action( 'retrieve_password_key', $user->user_login, $key );
		
		// Now insert the key, hashed, into the DB.
		if ( empty( $wp_hasher ) ) {
			$wp_hasher = new PasswordHash( 8, true );
		}
		$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );
		
		$switched_locale = switch_to_locale( get_user_locale( $user ) );
		
		$message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
		$message .= __('To set your password, visit the following address:') . "\r\n\r\n";
		$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";
		
		$message .= wp_login_url() . "\r\n";
		
		wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
		
		if ( $switched_locale ) {
			restore_previous_locale();
		}
	}
endif;

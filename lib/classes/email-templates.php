<?php
/**
 * Initialize and return an appropriate email template to use
 * @since 1.0
 * @subpackage YIKES Inc Custom Login
 */
class YIKES_Email_Templates {

	// Store our options for later use
	private $options;

	// Constructor
	public function __construct( $options ) {
		// Setup our options variable
		$this->options = $options;

		// set HTML email filters for all emails
		add_filter( 'wp_mail_content_type', function( $content_type ) {
			return 'text/html';
		});
	}
	/**
	 * Build the proper emial template
	 * @param  string $template_name      [description]
	 * @param  string $key                [description]
	 * @param  string $user_login         [description]
	 * @param  string $reset_password_url [description]
	 * @return string                     HTML markup for the email to be sent
	 */
	public function send_password_reset_email( $key, $user_login, $reset_pass_url ) {
		// include the password reset email
		include_once( YIKES_CUSTOM_LOGIN_PATH . 'templates/email/password-reset.php' );
		// include_once( YIKES_CUSTOM_LOGIN_PATH . 'templates/email/welcome.php' );
	}

	/**
	 * Email login credentials to a newly-registered user.
	 *
	 * A new user registration notification is also sent to admin email.
	 *
	 * @since 2.0.0
	 * @since 4.3.0 The `$plaintext_pass` parameter was changed to `$notify`.
	 * @since 4.3.1 The `$plaintext_pass` parameter was deprecated. `$notify` added as a third parameter.
	 *
	 * @global wpdb         $wpdb      WordPress database object for queries.
	 * @global PasswordHash $wp_hasher Portable PHP password hashing framework instance.
	 *
	 * @param int    $user_id    User ID.
	 * @param null   $deprecated Not used (argument deprecated).
	 * @param string $notify     Optional. Type of notification that should happen. Accepts 'admin' or an empty
	 *                           string (admin only), or 'both' (admin and user). Default empty.
	 */
	public function send_new_user_notifications( $user_id, $key ) {
		$user = new WP_User( $user_id );

		$user_login = stripslashes( $user->user_login );
		$user_email = stripslashes( $user->user_email );

		$message  = sprintf( __( 'New user registration on your blog %s:', 'yikes-inc-custom-login' ), get_option( 'blogname' ) ) . "\r\n\r\n";
		$message .= sprintf( __( 'Username: %s', 'yikes-inc-custom-login' ), $user_login ) . "\r\n\r\n";
		$message .= sprintf( __( 'E-mail: %s', 'yikes-inc-custom-login' ), $user_email ) . "\r\n";

		// Mail a notification to the admin
		wp_mail(
			get_option( 'admin_email' ),
			sprintf(
				__( '[%s] New User Registration', 'yikes-inc-custom-login' ),
				get_option( 'blogname' )
			),
			$message
		);

		// Setup our new user login URL
		$new_user_login_url = add_query_arg( array(
			'action' => 'rp',
			'key' => $key,
			'login' => rawurlencode( $user->user_login ),
		), esc_url( network_site_url( 'wp-login.php', 'login' ) ) );

		// Inclde our custom 'Welcome' email template
		ob_start();
		include_once( YIKES_CUSTOM_LOGIN_PATH . 'templates/email/welcome.php' );
		$message = ob_get_contents();
		ob_get_clean();

		// Email the user
		wp_mail(
			$user_email,
			sprintf(
				__( '[%s] Your username and password', 'yikes-inc-custom-url' ),
				get_option( 'blogname' )
			),
			$message
		);
	}
}

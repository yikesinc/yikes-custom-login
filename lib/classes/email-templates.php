<?php
/**
 * Initialize and return an appropriate email template to use
 * @since 1.0
 * @subpackage YIKES Inc Custom Login
 */
class YIKES_Email_Templates {

	// Constructor
	public function __construct() {
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
	public static function build_email_template( $template_name, $key, $user_login, $reset_pass_url ) {
		// switch over our template types
		switch ( $template_name ) {
			case 'password-reset':
				include_once( YIKES_CUSTOM_LOGIN_PATH . 'templates/email/password-reset.php' );
				break;
			case 'welcome':
				include_once( YIKES_CUSTOM_LOGIN_PATH . 'templates/email/welcome.php' );
				break;
			default:
				break;
		}
	}
}

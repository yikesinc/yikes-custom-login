<?php
/**
 * Helper functions to aid with custom login
 * Note: all of the functions are pluggable, and can be overridden in theme functions.php
 * @since 1.0
 */

/**
 * Get the account info page URL
 *
 * @return string URL of the account info page, or home URL if not set
 */
if ( ! function_exists( 'get_yikes_account_info_page' ) ) {
	function get_yikes_account_info_page() {
		$options = get_yikes_custom_login_options();
		/**
		 * If the account info page is not set, or
		 * if the account info page is set to the same as the member login page,
		 * redirect to homepage to prevent redirect loop
		 */
		return ( 0 === $options['account_info_page'] || ( $options['account_info_page'] === $options['member_login_page']  ) ) ? site_url() : esc_url( get_the_permalink( $options['account_info_page'] ) );
	}
}

/**
 * Get the login page URL
 *
 * @return string URL of the account info page, or home URL if not set
 */
if ( ! function_exists( 'get_yikes_login_page' ) ) {
	function get_yikes_login_page() {
		$options = get_yikes_custom_login_options();
		/**
		 * If the account info page is not set, or
		 * if the account info page is set to the same as the member login page,
		 * redirect to homepage to prevent redirect loop
		 */
		return ( 0 === $options['login_page'] ) ? wp_login_url() : esc_url( get_the_permalink( $options['login_page'] ) );
	}
}

/**
 * Get the account info page URL
 *
 * @return string URL of the account info page, or home URL if not set
 */
if ( ! function_exists( 'get_yikes_registration_page' ) ) {
	function get_yikes_registration_page() {
		$options = get_yikes_custom_login_options();
		/**
		 * If the account info page is not set, or
		 * if the account info page is set to the same as the member login page,
		 * redirect to homepage to prevent redirect loop
		 */
		return ( 0 === $options['register_page'] ) ? false : esc_url( get_the_permalink( $options['register_page'] ) );
	}
}

/**
 * Helper function to get the custom login options
 * @return array The custom login options.
 * @since 1.0
 */
function get_yikes_custom_login_options() {
	return get_option( 'yikes_custom_login', array(
		'plugin_setup' => false,
		'admin_redirect' => 1,
		'restrict_dashboard_access' => 1,
		'password_strength_meter' => 1,
		'notice_animation' => 'yikes-fadeInDown',
		'powered_by_yikes' => 1,
		'register_page' => null,
		'login_page' => null,
		'account_info_page' => null,
		'password_lost_page' => null,
		'pick_new_password_page' => null,
		'recaptcha_site_key' => false,
		'recaptcha_secret_key' => false,
	) );
}

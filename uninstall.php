<?php
/**
 * Main Uninstall File
 * 		We'll clean up after ourselves here, including removing all of our
 * 		plugin options, theme modifications (customizer) and anything else
 * 		that may have been created while using the plugin.
 * 	@since 1.0
 */

// If uninstall is not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Delete our entire options array
delete_option( 'yikes_custom_login' );

// Setup an array of theme mods to delete
$theme_modification_array = array(
	/* Logo Panel */
	'login_logo_section',
	/* Login Container Panel */
	'login_container_background',
	'login_container_opacity',
	'login_container_border_color',
	'login_container_border_style',
	'login_container_border_opacity',
	'login_container_border_width',
	'login_container_border_radius',
	'login_container_text_color',
	'login_container_sign_in_button_text',
	'login_container_full_width_sign_in_button',
	'login_container_link_color',
	'login_container_hide_forgot_password_link',
	'login_container_hide_register_link',
	/* Background Panel */
	'login_background',
	'login_background_size',
	'login_background_position',
	'login_background_repeat',
	/* Custom Scripts & Styles Panel */
	'yikes_login_custom_styles',
	'yikes_login_custom_scripts',
);

// Loop over our theme mods, and remove them
foreach ( $theme_modification_array as $mod_name ) {
	remove_theme_mod( $mod_name );
}

/**
 * Delete our custom pages created during plugin activation
 */
$plugin_options = get_option( 'yikes_custom_login', array(
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

$template_page_ids = array(
	$plugin_options['login_page'],
	$plugin_options['pick_new_password_page'],
	$plugin_options['password_lost_page'],
	$plugin_options['register_page'],
);

// Loop over and delete our pages
foreach ( $template_page_ids as $page_id ) {
	// Ensure we have a post ID to work with
	if ( is_numeric( $post_id ) ) {
		// Force delete, bypass trash
		wp_delete_post( $page_id, true );
	}
}

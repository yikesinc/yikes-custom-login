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
$template_page_ids = array(
	$this->options['login_page'],
	$this->options['pick_new_password_page'],
	$this->options['password_lost_page'],
	$this->options['register_page'],
);

// Loop over and delete our pages
foreach ( $template_page_ids as $page_id ) {
	// Force delete, bypass trash
	wp_delete_post( $page_id, true );
}

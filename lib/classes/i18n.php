<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.2.0
 */
class YIKES_Custom_Login_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.2.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'custom-wp-login',
			false,
			YIKES_CUSTOM_LOGIN_PATH . 'languages/'
		);

	}



}

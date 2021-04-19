<?php
/**
 * Plugin Name:       YIKES Custom Login
 * Plugin URI:        https://yikesplugins.com/
 * Description:       A plugin that replaces the WordPress login flow with custom pages.
 * Version:           1.2.4
 * Author:            YIKES, Inc.
 * Author URI:        http://www.yikesinc.com
 * License:           GPL-2.0+
 * Text Domain:       custom-wp-login
 * Domain Path:       /languages
 */

class YIKES_Custom_Login {

	// Private variable to store our options
	private $options, $option_class;

	/**
	 * Initializes the plugin.
	 *
	 * To keep the initialization fast, only add filter and action
	 * hooks in the constructor.
	 */
	public function __construct() {

		// Define constants
		if ( ! defined( 'YIKES_CUSTOM_LOGIN_VERSION' ) ) {
			define( 'YIKES_CUSTOM_LOGIN_VERSION', '1.2.4' );
		}
		if ( ! defined( 'YIKES_CUSTOM_LOGIN_PATH' ) ) {
			define( 'YIKES_CUSTOM_LOGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		}
		if ( ! defined( 'YIKES_CUSTOM_LOGIN_URL' ) ) {
			define( 'YIKES_CUSTOM_LOGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		}

		// Store our options
		$this->options = self::get_yikes_custom_login_options();
		$options       = $this->options;

		// Include our helper functions
		include_once plugin_dir_path( __FILE__ ) . 'lib/classes/helpers.php';

		// Include our custom widgets class
		include_once plugin_dir_path( __FILE__ ) . 'lib/classes/login-widget.php';

		// Include our customizer class
		include_once plugin_dir_path( __FILE__ ) . 'lib/classes/customizer.php';

		// Include our i18n class
		include_once plugin_dir_path( __FILE__ ) . 'lib/classes/i18n.php';

		// Set localization
		$this->set_locale();

		// Restrict admin dashboard access to only admins ('manage_options' capability)
		add_action( 'admin_init', array( $this, 'yikes_restrict_admin_dashboard' ) );

		// Redirects
		add_action( 'login_form_login', array( $this, 'redirect_to_custom_login' ) );
		add_filter( 'authenticate', array( $this, 'maybe_redirect_at_authenticate' ), 101, 3 );
		add_filter( 'login_redirect', array( $this, 'redirect_after_login' ), 10, 3 );
		add_action( 'wp_logout', array( $this, 'redirect_after_logout' ) );
		add_action( 'init', array( $this, 'redirect_logged_in_users' ) );

		add_action( 'login_form_register', array( $this, 'redirect_to_custom_register' ) );
		add_action( 'login_form_lostpassword', array( $this, 'redirect_to_custom_lostpassword' ) );
		add_action( 'login_form_rp', array( $this, 'redirect_to_custom_password_reset' ) );
		add_action( 'login_form_resetpass', array( $this, 'redirect_to_custom_password_reset' ) );

		// Handlers for form posting actions
		add_action( 'login_form_register', array( $this, 'do_register_user' ) );
		add_action( 'login_form_lostpassword', array( $this, 'do_password_lost' ) );
		add_action( 'login_form_rp', array( $this, 'do_password_reset' ) );
		add_action( 'login_form_resetpass', array( $this, 'do_password_reset' ) );
		add_action( 'init', array( $this, 'do_update_user_profile' ) );

		// Other customizations
		add_filter( 'retrieve_password_message', array( $this, 'replace_retrieve_password_message' ), 10, 4 );

		// Setup
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_yikes_custom_login_options_scripts_and_styles' ) );

		// Frontend setup
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_yikes_custom_login_frontend_scripts_and_styles' ) );

		// Shortcodes
		add_shortcode( 'custom-login-form', array( $this, 'render_login_form' ) );
		add_shortcode( 'account-info', array( $this, 'render_account_info_form' ) );
		add_shortcode( 'custom-register-form', array( $this, 'render_register_form' ) );
		add_shortcode( 'custom-password-lost-form', array( $this, 'render_password_lost_form' ) );
		add_shortcode( 'custom-password-reset-form', array( $this, 'render_password_reset_form' ) );

		// Include our settings page
		if ( ! class_exists( 'YIKES_Login_Settings' ) ) {
			include_once plugin_dir_path( __FILE__ ) . 'lib/classes/options.php';
		}

		// Include our custom page metaboxes
		include_once plugin_dir_path( __FILE__ ) . 'lib/classes/metaboxes.php';

		/* Clear our transient each time a page is updated/published - clears the pages settings dropdowns */
		add_action( 'save_post', array( $this, 'clear_transient_on_page_save' ), 10, 3 );

		/* Define our custom page template */
		add_filter( 'page_template', array( $this, 'yikes_custom_login_page_template' ) );

		/* Add some login page advertising text */
		add_action( 'yikes-custom-login-login-page-bottom', array( $this, 'yikes_custom_login_page_text' ) );
		add_action( 'yikes-custom-login-password-lost-page-bottom', array( $this, 'yikes_custom_login_page_text' ) );
		add_action( 'yikes-custom-login-pick-new-password-page-bottom', array( $this, 'yikes_custom_login_page_text' ) );
		add_action( 'yikes-custom-login-user-registration-page-bottom', array( $this, 'yikes_custom_login_page_text' ) );

		/* Add back links to the password reset form */
		add_action( 'yikes-custom-login-password-lost-page-after-form', array( $this, 'yikes_custom_password_lost_page_backlinks' ) );

		/* Append the custom site logo to the login form, registration form, password lost form and appropriate emails */
		add_action( 'yikes-custom-login-branding', array( $this, 'yikes_custom_login_generate_branding_logo' ) );

		/* Custom text below forms */
		add_action( 'yikes-custom-login-user-registration-page-after-form', array( $this, 'yikes_custom_login_append_already_a_member_text' ) );

		/* Load custom customizer styles where set */
		add_action( 'wp_enqueue_scripts', array( $this, 'yikes_custom_login_generate_customizer_styles' ) );

		/* Custom Login Sign In Button Text */
		add_filter( 'gettext', array( $this, 'yikes_filter_sign_in_button_text' ), 10, 3 );

		/* Hide the admin toolbar from all users who are not admins */
		add_action( 'init', array( $this, 'yikes_custom_login_hide_admin_toolbar' ) );

		/* Display an admin notice letting the user know pages were created */
		add_action( 'admin_notices', array( $this, 'yikes_display_page_creation_notice' ) );

		/* Custom 'Customize' row action link on the login page */
		add_filter( 'page_row_actions', array( $this, 'yikes_login_page_action_links' ), 10, 2 );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.2
	 */
	private function set_locale() {

		$plugin_i18n = new YIKES_Custom_Login_i18n();

		add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ) );
	}

	/**
	 * Hide the admin toolbar from all users who are NOT admins
	 *
	 * @since 1.0
	 */
	public function yikes_custom_login_hide_admin_toolbar() {
		/* Hide the admin bar on the frontend -- unless the user is an admin */
		if ( ! current_user_can( apply_filters( 'yikes-login-admin-toolbar-cap', 'manage_options' ) ) ) {
			add_filter( 'show_admin_bar', '__return_false' );
		}
	}

	/**
	 * Custom filter to alter the 'Sign In' button text
	 *
	 * @param  string $translation Original translation string.
	 * @param  string $text        Text to look for.
	 * @return string              The final altered string.
	 */
	public function yikes_filter_sign_in_button_text( $translation, $text, $domain ) {
		// If we're admin side - abort
		if ( is_admin() ) {
			return $translation;
		}
		// Alter 'Sign In' button text
		if ( 'Sign In' === $text ) {
			$signin_text = get_theme_mod( 'login_container_sign_in_button_text', false );
			if ( $signin_text ) {
				return $signin_text;
			}
			return $translation;
		}
		return $translation;
	}

	/**
	 * Restirct admin dashboard access for non-admin users
	 *
	 * @since 1.0
	 */
	public function yikes_restrict_admin_dashboard() {
		/* If the user has elected to not restrict dashboard access, abort */
		if ( 0 === $this->options['restrict_dashboard_access'] ) {
			return;
		}
		/* Allow users to decide who can access the dashboard by capability */
		$user_cap = apply_filters( 'yikes-custom-login-restrict-dashboard-capability', 'manage_options' );
		if ( ! current_user_can( $user_cap ) && '/wp-admin/admin-ajax.php' !== $_SERVER['PHP_SELF'] ) {
			wp_redirect( site_url() );
		}
	}

	/**
	 * Enqueue options page scripts & styles
	 *
	 * @since 1.0
	 */
	public function enqueue_yikes_custom_login_options_scripts_and_styles() {
		$screen = get_current_screen();
		// Confirm we are on the options page
		if ( isset( $screen ) && isset( $screen->base ) && 'settings_page_yikes-custom-login' === $screen->base ) {
			// select2 css
			wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . '/lib/css/min/select2.min.css', array( 'yikes-admin-styles' ), YIKES_CUSTOM_LOGIN_VERSION );
			// select2 js
			wp_enqueue_script( 'select2', plugin_dir_url( __FILE__ ) . '/lib/js/min/select2.min.js', array( 'jquery' ), YIKES_CUSTOM_LOGIN_VERSION, true );
			// File uploader for the branding tab
			wp_enqueue_media();
			// Options page scriptts
			wp_enqueue_script( 'yikes-options-script', plugin_dir_url( __FILE__ ) . '/lib/js/min/yikes-custom-login-options.min.js', array( 'select2' ), YIKES_CUSTOM_LOGIN_VERSION, true );
		}
	}

	/**
	 * Enqueue front end scripts
	 *
	 * @since 1.0
	 */
	public function enqueue_yikes_custom_login_frontend_scripts_and_styles() {
		// Load the login page script
		if ( is_page( $this->options['login_page'] ) || is_page( $this->options['register_page'] ) ||
		is_page( $this->options['password_lost_page'] ) || is_page( $this->options['pick_new_password_page'] ) ) {
			wp_enqueue_style( 'yikes-custom-login-public', plugin_dir_url( __FILE__ ) . '/lib/css/min/yikes-custom-login-public.min.css', array(), YIKES_CUSTOM_LOGIN_VERSION );
			wp_enqueue_script( 'yikes-login-page-script', plugin_dir_url( __FILE__ ) . '/lib/js/min/yikes-login-page.min.js', array( 'jquery' ), YIKES_CUSTOM_LOGIN_VERSION, true );
		}
		// Load REcaptcha script on all pages of our plugin
		if ( is_page( $this->options['login_page'] ) || is_page( $this->options['register_page'] ) ||
		is_page( $this->options['password_lost_page'] ) || is_page( $this->options['pick_new_password_page'] ) || is_page( $this->options['account_info_page'] ) ) {
			$site_locale = get_locale();
			$lang        = 'en';
			if ( $site_locale && explode( '_', $site_locale ) ) {
				$split_locale = explode( '_', $site_locale );
				$lang         = $split_locale[0];
			}
			// enqueue recaptcha (if it's enabled)
			wp_enqueue_script( 'recaptcha-api-js', apply_filters( 'yikes-custom-login-recaptcha-script-url', 'https://www.google.com/recaptcha/api.js?hl=' . apply_filters( 'yikes-custom-login-recaptcha-language', $lang ) ), array(), 'all', true );
		}
	}

	/**
	 * Helper function to get the custom login options
	 *
	 * @return array The custom login options.
	 * @since 1.0
	 */
	public static function get_yikes_custom_login_options() {
		return get_option(
			'yikes_custom_login',
			array(
				'plugin_setup'              => false,
				'admin_redirect'            => 1,
				'restrict_dashboard_access' => 1,
				'password_strength_meter'   => 1,
				'minimum_password_strength' => 'strong',
				'notice_animation'          => 'yikes-fadeInDown',
				'powered_by_yikes'          => 1,
				'register_page'             => null,
				'login_page'                => null,
				'account_info_page'         => null,
				'password_lost_page'        => null,
				'pick_new_password_page'    => null,
				'recaptcha_site_key'        => false,
				'recaptcha_secret_key'      => false,
			)
		);
	}

	//
	// REDIRECT FUNCTIONS
	//
	/**
	 * Plugin activation hook.
	 *
	 * Creates all WordPress pages needed by the plugin.
	 *
	 * @since 1.0
	 */
	public static function plugin_activated() {
		// store static count
		$page_created_count = 0;
		$page_created_array = array();
		// Information needed for creating the plugin's pages
		$page_definitions = array(
			apply_filters( 'yikes-custom-login-login-slug', 'member-login' ) => array(
				'title'   => __( 'Sign In', 'custom-wp-login' ),
				'content' => '[custom-login-form]',
			),
			apply_filters( 'yikes-custom-login-account-slug', 'member-account' ) => array(
				'title'   => __( 'Your Account', 'custom-wp-login' ),
				'content' => '[account-info]',
			),
			apply_filters( 'yikes-custom-login-register-slug', 'member-register' ) => array(
				'title'   => __( 'Registration', 'custom-wp-login' ),
				'content' => '[custom-register-form]',
			),
			apply_filters( 'yikes-custom-login-password-lost-slug', 'member-password-lost' ) => array(
				'title'   => __( 'Forgot Your Password?', 'custom-wp-login' ),
				'content' => '[custom-password-lost-form]',
			),
			apply_filters( 'yikes-custom-login-password-reset-slug', 'member-password-reset' ) => array(
				'title'   => __( 'Pick a New Password', 'custom-wp-login' ),
				'content' => '[custom-password-reset-form]',
			),
		);
		// Store our options
		$plugin_options = self::get_yikes_custom_login_options();
		// Loop over the pages
		foreach ( $page_definitions as $slug => $page ) {
			// Check that the page doesn't exist already
			$query = new WP_Query( 'pagename=' . $slug );
			if ( ! $query->have_posts() ) {
				// Add the page using the data from the array above
				$page_id = wp_insert_post(
					array(
						'post_content'   => $page['content'],
						'post_name'      => $slug,
						'post_title'     => $page['title'],
						'post_status'    => 'publish',
						'post_type'      => 'page',
						'ping_status'    => 'closed',
						'comment_status' => 'closed',
					)
				);
				// if the page was created -- increment
				if ( $page_id ) {
					$page_created_count++;
					$page_created_array[] = array(
						'id'         => $page_id,
						'edit_link'  => get_edit_post_link( $page_id ),
						'page_title' => get_the_title( $page_id ),
					);
				}
			} else {
				// Page IDs existed, update the ID values
				$page_obj = get_page_by_path( $slug, OBJECT, 'page' );
				$page_id  = ( $page_obj && isset( $page_obj->ID ) ) ? $page_obj->ID : 1;
			}
			// Update our option so we can use it on the options page & in our redirections
			switch ( $slug ) {
				case 'member-login':
					$plugin_options['login_page'] = $page_id;
					break;
				case 'member-account':
					$plugin_options['account_info_page'] = $page_id;
					break;
				case 'member-register':
					$plugin_options['register_page'] = $page_id;
					break;
				case 'member-password-lost':
					$plugin_options['password_lost_page'] = $page_id;
					break;
				case 'member-password-reset':
					$plugin_options['pick_new_password_page'] = $page_id;
					break;
				default:
					break;
			}
			// Update our options with the new page ID values
			update_option( 'yikes_custom_login', $plugin_options );
			// Update the post meta to utilize full width page templates out of the box
			update_post_meta( $page_id, '_full_width_page_template', 1 );
		}
		// Update our options
		if ( 0 < $page_created_count ) {
			// Store our options for later use
			update_option( 'yikes_custom_login_pages_created_count', $page_created_count );
			update_option( 'yikes_custom_login_pages_data_array', $page_created_array );
		}
	}

	/**
	 * Display a notice back to the user letting them know that some pages
	 * were created, and add links back to those new pages
	 *
	 * @return string HTML markup for our admin notice
	 * @since 1.0
	 */
	public function yikes_display_page_creation_notice() {
		$string        = ''; // empty string
		$pages_created = get_option( 'yikes_custom_login_pages_data_array', false );
		$page_count    = get_option( 'yikes_custom_login_pages_created_count', false );
		// abort if no settings were found
		if ( ! $pages_created || ! $page_count ) {
			return;
		}
		$page_created_text = sprintf( _n( '%s page successfully created.', '%s pages successfully created.', $page_count, 'custom-wp-login' ), $page_count );
		$end               = count( $pages_created );
		foreach ( $pages_created as $page_created_data ) {
			$string .= '<a href="' . esc_url( $page_created_data['edit_link'] ) . '">' . esc_attr( $page_created_data['page_title'] ) . '</a>';
			if ( 0 !== --$end ) {
				$string .= ' | ';
			}
		}
		printf(
			'<div class="notice notice-success"><p>%1$s</p><p>%2$s</p></div>',
			wp_kses_post( $page_created_text ),
			wp_kses_post( $string )
		);
		// Delete our options once they have been displayed to the user, so to not repeat this notice
		delete_option( 'yikes_custom_login_pages_created_count' );
		delete_option( 'yikes_custom_login_pages_data_array' );
	}

	/**
	 * Redirect the user to the custom login page instead of wp-login.php.
	 *
	 * @since 1.0
	 */
	public function redirect_to_custom_login() {
		if ( 'GET' === $_SERVER['REQUEST_METHOD'] ) {

			if ( is_user_logged_in() ) {
				$this->redirect_logged_in_user();
				exit;
			}

			// The rest are redirected to the login page
			$login_url = esc_url( get_the_permalink( $this->options['login_page'] ) );
			if ( ! empty( $_REQUEST['redirect_to'] ) ) {
				$login_url = add_query_arg( 'redirect_to', $_REQUEST['redirect_to'], $login_url );
			}

			if ( ! empty( $_REQUEST['checkemail'] ) ) {
				$login_url = add_query_arg( 'checkemail', $_REQUEST['checkemail'], $login_url );
			}

			wp_redirect( $login_url );
			exit;
		}
	}

	/**
	 * Add custom post row link on the 'Login' page, linking to the customizer
	 *
	 * @since 1.0
	 */
	public function yikes_login_page_action_links( $actions, $post_object ) {
		// Check if the 'Full Width' page template is active
		$active_template = ( get_post_meta( $this->options['login_page'], '_full_width_page_template', true ) ) ? true : false;
		// if the current post is not equal to the sign in page, abort
		if ( ! $active_template || $post_object->ID != $this->options['login_page'] ) {
			return $actions;
		}
		// build the customizer link
		$customizer_link                        = add_query_arg(
			array(
				'url' => esc_url( get_the_permalink( $this->options['login_page'] ) ),
			),
			esc_url_raw( admin_url( 'customize.php' ) )
		);
		$actions['yikes_login_customizer_link'] = '<a class="cgc_ub_edit_badge" href=" ' . esc_url( $customizer_link ) . '">' . __( 'Customize Login', 'custom-wp-login' ) . '</a>';
		return $actions;
	}
	/**
	 * Redirect the user after authentication if there were any errors.
	 *
	 * @param Wp_User|Wp_Error $user       The signed in user, or the errors that have occurred during login.
	 * @param string           $username   The user name used to log in.
	 * @param string           $password   The password used to log in.
	 *
	 * @return Wp_User|Wp_Error The logged in user, or error information if there were errors.
	 * @since 1.0
	 */
	public function maybe_redirect_at_authenticate( $user, $username, $password ) {
		// Check if the earlier authenticate filter (most likely,
		// the default WordPress authentication) functions have found errors
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			if ( is_wp_error( $user ) ) {
				$error_codes = join( ',', $user->get_error_codes() );

				$login_url = esc_url( get_the_permalink( $this->options['login_page'] ) );
				$login_url = add_query_arg( 'login', $error_codes, $login_url );

				wp_redirect( $login_url );
				exit;
			}
		}

		return $user;
	}

	/**
	 * Returns the URL to which the user should be redirected after the (successful) login.
	 *
	 * @param string           $redirect_to                    The redirect destination URL.
	 * @param string           $requested_redirect_to   The requested redirect destination URL passed as a parameter.
	 * @param  WP_User|WP_Error $user                               WP_User object if login was successful, WP_Error object otherwise.
	 *
	 * @return string Redirect URL
	 * @since 1.0.0
	 */
	public function redirect_after_login( $redirect_to, $requested_redirect_to, $user ) {
		$redirect_url  = home_url();
		$login_page_id = ( wp_get_referer() ) ? url_to_postid( wp_get_referer() ) : false;

		if ( ! isset( $user->ID ) ) {
			return $redirect_url;
		}

		// If admin_redirect is not set, abort
		if ( 0 === $this->options['admin_redirect'] ) {
			$logged_in_redirect_url = ( 0 === $this->options['account_info_page'] ) ? site_url() : esc_url( get_the_permalink( $this->options['account_info_page'] ) );
			wp_redirect( apply_filters( 'yikes-custom-login-redirect', $logged_in_redirect_url ) );
			return;
		}

		if ( user_can( $user, 'manage_options' ) ) {
			// Use the redirect_to parameter if one is set, otherwise redirect to admin dashboard.
			$redirect_url = ( '' === $requested_redirect_to ) ? admin_url() : $redirect_to;
		} else {
			// if the account info page has been disabled, do not redirect there
			// but instead redirect to the homepage/custom URL
			$redirect_url = ( 0 === $this->options['account_info_page'] ) ? add_query_arg( array( 'logged_in' => 'true' ), site_url() ) : esc_url( get_the_permalink( $this->options['account_info_page'] ) );
		}

		wp_safe_redirect( apply_filters( 'yikes-custom-login-login-redirect-url', $redirect_url, $login_page_id, $user ) );
		exit;
	}

	/**
	 * Redirect to custom login page after the user has been logged out.
	 *
	 * @since 1.0
	 */
	public function redirect_after_logout() {
		$redirect_url = add_query_arg(
			array(
				'logged_out' => 'true',
			),
			esc_url( get_the_permalink( $this->options['login_page'] ) )
		);
		wp_redirect( apply_filters( 'yikes-custom-login-logout-redirect-url', $redirect_url ) );
		exit;
	}

	/**
	 * Prevent access from certain pages for logged in users
	 * eg: 'Sign In'
	 *
	 * @return string redirect if logged in, or null of not
	 * @since 1.0
	 */
	public function redirect_logged_in_users() {
		// if the customizer is active, do not redirect
		// allowing users to customize the login form
		if ( is_customize_preview() ) {
			return;
		}
		$request_page_id = url_to_postid( site_url() . $_SERVER['REQUEST_URI'] );
		// If the current request is equal to the login page, and the user is loged in
		if ( $request_page_id === (int) $this->options['login_page'] ) {
			if ( is_user_logged_in() ) {
				if ( ! current_user_can( 'manage_options' ) ) {
					$account_info_page = get_yikes_account_info_page();
					wp_redirect( apply_filters( 'yikes-custom-login-login-redirect-url', $account_info_page, $request_page_id, wp_get_current_user() ) );
					exit;
				}
				wp_redirect( admin_url() );
				exit;
			}
		}
		return;
	}

	/**
	 * Redirects the user to the custom registration page instead of wp-login.php?action=register.
	 *
	 * @since 1.0
	 */
	public function redirect_to_custom_register() {
		if ( 'GET' === $_SERVER['REQUEST_METHOD'] ) {
			if ( is_user_logged_in() ) {
				$this->redirect_logged_in_user();
			} else {
				wp_redirect( esc_url( get_the_permalink( $this->options['register_page'] ) ) );
			}
			exit;
		}
	}

	/**
	 * Redirects the user to the custom "Forgot your password?" page instead of wp-login.php?action=lostpassword.
	 *
	 * @since 1.0
	 */
	public function redirect_to_custom_lostpassword() {
		if ( 'GET' === $_SERVER['REQUEST_METHOD'] ) {
			if ( is_user_logged_in() ) {
				$this->redirect_logged_in_user();
				exit;
			}

			wp_redirect( esc_url( get_the_permalink( $this->options['password_lost_page'] ) ) );
			exit;
		}
	}

	/**
	 * Redirects to the custom password reset page, or the login page if there are errors.
	 *
	 * @since 1.0
	 */
	public function redirect_to_custom_password_reset() {
		if ( 'GET' === $_SERVER['REQUEST_METHOD'] ) {
			// Verify key / login combo
			$user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
			if ( ! $user || is_wp_error( $user ) ) {
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					wp_redirect(
						add_query_arg(
							array(
								'login' => 'expiredkey',
							),
							esc_url( get_the_permalink( $this->options['login_page'] ) )
						)
					);
				} else {
					wp_redirect(
						add_query_arg(
							array(
								'login' => 'invalidkey',
							),
							esc_url( get_the_permalink( $this->options['login_page'] ) )
						)
					);
				}
				exit;
			}
			$redirect_url = esc_url( get_the_permalink( $this->options['pick_new_password_page'] ) );
			wp_redirect(
				add_query_arg(
					array(
						'login' => esc_attr( $_REQUEST['login'] ),
						'key'   => esc_attr( $_REQUEST['key'] ),
					),
					$redirect_url
				)
			);
			exit;
		}
	}


	//
	// FORM RENDERING SHORTCODES
	//

	/**
	 * A shortcode for rendering the login form.
	 *
	 * @param  array  $attributes  Shortcode attributes.
	 * @param  string $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 * @since 1.0
	 */
	public function render_login_form( $attributes, $content = null ) {

		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes         = shortcode_atts( $default_attributes, $attributes );

		// Pass the redirect parameter to the WordPress login functionality: by default,
		// don't specify a redirect, but if a valid redirect URL has been passed as
		// request parameter, use it.
		$attributes['redirect'] = '';
		if ( isset( $_REQUEST['redirect_to'] ) ) {
			$attributes['redirect'] = wp_validate_redirect( $_REQUEST['redirect_to'], $attributes['redirect'] );
		}

		// Error messages
		$errors = array();
		if ( isset( $_REQUEST['login'] ) ) {
			$error_codes = explode( ',', $_REQUEST['login'] );

			foreach ( $error_codes as $code ) {
				$errors[] = $this->get_error_message( $code );
			}
		}
		$attributes['errors'] = $errors;

		// Check if user just logged out
		$attributes['logged_out'] = ( isset( $_GET['logged_out'] ) && 'true' === $_GET['logged_out'] ) ? 'true' : 'false';

		// Check if the user just registered
		$attributes['registered'] = isset( $_REQUEST['registered'] );

		// Check if the user just requested a new password
		$attributes['lost_password_sent'] = ( isset( $_REQUEST['checkemail'] ) && 'confirm' === $_REQUEST['checkemail'] );

		// Check if user just updated password
		$attributes['password_updated'] = ( isset( $_REQUEST['password'] ) && 'changed' === $_REQUEST['password'] );

		// Store the username
		$attributes['username_value'] = isset( $_POST['log'] ) ? $_POST['log'] : '';

		// Render the login form using an external template
		return $this->get_template_html( 'login-form', $attributes );
	}

	/**
	 * A shortcode for rendering the login form.
	 *
	 * @param  array  $attributes  Shortcode attributes.
	 * @param  string $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 * @since 1.0
	 */
	public function render_account_info_form( $attributes, $content = null ) {
		// Error messages
		$errors = array();
		if ( isset( $_REQUEST['error'] ) ) {
			$error_codes = explode( ',', $_REQUEST['error'] );

			foreach ( $error_codes as $code ) {
				$errors[] = $this->get_error_message( $code );
			}
		}

		// Checking to see if shortcode has any props
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}

		$attributes['errors'] = $errors;
		// Render the login form using an external template
		return $this->get_template_html( 'account-info-form', $attributes );
	}

	/**
	 * A shortcode for rendering the new user registration form.
	 *
	 * @param  array  $attributes  Shortcode attributes.
	 * @param  string $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 * @since 1.0
	 */
	public function render_register_form( $attributes, $content = null ) {
		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes         = shortcode_atts( $default_attributes, $attributes );

		if ( is_user_logged_in() ) {
			$view_account_btn = ( 0 === $this->options['account_info_page'] ) ? '' : '<a href="' . esc_url( get_the_permalink( $this->options['account_info_page'] ) ) . '">' . __( 'View Account', 'custom-wp-login' ) . '</a>';
			return sprintf(
				'<p>' . esc_html_x( 'You are already signed in. %s', 'The "Account Info" page link.', 'custom-wp-login' ) . '<p>',
				wp_kses_post( $view_account_btn )
			);
		} elseif ( ! get_option( 'users_can_register' ) ) {
			return '<p>' . esc_html__( 'Registering new users is currently disabled.', 'custom-wp-login' ) . '</p>';
		} else {
			// Retrieve possible errors from request parameters
			$attributes['errors'] = array();
			if ( isset( $_REQUEST['register-errors'] ) ) {
				$error_codes = explode( ',', $_REQUEST['register-errors'] );

				foreach ( $error_codes as $error_code ) {
					$attributes['errors'][] = $this->get_error_message( $error_code );
				}
			}
			return $this->get_template_html( 'register-form', $attributes );
		}
	}

	/**
	 * A shortcode for rendering the form used to initiate the password reset.
	 *
	 * @param  array  $attributes  Shortcode attributes.
	 * @param  string $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 * @since 1.0
	 */
	public function render_password_lost_form( $attributes, $content = null ) {
		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes         = shortcode_atts( $default_attributes, $attributes );

		if ( is_user_logged_in() ) {
			return __( 'You are already signed in.', 'custom-wp-login' );
		} else {
			// Retrieve possible errors from request parameters
			$attributes['errors'] = array();
			if ( isset( $_REQUEST['errors'] ) ) {
				$error_codes = explode( ',', $_REQUEST['errors'] );

				foreach ( $error_codes as $error_code ) {
					$attributes['errors'][] = $this->get_error_message( $error_code );
				}
			}

			return $this->get_template_html( 'password-lost-form', $attributes );
		}
	}

	/**
	 * A shortcode for rendering the form used to reset a user's password.
	 *
	 * @param  array  $attributes  Shortcode attributes.
	 * @param  string $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 * @since 1.0
	 */
	public function render_password_reset_form( $attributes, $content = null ) {
		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes         = shortcode_atts( $default_attributes, $attributes );

		if ( is_user_logged_in() ) {
			return __( 'You are already signed in.', 'custom-wp-login' );
		} else {
			if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {
				$attributes['login'] = $_REQUEST['login'];
				$attributes['key']   = $_REQUEST['key'];

				// Error messages
				$errors = array();
				if ( isset( $_REQUEST['error'] ) ) {
					$error_codes = explode( ',', $_REQUEST['error'] );

					foreach ( $error_codes as $code ) {
						$errors[] = $this->get_error_message( $code );
					}
				}
				$attributes['errors'] = $errors;

				return $this->get_template_html( 'password-reset-form', $attributes );
			} else {
				return __( 'Invalid password reset link.', 'custom-wp-login' );
			}
		}
	}

	/**
	 * Renders the contents of the given template to a string and returns it.
	 *
	 * @param string $template_name The name of the template to render (without .php)
	 * @param array  $attributes    The PHP variables for the template
	 *
	 * @return string               The contents of the template.
	 * @since 1.0
	 */
	private function get_template_html( $template_name, $attributes = null ) {
		if ( ! $attributes ) {
			$attributes = array();
		}

		ob_start();

		do_action( 'yikes_custom_login_before_' . $template_name );

		/**
		 * Check if the user has created a custom template
		 * Note: Users can create a directory in their theme root '/yikes-custom-login/' and add templates ot it to override defaults.
		 */
		if ( file_exists( get_template_directory() . '/yikes-custom-login/' . $template_name . '.php' ) ) {
			require get_template_directory() . '/yikes-custom-login/' . $template_name . '.php';
		} else {
			require 'templates/' . $template_name . '.php';
		}

		do_action( 'yikes_custom_login_after_' . $template_name );

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}


	//
	// ACTION HANDLERS FOR FORMS IN FLOW
	//

	/**
	 * Handles the registration of a new user.
	 *
	 * Used through the action hook "login_form_register" activated on wp-login.php
	 * when accessed through the registration action.
	 *
	 * @since 1.0
	 */
	public function do_register_user() {
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			$redirect_url = esc_url( get_the_permalink( $this->options['register_page'] ) );

			// Verify our nonce
			if ( ! isset( $_POST['yikes_custom_login_register'] ) || ! wp_verify_nonce( $_POST['yikes_custom_login_register'], 'yikes_custom_login_register' ) ) {
				$redirect_url = add_query_arg( 'register-errors', 'nonce', $redirect_url );
				wp_redirect( $redirect_url );
				exit;
			}

			// unset the nonce values once validated
			unset( $_POST['yikes_custom_login_register'], $_POST['_wp_http_referer'] );

			if ( ! get_option( 'users_can_register' ) ) {
				// Registration closed, display error
				$redirect_url = add_query_arg( 'register-errors', 'closed', $redirect_url );
			} elseif ( ! $this->verify_recaptcha() ) {
				// Recaptcha check failed, display error
				$redirect_url = add_query_arg( 'register-errors', 'captcha', $redirect_url );
			} else {
				$email      = sanitize_email( $_POST['email'] );
				$first_name = sanitize_text_field( $_POST['first_name'] );
				$last_name  = sanitize_text_field( $_POST['last_name'] );
				unset( $_POST['email'], $_POST['first_name'], $_POST['last_name'] );

				$result = $this->register_user( $email, $first_name, $last_name, $_POST );

				if ( is_wp_error( $result ) ) {
					// Parse errors into a string and append as parameter to redirect
					$errors       = join( ',', $result->get_error_codes() );
					$redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
				} else {
					// Success, redirect to login page.
					$redirect_url = esc_url( get_the_permalink( $this->options['login_page'] ) );
					$redirect_url = add_query_arg( 'registered', $email, $redirect_url );
				}
			}

			wp_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Initiates password reset.
	 *
	 * @since 1.0
	 */
	public function do_password_lost() {
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			$errors = retrieve_password();
			if ( is_wp_error( $errors ) ) {
				// Errors found
				$redirect_url = esc_url( get_the_permalink( $this->options['password_lost_page'] ) );
				$redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
			} else {
				// Email sent
				$redirect_url = esc_url( get_the_permalink( $this->options['login_page'] ) );
				$redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
				if ( ! empty( $_REQUEST['redirect_to'] ) ) {
					$redirect_url = $_REQUEST['redirect_to'];
				}
			}

			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Resets the user's password if the password reset form was submitted.
	 *
	 * @since 1.0
	 */
	public function do_password_reset() {
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			$rp_key   = $_REQUEST['key'];
			$rp_login = $_REQUEST['login'];

			// Manual reset on the 'Account' page (user is logged in)
			if ( is_user_logged_in() && isset( $_POST['user_manual_reset'] ) && 'true' === esc_textarea( $_POST['user_manual_reset'] ) ) {
				// Store the user object
				$user_obj = get_user_by( 'id', absint( $_POST['user_id'] ) );
				// reset the password
				reset_password( $user_obj, esc_textarea( $_POST['pass1'] ) );
				// Redirect
				wp_redirect(
					add_query_arg(
						array(
							'password' => 'changed',
						),
						esc_url( get_the_permalink( $this->options['login_page'] ) )
					)
				);
				exit;
			}

			$user = check_password_reset_key( $rp_key, $rp_login );

			if ( ! $user || is_wp_error( $user ) ) {
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					wp_redirect(
						add_query_arg(
							array(
								'login' => 'expiredkey',
							),
							esc_url( get_the_permalink( $this->options['login_page'] ) )
						)
					);
				} else {
					wp_redirect(
						add_query_arg(
							array(
								'login' => 'invalidkey',
							),
							esc_url( get_the_permalink( $this->options['login_page'] ) )
						)
					);
				}
				exit;
			}

			if ( isset( $_POST['pass1'] ) ) {
				if ( $_POST['pass1'] !== $_POST['pass2'] ) {
					// Passwords don't match
					$redirect_url = esc_url( get_the_permalink( $this->options['pick_new_password_page'] ) );
					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );
					wp_redirect( $redirect_url );
					exit;
				}

				if ( empty( $_POST['pass1'] ) ) {
					// Password is empty
					$redirect_url = esc_url( get_the_permalink( $this->options['pick_new_password_page'] ) );

					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );

					wp_redirect( $redirect_url );
					exit;
				}

				// Parameter checks OK, reset password
				reset_password( $user, $_POST['pass1'] );
				// Redirect
				wp_redirect(
					add_query_arg(
						array(
							'password' => 'changed',
						),
						esc_url( get_the_permalink( $this->options['login_page'] ) )
					)
				);
			} else {
				echo 'Invalid request.';
			}
			exit;
		}
	}

	/**
	 * Update the given members profile based on the submitted data
	 *
	 * @since 1.0
	 */
	public function do_update_user_profile() {
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			// if not update user, abort
			if ( empty( $_POST['action'] ) || 'update-user' !== $_POST['action'] ) {
				return;
			}
			// verify our nonce
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user' ) ) {
				wp_redirect(
					add_query_arg(
						array(
							'update' => 'nonce_error',
						),
						esc_url( wp_get_referer() )
					)
				);
				exit;
			}
			// Setup our globals
			global $current_user, $wp_roles;

			// unset the nonce, so we can loop over each piece of data
			unset( $_POST['_wpnonce'], $_POST['_wp_http_referer'], $_POST['action'], $_POST['updateuser'] );

			// Setup a new array of data
			$user_data = array(
				'ID' => $current_user->ID,
			);
			// User data array
			$user_data_array = array(
				'user_login',
				'user_nicename',
				'user_email',
				'user_url',
				'display_name',
				'first_name',
				'last_name',
			);

			do_action( 'yikes-custom-login-before-profile-update', $current_user->ID );

			// Loop over the remaining data, and update it!
			foreach ( $_POST as $profile_data_key => $profile_data_value ) {
				if ( in_array( $profile_data_key, $user_data_array ) ) {
					// push our data to the array, and sanitize it
					$user_data[ $profile_data_key ] = trim( sanitize_text_field( $profile_data_value ) );
				} else {
					update_user_meta( $current_user->ID, $profile_data_key, trim( sanitize_text_field( $profile_data_value ) ) );
				}
			}

			// Now update the user
			$update_user = wp_update_user( $user_data );
			if ( is_wp_error( $update_user ) ) {
				wp_redirect(
					add_query_arg(
						array(
							'update' => 'error',
						),
						esc_url( wp_get_referer() )
					)
				);
				exit;
			}

			do_action( 'yikes-custom-login-successful-profile-update', $current_user->ID );

			// Success
			wp_redirect(
				add_query_arg(
					array(
						'update' => 'success',
					),
					esc_url( wp_get_referer() )
				)
			);
			exit;
		}
	}

	/**
	 * Display errors above a given form
	 *
	 * @since 1.0
	 */
	public function yikes_custom_login_display_alerts( $errors ) {
		/* If errors are found, display them */
		if ( count( $errors ) > 0 ) {
			printf(
				'<p class="error %s %s">%s</p>',
				'yikes-custom-login-alert yikes-custom-login-alert-danger yikes-animated',
				esc_attr( $this->options['notice_animation'] ),
				esc_html( implode( '<br />', $error ) )
			);
		}
		// if there's an error
		if ( isset( $_GET['update'] ) ) {
			switch ( $_GET['update'] ) {
				default:
				case 'error':
					$alert_class = 'yikes-custom-login-alert-danger';
					$message     = sprintf( _x( '%s An error occured. Please try again.', 'Unicode value for "X".', 'custom-wp-login' ), '&#10007;' );
					break;
				case 'nonce_error':
					$alert_class = 'yikes-custom-login-alert-danger';
					$message     = sprintf( _x( '%s The security check did not pass. Please refresh the page and try again.', 'Unicode value for "X".', 'custom-wp-login' ), '&#10007;' );
					break;
				case 'success':
					$alert_class = 'yikes-custom-login-alert-success';
					$message     = sprintf( _x( '%s Profile successfully updated.', 'custom-wp-login' ), '&#10003;' );
					break;
			}
			printf(
				'<p class="error %s %s %s">%s</p>',
				'yikes-custom-login-alert yikes-animated',
				esc_attr( $alert_class ),
				esc_attr( $this->options['notice_animation'] ),
				esc_html( $message )
			);
		}
	}

	//
	// OTHER CUSTOMIZATIONS
	//

	/**
	 * Returns the message body for the password reset mail.
	 * Called through the retrieve_password_message filter.
	 *
	 * @param string  $message    Default mail message.
	 * @param string  $key        The activation key.
	 * @param string  $user_login The username for the user.
	 * @param WP_User $user_data  WP_User object.
	 *
	 * @return string   The mail message to send.
	 * @since 1.0
	 */
	public function replace_retrieve_password_message( $message, $key, $user_login, $user_data ) {
		// setup the reset password URL
		$reset_pass_url = add_query_arg(
			array(
				'action' => 'rp',
				'key'    => $key,
				'login'  => rawurlencode( $user_login ),
			),
			site_url( 'wp-login.php', 'login' )
		);
		// Instantiate the email templates class
		include_once plugin_dir_path( __FILE__ ) . 'lib/classes/email-templates.php';
		// load the 'password-reset' template
		ob_start();
		$email_template = new YIKES_Email_Templates( $this->options );
		$email_template->send_password_reset_email( $key, $user_login, $reset_pass_url );
		$msg = ob_get_contents();
		ob_get_clean();
		// Create new message
		return $msg;
	}


	//
	// HELPER FUNCTIONS
	//

	/**
	 * Validates and then completes the new user signup process if all went well.
	 *
	 * @param string $email         The new user's email address
	 * @param string $first_name    The new user's first name
	 * @param string $last_name     The new user's last name
	 *
	 * @return int|WP_Error         The id of the user that was created, or error if failed.
	 * @since 1.0
	 */
	private function register_user( $email, $first_name, $last_name, $additional_fields = array() ) {
		global $wpdb;

		$errors = new WP_Error();

		// Email address is used as both username and email. It is also the only
		// parameter we need to validate
		if ( ! is_email( $email ) ) {
			$errors->add( 'email', $this->get_error_message( 'email' ) );
			return $errors;
		}

		if ( username_exists( $email ) || email_exists( $email ) ) {
			$errors->add( 'email_exists', $this->get_error_message( 'email_exists' ) );
			return $errors;
		}

		// Generate the password so that the subscriber will have to check email
		$key = wp_generate_password( 20, false );

		$user_data = array(
			'user_login' => $email,
			'user_email' => $email,
			'user_pass'  => $key,
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'nickname'   => $first_name,
			'role'       => apply_filters( 'yikes-custom-login-new-user-role', get_option( 'default_role' ) ),
		);

		$user_id = wp_insert_user( $user_data );

		if ( $user_id ) {
			/** This action is documented in wp-login.php */
			do_action( 'retrieve_password_key', $email, $key );

			// If additional fields are set, loop over them and update the user
			if ( ! empty( $additional_fields ) ) {
				foreach ( $additional_fields as $field_id => $field_value ) {
					update_user_meta( $user_id, $field_id, $field_value );
				}
			}

			// Now insert the key, hashed, into the DB.
			if ( empty( $wp_hasher ) ) {
				require_once ABSPATH . WPINC . '/class-phpass.php';
				$wp_hasher = new PasswordHash( 8, true );
			}

			$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
			$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $email ) );

			// Inclde our custom 'Welcome' email template
			include_once YIKES_CUSTOM_LOGIN_PATH . 'lib/classes/email-templates.php';
			$email_class = new YIKES_Email_Templates( $this->options );
			$email_class->send_new_user_notifications( $user_id, $key );

			return $user_id;
		}
		return false;
	}

	/**
	 * Checks that the reCAPTCHA parameter sent with the registration
	 * request is valid.
	 *
	 * @return bool True if the CAPTCHA is OK, otherwise false.
	 * @since 1.0
	 */
	private function verify_recaptcha() {
		// This field is set by the recaptcha widget if check is successful
		if ( isset( $_POST['g-recaptcha-response'] ) ) {
			$captcha_response = $_POST['g-recaptcha-response'];
			// Verify the captcha response from Google
			$response = wp_remote_post(
				'https://www.google.com/recaptcha/api/siteverify',
				array(
					'body' => array(
						'secret'   => get_option( 'personalize-login-recaptcha-secret-key' ),
						'response' => $captcha_response,
					),
				)
			);
			$success  = false;
			if ( $response && is_array( $response ) ) {
				$decoded_response = json_decode( $response['body'] );
				$success          = $decoded_response->success;
			}
			return $success;
		}
		return true;
	}

	/**
	 * Redirects the user to the correct page depending on whether he / she
	 * is an admin or not.
	 *
	 * @param string $redirect_to   An optional redirect_to URL for admin users
	 * @since 1.0
	 */
	private function redirect_logged_in_user( $redirect_to = null ) {
		$user = wp_get_current_user();
		// Check if the user has the 'manage_options' capabilities
		if ( user_can( $user, 'manage_options' ) ) {
			if ( $redirect_to ) {
				wp_safe_redirect( $redirect_to );
			} else {
				wp_redirect( admin_url() );
			}
		} else {
			if ( 0 === $this->options['account_info_page'] ) {
				wp_redirect( site_url() );
				exit;
			}
			$logged_in_redirect_url = apply_filters( 'yikes-custom-login-redirect', esc_url( get_the_permalink( $this->options['account_info_page'] ) ) );
			wp_redirect( $logged_in_redirect_url );
			exit;
		}
	}

	/**
	 * Finds and returns a matching error message for the given error code.
	 *
	 * @param string $error_code    The error code to look up.
	 *
	 * @return string               An error message.
	 */
	private function get_error_message( $error_code ) {
		switch ( $error_code ) {
			// Login errors

			case 'empty_username':
				return __( 'You do have an email address, right?', 'custom-wp-login' );

			case 'empty_password':
				return __( 'You need to enter a password to login.', 'custom-wp-login' );

			case 'invalid_username':
				return __(
					"The username you entered is unknown. Please check again or try your email address.",
					'custom-wp-login'
				);

			case 'incorrect_password':
				$err = _x(
					"The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
					'The WordPress lost password URL.',
					'custom-wp-login'
				);
				return sprintf( $err, wp_lostpassword_url() );

			// Registration errors

			case 'email':
				return __( 'The email address you entered is not valid.', 'custom-wp-login' );

			case 'email_exists':
				return __( 'An account exists with this email address.', 'custom-wp-login' );

			case 'closed':
				return __( 'Registering new users is currently not allowed.', 'custom-wp-login' );

			case 'captcha':
				return __( 'The Google reCAPTCHA check failed. Are you a robot?', 'custom-wp-login' );

			// Lost password

			case 'empty_username':
				return __( 'You need to enter your email address to continue.', 'custom-wp-login' );

			case 'invalid_email':
			case 'invalidcombo':
				return __( 'There are no users registered with this email address.', 'custom-wp-login' );

			// Reset password

			case 'expiredkey':
			case 'invalidkey':
				return __( 'The password reset link you used is not valid anymore.', 'custom-wp-login' );

			case 'password_reset_mismatch':
				return __( "The two passwords you entered don't match.", 'custom-wp-login' );

			case 'password_reset_empty':
				return __( "Sorry, we don't accept empty passwords.", 'custom-wp-login' );

			default:
				break;
		}

		return __( 'An unknown error occurred. Please try again later.', 'custom-wp-login' );
	}

	/**
	 * Clear the 'yikes_custom_login_pages_query' transient when pages are updated/published
	 *
	 * @param  int    $post_id    The post ID that is being updated/published
	 * @param  object $post     The post object.
	 * @param  bool   $update     Whether this is an existing post being updated or not  [description]
	 */
	public function clear_transient_on_page_save( $post_id, $post, $update ) {
		// if it is not a page, abort
		if ( 'page' !== $post->post_type ) {
			return;
		}
		// clear our transient
		delete_transient( 'yikes_custom_login_pages_query' );
	}

	/**
	 * Use a custom page template for the login page
	 *
	 * @param  string $page_template Name of the template to use
	 * @return string                The template to use
	 */
	public function yikes_custom_login_page_template( $page_template ) {
		// Login Page Template
		if ( is_page( $this->options['login_page'] ) ) {
			// if the full width page template is set
			if ( get_post_meta( $this->options['login_page'], '_full_width_page_template', true ) ) {
				$page_template = YIKES_CUSTOM_LOGIN_PATH . 'templates/page/login-page-template.php';
			}
		}
		// Password Lost Page
		if ( is_page( $this->options['password_lost_page'] ) ) {
			// check if the user is logged in
			if ( is_user_logged_in() ) {
				$account_info_page_url = ( 0 === $this->options['account_info_page'] ) ? site_url() : esc_url( get_the_permalink( $this->options['account_info_page'] ) . '#new-password' );
				wp_redirect( $account_info_page_url );
				exit;
			}
			if ( get_post_meta( $this->options['password_lost_page'], '_full_width_page_template', true ) ) {
				$page_template = YIKES_CUSTOM_LOGIN_PATH . 'templates/page/password-lost-page-template.php';
			}
		}
		// Pick New Password Page
		if ( is_page( $this->options['pick_new_password_page'] ) ) {
			// check if the user is logged in
			if ( is_user_logged_in() ) {
				$account_info_page_url = ( 0 === $this->options['account_info_page'] ) ? site_url() : esc_url( get_the_permalink( $this->options['account_info_page'] ) . '#new-password' );
				wp_redirect( $account_info_page_url );
				exit;
			}
			if ( get_post_meta( $this->options['pick_new_password_page'], '_full_width_page_template', true ) ) {
				$page_template = YIKES_CUSTOM_LOGIN_PATH . 'templates/page/pick-new-password-page-template.php';
			}
		}

		// New User Registration Page
		if ( is_page( $this->options['register_page'] ) ) {

			// check if the user is logged in
			if ( is_user_logged_in() ) {

				if ( ! current_user_can( 'manage_options' ) ) {
					$account_info_page_url = get_yikes_account_info_page();
					$account_info_page_id  = get_yikes_account_info_page_id();
					wp_redirect( apply_filters( 'yikes-custom-login-login-redirect-url', $account_info_page_url, $account_info_page_id, wp_get_current_user() ) );
					exit;
				}
				wp_redirect( admin_url() );
				exit;
			}

			if ( get_post_meta( $this->options['register_page'], '_full_width_page_template', true ) ) {
				$page_template = YIKES_CUSTOM_LOGIN_PATH . 'templates/page/new-user-registration-page-template.php';
			}
		}

		return $page_template;
	}

	/**
	 * Add custom back links etc. to the template pages
	 *
	 * @return string html markup to be used
	 */
	public function yikes_custom_password_lost_page_backlinks() {
		ob_start();
		?>
		<a href="<?php echo esc_url( get_the_permalink( $this->options['login_page'] ) ); ?>">&#xab; Back</a>
		<?php
		$links = ob_get_contents();
		ob_get_clean();
		echo wp_kses_post( $links );
	}

	/**
	 * Add custom 'Powered By' link/ad to the login page (bottom left)
	 *
	 * @return string html markup to be used
	 */
	public function yikes_custom_login_page_text() {
		// If the setting has been disabled, abort
		if ( ! isset( $this->options['powered_by_yikes'] ) || 0 === $this->options['powered_by_yikes'] ) {
			return;
		}
		ob_start();
		?>
		<div class="powered-by-yikes">
			<?php
			printf(
				esc_attr_x( '%1$s Powered by %2$s.', 'First: Unicode for lightning bolt. Second: Anchor tag linking back to yikesplugins.com', 'custom-wp-login' ),
				esc_attr( '⚡' ),
				wp_kses_post( '<a href="https://yikesplugins.com/" target="_blank">YIKES Plugins</a>' )
			);
			?>
		</div>
		<?php
		$div = ob_get_contents();
		ob_get_clean();
		echo wp_kses_post( $div );
	}

	/**
	 * Generate the back link with the site branding image
	 *
	 * @return string HTMl to be used for the site branding
	 */
	public function yikes_custom_login_generate_branding_logo() {
		$customizer_modifications = get_theme_mod( 'login_logo', false );
		if ( $customizer_modifications ) {
			ob_start();
			?>
			<a class="yikes-custom-login-site-branding yikes-animated yikes-fadeIn" href="<?php echo esc_url( site_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'site_title' ) ); ?>">
				<img src="<?php echo esc_url( get_theme_mod( 'login_logo', false ) ); ?>" />
			</a>
			<?php
			$branding = ob_get_contents();
			ob_get_clean();
			echo wp_kses_post( $branding );
		}
	}
	/**
	 * Append 'Already a member?'
	 *
	 * @return html String to append below the new user registration form
	 */
	public function yikes_custom_login_append_already_a_member_text() {
		$sign_in_url = esc_url( get_the_permalink( $this->options['login_page'] ) );
		echo wp_kses_post( '<small class="yikes-already-a-member"><em>' . sprintf( _x( 'Already a member? %s', 'The Login page URL. <a> tag.', 'custom-wp-login' ), '<a href="' . $sign_in_url . '">' . __( 'Sign In', 'custom-wp-login' ) . '</a>' ) . '</em></small>' );
	}

	/**
	 * Print out inline styles for our full width page templates
	 * that are stored in the customizer options
	 *
	 * @since 1.0
	 */
	public function yikes_custom_login_generate_customizer_styles() {
		global $post;
		// ensure we only print out on the proper pages
		$page_ids = array(
			$this->options['login_page'],
			$this->options['pick_new_password_page'],
			$this->options['password_lost_page'],
			$this->options['register_page'],
		);
		if ( is_object( $post ) && in_array( $post->ID, $page_ids ) ) {
			// Login Container border Color
			include_once plugin_dir_path( __FILE__ ) . 'lib/classes/customizer-style-overrides.php';
			$customizer_class = new YIKES_Custom_Login_Customizer_Stlyes_Override();
			wp_add_inline_style( 'yikes-custom-login-public', $customizer_class::generate_customizer_styles() );
			// First check that wp_add_inline_script exists (WordPress 4.5+)
			if ( function_exists( 'wp_add_inline_script' ) ) {
				wp_add_inline_script( 'yikes-login-page-script', $customizer_class::generate_customizer_scripts(), 'after' );
			}
		}
	}
}

// PLUGIN SETUP
// Initialize the plugin and options
$yikes_custom_login = new YIKES_Custom_Login();

// Create the custom pages at plugin activation
register_activation_hook( __FILE__, array( 'YIKES_Custom_Login', 'plugin_activated' ) );

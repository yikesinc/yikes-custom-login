<?php
/**
 * Plugin Name:       YIKES Custom Login
 * Plugin URI:        https://www.yikesplugins.com
 * Description:       A plugin that replaces the WordPress login flow with custom pages.
 * Version:           1.0.0
 * Author:            YIKES, Evan Herman
 * Author URI:        http://www.yikesinc.com
 * License:           GPL-2.0+
 * Text Domain:       yikes-custom-login
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
			define( 'YIKES_CUSTOM_LOGIN_VERSION', '1.0' );
		}

		// Restrict admin dashboard access to only admins ('manage_options' capability)
		add_action( 'admin_init', 'yikes_restrict_admin_dashboard', 1 );

		// Redirects
		add_action( 'login_form_login', array( $this, 'redirect_to_custom_login' ) );
		add_filter( 'authenticate', array( $this, 'maybe_redirect_at_authenticate' ), 101, 3 );
		add_filter( 'login_redirect', array( $this, 'redirect_after_login' ), 10, 3 );
		add_action( 'wp_logout', array( $this, 'redirect_after_logout' ) );

		add_action( 'login_form_register', array( $this, 'redirect_to_custom_register' ) );
		add_action( 'login_form_lostpassword', array( $this, 'redirect_to_custom_lostpassword' ) );
		add_action( 'login_form_rp', array( $this, 'redirect_to_custom_password_reset' ) );
		add_action( 'login_form_resetpass', array( $this, 'redirect_to_custom_password_reset' ) );

		// Handlers for form posting actions
		add_action( 'login_form_register', array( $this, 'do_register_user' ) );
		add_action( 'login_form_lostpassword', array( $this, 'do_password_lost' ) );
		add_action( 'login_form_rp', array( $this, 'do_password_reset' ) );
		add_action( 'login_form_resetpass', array( $this, 'do_password_reset' ) );

		// Other customizations
		add_filter( 'retrieve_password_message', array( $this, 'replace_retrieve_password_message' ), 10, 4 );

		// Setup
		add_action( 'wp_print_footer_scripts', array( $this, 'add_captcha_js_to_footer' ) );
		add_filter( 'admin_init' , array( $this, 'register_settings_fields' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_yikes_custom_login_options_scripts_and_styles' ) );

		// Shortcodes
		add_shortcode( 'custom-login-form', array( $this, 'render_login_form' ) );
		add_shortcode( 'account-info', array( $this, 'render_account_info_form' ) );
		add_shortcode( 'custom-register-form', array( $this, 'render_register_form' ) );
		add_shortcode( 'custom-password-lost-form', array( $this, 'render_password_lost_form' ) );
		add_shortcode( 'custom-password-reset-form', array( $this, 'render_password_reset_form' ) );

		// Include our settings page
		if ( ! class_exists( 'YIKES_Login_Settings' ) ) {
			include_once( plugin_dir_path( __FILE__ ) . 'lib/classes/options.php' );
			// Store our options
			$this->options = self::get_yikes_custom_login_options();
		}

		/* Clear our transient each time a page is updated/published - clears the pages settings dropdowns */
		add_action( 'save_post', array( $this, 'clear_transient_on_page_save' ), 10, 3 );
	}

	/**
	 * Restirct admin dashboard access for non-admin users
	 * @since 1.0
	 */
	public function yikes_restrict_admin_dashboard() {
		if ( ! current_user_can( 'manage_options' ) && '/wp-admin/admin-ajax.php' !== $_SERVER['PHP_SELF'] ) {
			wp_redirect( site_url() );
		}
	}

	/**
	 * Enqueue frontend styles for all of our shortcodes
	 * Used where all of our shortcodes are being used
	 * @since 1.0
	 */
	public function enqueue_yikes_custom_login_styles() {
		wp_enqueue_style( 'yikes-custom-login-public', plugin_dir_url( __FILE__ ) . '/lib/css/min/yikes-custom-login-public.min.css', array(), YIKES_CUSTOM_LOGIN_VERSION );
	}

	/**
	 * Enqueue options page scripts & styles
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
			// Options page scriptts
			wp_enqueue_script( 'yikes-options-script', plugin_dir_url( __FILE__ ) . '/lib/js/min/yikes-custom-login-options.min.js', array( 'select2' ), YIKES_CUSTOM_LOGIN_VERSION, true );
		}
	}

	/**
	 * Helper function to get the custom login options
	 * @return array The custom login options.
	 * @since 1.0
	 */
	public static function get_yikes_custom_login_options() {
		return get_option( 'yikes_custom_login', array(
			'admin_redirect' => 1,
			'notice_animation' => 'none',
			'register_page' => null,
			'login_page' => null,
			'account_info_page' => null,
			'password_lost_page' => null,
			'recaptcha_site_key' => false,
			'recaptcha_secret_key' => false,
		) );
	}

	/**
	 * Plugin activation hook.
	 *
	 * Creates all WordPress pages needed by the plugin.
	 * @since 1.0
	 */
	public static function plugin_activated() {
		// Information needed for creating the plugin's pages
		$page_definitions = array(
			apply_filters( 'yikes-custom-login-login-slug', 'member-login' ) => array(
				'title' => __( 'Sign In', 'yikes-custom-login' ),
				'content' => '[custom-login-form]',
			),
			apply_filters( 'yikes-custom-login-account-slug', 'member-account' ) => array(
				'title' => __( 'Your Account', 'yikes-custom-login' ),
				'content' => '[account-info]',
			),
			apply_filters( 'yikes-custom-login-register-slug', 'member-register' ) => array(
				'title' => __( 'Registration', 'yikes-custom-login' ),
				'content' => '[custom-register-form]',
			),
			apply_filters( 'yikes-custom-login-password-lost-slug', 'member-password-lost' ) => array(
				'title' => __( 'Forgot Your Password?', 'yikes-custom-login' ),
				'content' => '[custom-password-lost-form]',
			),
			apply_filters( 'yikes-custom-login-password-reset-slug', 'member-password-reset' ) => array(
				'title' => __( 'Pick a New Password', 'yikes-custom-login' ),
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
					default:
						break;
				}
				// Update our options with the new page ID values
				update_option( 'yikes_custom_login', $plugin_options );
			}
		}
	}

	//
	// REDIRECT FUNCTIONS
	//

	/**
	 * Redirect the user to the custom login page instead of wp-login.php.
	 * @since 1.0
	 */
	public function redirect_to_custom_login() {
		if ( 'GET' === $_SERVER['REQUEST_METHOD'] ) {
			if ( is_user_logged_in() ) {
				$this->redirect_logged_in_user();
				exit;
			}

			// The rest are redirected to the login page
			$login_url = home_url( 'member-login' );
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
	 * Redirect the user after authentication if there were any errors.
	 *
	 * @param Wp_User|Wp_Error  $user       The signed in user, or the errors that have occurred during login.
	 * @param string            $username   The user name used to log in.
	 * @param string            $password   The password used to log in.
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

				$login_url = home_url( 'member-login' );
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
	 * @param string           $redirect_to           The redirect destination URL.
	 * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
	 * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
	 *
	 * @return string Redirect URL
	 * @since 1.0
	 */
	public function redirect_after_login( $redirect_to, $requested_redirect_to, $user ) {
		$redirect_url = home_url();

		if ( ! isset( $user->ID ) ) {
			return $redirect_url;
		}

		// If admin_redirect is not set, abort
		if ( 0 === $this->options['admin_redirect'] ) {
			$logged_in_redirect_url = apply_filters( 'yikes-custom-login-redirect', home_url( 'member-account' ) );
			wp_redirect( $logged_in_redirect_url );
			return;
		}

		if ( user_can( $user, 'manage_options' ) ) {
			// Use the redirect_to parameter if one is set, otherwise redirect to admin dashboard.
			if ( '' === $requested_redirect_to ) {
				$redirect_url = admin_url();
			} else {
				$redirect_url = $redirect_to;
			}
		} else {
			// Non-admin users always go to their account page after login
			$redirect_url = home_url( 'member-account' );
		}
		return wp_validate_redirect( apply_filters( 'yikes-custom-login-redirect', $redirect_url ), home_url() );
	}

	/**
	 * Redirect to custom login page after the user has been logged out.
	 * @since 1.0
	 */
	public function redirect_after_logout() {
		$redirect_url = home_url( 'member-login?logged_out=true' );
		wp_redirect( $redirect_url );
		exit;
	}

	/**
	 * Redirects the user to the custom registration page instead of wp-login.php?action=register.
	 * @since 1.0
	 */
	public function redirect_to_custom_register() {
		if ( 'GET' === $_SERVER['REQUEST_METHOD'] ) {
			if ( is_user_logged_in() ) {
				$this->redirect_logged_in_user();
			} else {
				wp_redirect( home_url( 'member-register' ) );
			}
			exit;
		}
	}

	/**
	 * Redirects the user to the custom "Forgot your password?" page instead of wp-login.php?action=lostpassword.
	 * @since 1.0
	 */
	public function redirect_to_custom_lostpassword() {
		if ( 'GET' === $_SERVER['REQUEST_METHOD'] ) {
			if ( is_user_logged_in() ) {
				$this->redirect_logged_in_user();
				exit;
			}

			wp_redirect( home_url( 'member-password-lost' ) );
			exit;
		}
	}

	/**
	 * Redirects to the custom password reset page, or the login page if there are errors.
	 * @since 1.0
	 */
	public function redirect_to_custom_password_reset() {
		if ( 'GET' === $_SERVER['REQUEST_METHOD'] ) {
			// Verify key / login combo
			$user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
			if ( ! $user || is_wp_error( $user ) ) {
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					wp_redirect( home_url( 'member-login?login=expiredkey' ) );
				} else {
					wp_redirect( home_url( 'member-login?login=invalidkey' ) );
				}
				exit;
			}

			$redirect_url = home_url( 'member-password-reset' );
			$redirect_url = add_query_arg( 'login', esc_attr( $_REQUEST['login'] ), $redirect_url );
			$redirect_url = add_query_arg( 'key', esc_attr( $_REQUEST['key'] ), $redirect_url );

			wp_redirect( $redirect_url );
			exit;
		}
	}


	//
	// FORM RENDERING SHORTCODES
	//

	/**
	 * A shortcode for rendering the login form.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 * @since 1.0
	 */
	public function render_login_form( $attributes, $content = null ) {
		// Enqueue the plugin frontend styles
		$this->enqueue_yikes_custom_login_styles();

		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes = shortcode_atts( $default_attributes, $attributes );

		if ( is_user_logged_in() ) {
			return __( 'You are already signed in.', 'yikes-custom-login' );
		}

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
		$attributes['logged_out'] = isset( $_REQUEST['logged_out'] ) && true === $_REQUEST['logged_out'];

		// Check if the user just registered
		$attributes['registered'] = isset( $_REQUEST['registered'] );

		// Check if the user just requested a new password
		$attributes['lost_password_sent'] = isset( $_REQUEST['checkemail'] ) && 'confirm' === $_REQUEST['checkemail'];

		// Check if user just updated password
		$attributes['password_updated'] = isset( $_REQUEST['password'] ) && 'changed' === $_REQUEST['password'];

		// Store the username
		$attributes['username_value'] = isset( $_POST['log'] ) ? $_POST['log'] : '';

		// Render the login form using an external template
		return $this->get_template_html( 'login-form', $attributes );
	}

	/**
	 * A shortcode for rendering the login form.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 * @since 1.0
	 */
	public function render_account_info_form( $attributes, $content = null ) {
		// Enqueue the plugin frontend styles
		$this->enqueue_yikes_custom_login_styles();
		// Render the login form using an external template
		return $this->get_template_html( 'account-info-form', $attributes );
	}

	/**
	 * A shortcode for rendering the new user registration form.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 * @since 1.0
	 */
	public function render_register_form( $attributes, $content = null ) {
		// Enqueue the plugin frontend styles
		$this->enqueue_yikes_custom_login_styles();
		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes = shortcode_atts( $default_attributes, $attributes );

		if ( is_user_logged_in() ) {
			return __( 'You are already signed in.', 'yikes-custom-login' );
		} elseif ( ! get_option( 'users_can_register' ) ) {
			return __( 'Registering new users is currently not allowed.', 'yikes-custom-login' );
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
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 * @since 1.0
	 */
	public function render_password_lost_form( $attributes, $content = null ) {
		// Enqueue the plugin frontend styles
		$this->enqueue_yikes_custom_login_styles();
		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes = shortcode_atts( $default_attributes, $attributes );

		if ( is_user_logged_in() ) {
			return __( 'You are already signed in.', 'yikes-custom-login' );
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
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 * @since 1.0
	 */
	public function render_password_reset_form( $attributes, $content = null ) {
		// Enqueue the plugin frontend styles
		$this->enqueue_yikes_custom_login_styles();
		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes = shortcode_atts( $default_attributes, $attributes );

		if ( is_user_logged_in() ) {
			return __( 'You are already signed in.', 'yikes-custom-login' );
		} else {
			if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {
				$attributes['login'] = $_REQUEST['login'];
				$attributes['key'] = $_REQUEST['key'];

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
				return __( 'Invalid password reset link.', 'yikes-custom-login' );
			}
		}
	}

	/**
	 * An action function used to include the reCAPTCHA JavaScript file
	 * at the end of the page.
	 * @since 1.0
	 */
	public function add_captcha_js_to_footer() {
		echo "<script src='https://www.google.com/recaptcha/api.js?hl=en'></script>";
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
			require( get_template_directory() . '/yikes-custom-login/' . $template_name . '.php' );
		} else {
			require( 'templates/' . $template_name . '.php' );
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
	 * @since 1.0
	 */
	public function do_register_user() {
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			$redirect_url = home_url( 'member-register' );

			if ( ! get_option( 'users_can_register' ) ) {
				// Registration closed, display error
				$redirect_url = add_query_arg( 'register-errors', 'closed', $redirect_url );
			} elseif ( ! $this->verify_recaptcha() ) {
				// Recaptcha check failed, display error
				$redirect_url = add_query_arg( 'register-errors', 'captcha', $redirect_url );
			} else {
				$email = $_POST['email'];
				$first_name = sanitize_text_field( $_POST['first_name'] );
				$last_name = sanitize_text_field( $_POST['last_name'] );

				$result = $this->register_user( $email, $first_name, $last_name );

				if ( is_wp_error( $result ) ) {
					// Parse errors into a string and append as parameter to redirect
					$errors = join( ',', $result->get_error_codes() );
					$redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
				} else {
					// Success, redirect to login page.
					$redirect_url = home_url( 'member-login' );
					$redirect_url = add_query_arg( 'registered', $email, $redirect_url );
				}
			}

			wp_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Initiates password reset.
	 * @since 1.0
	 */
	public function do_password_lost() {
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			$errors = retrieve_password();
			if ( is_wp_error( $errors ) ) {
				// Errors found
				$redirect_url = home_url( 'member-password-lost' );
				$redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
			} else {
				// Email sent
				$redirect_url = home_url( 'member-login' );
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
	 * @since 1.0
	 */
	public function do_password_reset() {
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			$rp_key = $_REQUEST['rp_key'];
			$rp_login = $_REQUEST['rp_login'];

			$user = check_password_reset_key( $rp_key, $rp_login );

			if ( ! $user || is_wp_error( $user ) ) {
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					wp_redirect( home_url( 'member-login?login=expiredkey' ) );
				} else {
					wp_redirect( home_url( 'member-login?login=invalidkey' ) );
				}
				exit;
			}

			if ( isset( $_POST['pass1'] ) ) {
				if ( $_POST['pass1'] !== $_POST['pass2'] ) {
					// Passwords don't match
					$redirect_url = home_url( 'member-password-reset' );

					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );

					wp_redirect( $redirect_url );
					exit;
				}

				if ( empty( $_POST['pass1'] ) ) {
					// Password is empty
					$redirect_url = home_url( 'member-password-reset' );

					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );

					wp_redirect( $redirect_url );
					exit;

				}

				// Parameter checks OK, reset password
				reset_password( $user, $_POST['pass1'] );
				wp_redirect( home_url( 'member-login?password=changed' ) );
			} else {
				echo 'Invalid request.';
			}

			exit;
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
		// Create new message
		$msg  = __( 'Hello!', 'yikes-custom-login' ) . "\r\n\r\n";
		$msg .= sprintf( __( 'You asked us to reset your password for your account using the email address %s.', 'yikes-custom-login' ), $user_login ) . "\r\n\r\n";
		$msg .= __( "If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.", 'yikes-custom-login' ) . "\r\n\r\n";
		$msg .= __( 'To reset your password, visit the following address:', 'yikes-custom-login' ) . "\r\n\r\n";
		$msg .= site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . "\r\n\r\n";
		$msg .= __( 'Thanks!', 'yikes-custom-login' ) . "\r\n";

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
	private function register_user( $email, $first_name, $last_name ) {
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

		// Generate the password so that the subscriber will have to check email...
		$password = wp_generate_password( 12, false );

		$user_data = array(
			'user_login'    => $email,
			'user_email'    => $email,
			'user_pass'     => $password,
			'first_name'    => $first_name,
			'last_name'     => $last_name,
			'nickname'      => $first_name,
			'role'					=> apply_filters( 'yikes-custom-login-new-user-role', 'subscriber' ),
		);

		$user_id = wp_insert_user( $user_data );
		wp_new_user_notification( $user_id, $password );

		return $user_id;
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
						'secret' => get_option( 'personalize-login-recaptcha-secret-key' ),
						'response' => $captcha_response,
					),
				)
			);
			$success = false;
			if ( $response && is_array( $response ) ) {
				$decoded_response = json_decode( $response['body'] );
				$success = $decoded_response->success;
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
			$logged_in_redirect_url = apply_filters( 'yikes-custom-login-redirect', home_url( 'member-account' ) );
			wp_redirect( $logged_in_redirect_url );
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
				return __( 'You do have an email address, right?', 'yikes-custom-login' );

			case 'empty_password':
				return __( 'You need to enter a password to login.', 'yikes-custom-login' );

			case 'invalid_username':
				return __(
					"We don't have any users with that email address. Maybe you used a different one when signing up?",
					'yikes-custom-login'
				);

			case 'incorrect_password':
				$err = __(
					"The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
					'yikes-custom-login'
				);
				return sprintf( $err, wp_lostpassword_url() );

			// Registration errors

			case 'email':
				return __( 'The email address you entered is not valid.', 'yikes-custom-login' );

			case 'email_exists':
				return __( 'An account exists with this email address.', 'yikes-custom-login' );

			case 'closed':
				return __( 'Registering new users is currently not allowed.', 'yikes-custom-login' );

			case 'captcha':
				return __( 'The Google reCAPTCHA check failed. Are you a robot?', 'yikes-custom-login' );

			// Lost password

			case 'empty_username':
				return __( 'You need to enter your email address to continue.', 'yikes-custom-login' );

			case 'invalid_email':
			case 'invalidcombo':
				return __( 'There are no users registered with this email address.', 'yikes-custom-login' );

			// Reset password

			case 'expiredkey':
			case 'invalidkey':
				return __( 'The password reset link you used is not valid anymore.', 'yikes-custom-login' );

			case 'password_reset_mismatch':
				return __( "The two passwords you entered don't match.", 'yikes-custom-login' );

			case 'password_reset_empty':
				return __( "Sorry, we don't accept empty passwords.", 'yikes-custom-login' );

			default:
				break;
		}

		return __( 'An unknown error occurred. Please try again later.', 'yikes-custom-login' );
	}

	/**
	 * Get all of the available profile meta fieilds
	 * Used when building the 'Update Profile' form
	 * @return array The fields to be used when rendering the 'Update Profile' form.
	 */
	public function yikes_custom_login_get_profile_fields( $user_id ) {
		// If no user id is specified
		if ( ! $user_id ) {
			return false;
		}

		/* Fields that we should NOT display on the frontend for users to edit */
		$excluded_fields = apply_filters( 'yikes-custom-login-excluded-profile-fields', array(
			'rich_editing',
			'comment_shortcuts',
			'admin_color',
			'use_ssl',
			'show_admin_bar_front',
			'wp_capabilities',
			'wp_user_level',
			'dismissed_wp_pointers',
			'show_welcome_panel',
			'session_tokens',
			'wp_dashboard_quick_press_last_post_id',
			'wp_user-settings',
			'wp_user-settings-time',
		) );

		// Get ALL meta fields
		$user_meta_fields = get_user_meta( $user_id );

		// Loop and unset our fields
		foreach ( $excluded_fields as $meta_id ) {
			unset( $user_meta_fields[ $meta_id ] );
		}

		// Create an array to loop over for the_author_meta()
		$author_meta_array = apply_filters( 'yikes-custom-login-author-meta-fields', array(
			'user_email',
			'user_url',
		) );

		// Loop over and push the additional fields to our fields array
		foreach ( $author_meta_array as $meta_id ) {
			$user_meta_fields[ $meta_id ] = array( get_the_author_meta( $meta_id, $user_id ) );
		}

		// store an int value to increment and retreive our keys (for label parameter)
		$key_location = 0;
		$user_meta_field_keys = array_keys( $user_meta_fields );

		// Loop over the keys and push the field label into our array
		foreach ( $user_meta_field_keys as $key => $meta_id ) {
			$user_meta_fields[ $meta_id ][] = $meta_id;
		}

		// Finally loop over our final fields array, and push two new parameters - 'type' and 'label'
		$user_meta_fields = array_map( function( $user_meta_fields ) {
			return array(
				'label' => esc_textarea( $this->yikes_custom_login_get_form_field_label( $user_meta_fields[1] ) ),
				'type' => $this->yikes_custom_login_get_form_field_type( $user_meta_fields[1] ),
				'data' => $this->yikes_custom_login_escape_form_field_data( $user_meta_fields[1], $user_meta_fields[0] ),
			);
		}, $user_meta_fields );

		/* Re-arrange the 'Biography' textarea field to the end of the form */
		$biography_field = $user_meta_fields['description'];
		unset( $user_meta_fields['description'] );
		$user_meta_fields[] = $biography_field;

		// Return the newly formed array
		return apply_filters( 'yikes-custom-login-profile-fields', $user_meta_fields, $user_id );
	}

	/**
	 * Switch statement to return an appropriate form field label for a given key
	 * @param  string $field_key The name of the field (eg first_name)
	 * @return string            The newly formatted field label
	 * @since 1.0
	 */
	public function yikes_custom_login_get_form_field_label( $field_key ) {
		// if no field key is set, abort
		if ( ! $field_key ) {
			return;
		}
		// Switch statement over the field key to dictate the label
		switch ( $field_key ) {
			default:
			case 'nickname':
				$label = __( 'Nickname', 'yikes-inc-custom-login' );
				break;
			case 'first_name':
				$label = __( 'First Name', 'yikes-inc-custom-login' );
				break;
			case 'last_name':
				$label = __( 'Last Name', 'yikes-inc-custom-login' );
				break;
			case 'description':
				$label = __( 'Biography', 'yikes-inc-custom-login' );
				break;
			case 'user_email':
				$label = __( 'Email Address', 'yikes-inc-custom-login' );
				break;
			case 'user_url':
				$label = __( 'Website', 'yikes-inc-custom-login' );
				break;
		}
		return apply_filters( 'yikes-custom-login-' . $field_key . '-label', $label );
	}

	/**
	 * Set the appropriate field type based on the field key passed in
	 * @param  string $field_key The key/name of the field.
	 * @return string            The field type to be used.
	 */
	public function yikes_custom_login_get_form_field_type( $field_key ) {
		// if no field key is set, abort
		if ( ! $field_key ) {
			return;
		}
		// Switch statement over the field key to dictate the label
		switch ( $field_key ) {
			default:
			case 'nickname':
			case 'first_name':
			case 'last_name':
				$type = 'text';
				break;
			case 'description':
				$type = 'textarea';
				break;
			case 'user_email':
				$type = 'email';
				break;
			case 'user_url':
				$type = 'url';
				break;
		}
		return apply_filters( 'yikes-custom-login-' . $field_key . '-type', $type );
	}

	/**
	 * Escape and return the field data for use in the form
	 * @param  string $field_key  The field key, used to decide how to sanitize
	 * @param  string $field_data The field data that will be sanitized and returned
	 * @return string             The escaped form data that will be used
	 */
	public function yikes_custom_login_escape_form_field_data( $field_key, $field_data ) {
		// if no field key is set, abort
		if ( ! $field_key ) {
			return;
		}
		// Switch statement over the field key to dictate the label
		switch ( $field_key ) {
			default:
			case 'text':
			case 'textarea':
				return esc_textarea( $field_data );
				break;
			case 'url':
				return esc_url( $field_data );
				break;
			case 'email':
				return sanitize_email( $field_data );
				break;
		}
	}
	/**
	 * Clear the 'yikes_custom_login_pages_query' transient when pages are updated/published
	 * @param  int 		$post_id 	The post ID that is being updated/published
	 * @param  object $post    	The post object.
	 * @param  bool 	$update		Whether this is an existing post being updated or not  [description]
	 */
	public function clear_transient_on_page_save( $post_id, $post, $update ) {
		// if it is not a page, abort
		if ( 'page' !== $post->post_type ) {
			return;
		}
		// clear our transient
		delete_transient( 'yikes_custom_login_pages_query' );
	}
}

//
// PLUGIN SETUP
//
//
// Initialize the plugin
$personalize_login_pages_plugin = new YIKES_Custom_Login();

// Create the custom pages at plugin activation
register_activation_hook( __FILE__, array( 'YIKES_Custom_Login', 'plugin_activated' ) );

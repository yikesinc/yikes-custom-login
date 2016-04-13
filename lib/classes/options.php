<?php
/**
 * YIKES Inc. Custom Login
 * Settings Page Template
 * @since 1.0
 */
class YIKES_Login_Settings {
	/**
	* Holds the values to be used in the fields callbacks
	*/
	private $options;

	/**
	* Start up
	*/
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'yikes_custom_login_load_option_styles' ) );
	}

	/**
	* Add options page
	*/
	public function add_plugin_page() {
		// This page will be under "Settings"
		add_options_page(
			'Settings Admin',
			__( 'Custom Login', 'yikes-inc-custom-login' ),
			'manage_options',
			'yikes-custom-login',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Render our
	 * @param  string $current [description]
	 * @return [type]          [description]
	 */
	public function yikes_admin_tabs( $current = 'general' ) {
		$tabs = array(
			'general' => 'General',
			'pages' => 'Pages',
			'recaptcha' => '<img class="recaptcha-icon" src="' . esc_url( plugin_dir_url( __FILE__ ) . '../images/recaptcha-icon.png' ) . '" /> reCAPTCHA',
		);
		$links = array();
		echo '<div id="icon-themes" class="icon32"><br></div>';
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $tab => $name ) {
			$class = ( $tab === $current ) ? ' nav-tab-active' : '';
			echo '<a class="nav-tab' . esc_attr( $class ) . '" href="options-general.php?page=yikes-custom-login&tab=' . esc_attr( $tab ) . '">' . wp_kses_post( $name ) . '</a>';
		}
		echo '</h2>';
	}

	/**
	* Options page callback
	*/
	public function create_admin_page() {
		// Store the options by retreiving it from our parent class
		$this->options = YIKES_Custom_Login::get_yikes_custom_login_options();
		$tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'general';
		?>
		<!-- Begin Options Content -->
		<div class="wrap">

			<div id="icon-options-general" class="icon32"></div>

			<h1><?php esc_attr_e( 'Custom Login Settings', 'yikes-inc-custom-login' ); ?></h1>

			<p class="description"><?php esc_attr_e( 'Adjust the settings for the custom login plugin below.', 'yikes-inc-custom-login' ); ?></p>

			<?php
			// Store our tab
			$tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'general';
			// Generate our settings tabs
			$this->yikes_admin_tabs( $tab );
			?>

			<div id="poststuff">

				<div id="post-body" class="metabox-holder columns-2">

					<!-- main content -->
					<div id="post-body-content">

						<div class="meta-box-sortables ui-sortable">

							<div class="postbox">

								<div class="inside">

									<form method="post" action="options.php" class="yikes-custom-login-settings-<?php echo esc_attr( $tab ); ?>">
										<?php
										// This prints out all hidden setting fields
										settings_fields( 'yikes_custom_login_option_group' );
										do_settings_sections( 'yikes-custom-login' );
										submit_button();
										?>
									</form>

								</div>
								<!-- .inside -->

							</div>
							<!-- .postbox -->

						</div>
						<!-- .meta-box-sortables .ui-sortable -->

					</div>
					<!-- post-body-content -->

					<!-- sidebar -->
					<div id="postbox-container-1" class="postbox-container options-sidebar">

						<div class="meta-box-sortables">

							<div class="postbox">

								<div class="inside">
									<a href="https://yikesplugins.com/" title="YIKES Plugins" target="_blank" class="yikes-plugins-logo-link">
										<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../images/yikes-plugins-logo.png' ); ?>" class="yikes-plugins-logo" alt="<?php esc_attr_e( 'Yikes Plugins', 'yikes-inc-custom-login' ); ?>" />
									</a>
								</div>
								<!-- .inside -->

							</div>
							<!-- .postbox -->

						</div>
						<!-- .meta-box-sortables -->

					</div>
					<!-- #options-sidebar .postbox-container -->

				</div>
				<!-- #post-body .metabox-holder .columns-2 -->

				<br class="clear">
			</div>
			<!-- #poststuff -->

		</div> <!-- .wrap -->
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		// Generate Setting
		register_setting(
			'yikes_custom_login_option_group', // Option group
			'yikes_custom_login', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		/* Add General Settings Section */
		add_settings_section(
			'yikes_custom_login_general_section', // ID
			'', // Title
			array( $this, 'print_section_info' ), // Callback
			'yikes-custom-login' // Page
		);

		// Admin Redirection Setting
		add_settings_field(
			'admin_redirect', // ID
			__( 'Admin Redirect', 'yikes-inc-custom-login' ), // Title
			array( $this, 'admin_redirect_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_general_section' // Section
		);
		// Admin Redirection Setting
		add_settings_field(
			'restrict_dashboard_access', // ID
			__( 'Restrict Dashboard Access', 'yikes-inc-custom-login' ), // Title
			array( $this, 'restrict_dashboard_access_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_general_section' // Section
		);
		// Admin Redirection Setting
		add_settings_field(
			'password_strength_meter', // ID
			__( 'Password Strength Meter', 'yikes-inc-custom-login' ), // Title
			array( $this, 'password_strength_meter_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_general_section' // Section
		);
		/* Notice Animations */
		add_settings_field(
			'notice_anmation', // ID
			'Notice Animation', // Title
			array( $this, 'notice_anmation_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_general_section' // Section
		);

		/** Add Pages Settings Section **/
		add_settings_section(
			'yikes_custom_login_pages_section', // ID
			'', // Title
			array( $this, 'print_section_info' ), // Callback
			'yikes-custom-login' // Page
		);

		/* Login Page Option */
		add_settings_field(
			'login_page', // ID
			'Login Page', // Title
			array( $this, 'page_select_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_pages_section', // Section
			array(
				'field' => 'login_page',
			)
		);

		/* Account Info Page Option */
		add_settings_field(
			'account_info_page', // ID
			'Account Page', // Title
			array( $this, 'page_select_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_pages_section', // Section
			array(
				'field' => 'account_info_page',
			)
		);

		/* Registration PAge Option */
		add_settings_field(
			'register_page', // ID
			'Registration Page', // Title
			array( $this, 'page_select_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_pages_section', // Section
			array(
				'field' => 'register_page',
			)
		);

		/* Login Page Option */
		add_settings_field(
			'password_lost_page', // ID
			'Reset Password Page', // Title
			array( $this, 'page_select_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_pages_section', // Section
			array(
				'field' => 'password_lost_page',
			)
		);

		/** Add Recaptcha Settings Section **/
		add_settings_section(
			'yikes_custom_login_recaptcha_section', // ID
			'', // Title
			array( $this, 'print_section_info' ), // Callback
			'yikes-custom-login' // Page
		);

		/* reCAPTCHA Site Key Option */
		add_settings_field(
			'recaptcha_site_key', // ID
			'Site Key', // Title
			array( $this, 'recaptcha_field_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_recaptcha_section', // Section
			array(
				'field' => 'recaptcha_site_key',
			)
		);

		/* reCAPTCHA Secret Key Option */
		add_settings_field(
			'recaptcha_secret_key', // ID
			'Secret Key', // Title
			array( $this, 'recaptcha_field_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_recaptcha_section', // Section
			array(
				'field' => 'recaptcha_secret_key',
			)
		);
	}

	/**
	* Sanitize each setting field as needed
	*
	* @param array $input Contains all settings fields as array keys
	*/
	public function sanitize( $input ) {
		$new_input = array();
		// Admin Redirect Sanitization
		$new_input['admin_redirect'] = ( isset( $input['admin_redirect'] ) ) ? absint( $input['admin_redirect'] ) : 0;
		// Restrict Dashboard Access Sanitization
		$new_input['restrict_dashboard_access'] = ( isset( $input['restrict_dashboard_access'] ) ) ? 1 : 0;
		// Use password strength meter and enforce strong passwords
		$new_input['password_strength_meter'] = ( isset( $input['password_strength_meter'] ) ) ? 1 : 0;
		// Notice animations
		$new_input['notice_animation'] = ( isset( $input['notice_animation'] ) ) ? $input['notice_animation'] : 'none';
		// Login Page
		$new_input['register_page'] = ( isset( $input['register_page'] ) ) ? $input['register_page'] : $this->options['register_page'];
		// Login Page
		$new_input['login_page'] = ( isset( $input['login_page'] ) ) ? $input['login_page'] : $this->options['login_page'];
		// Login Page
		$new_input['account_info_page'] = ( isset( $input['account_info_page'] ) ) ? $input['account_info_page'] : $this->options['account_info_page'];
		// Login Page
		$new_input['password_lost_page'] = ( isset( $input['password_lost_page'] ) ) ? $input['password_lost_page'] : $this->options['password_lost_page'];
		// Recaptcha Site Key
		$new_input['recaptcha_site_key'] = ( isset( $input['recaptcha_site_key'] ) ) ? $input['recaptcha_site_key'] : false;
		// Recaptcha Secret
		$new_input['recaptcha_secret_key'] = ( isset( $input['recaptcha_secret_key'] ) ) ? $input['recaptcha_secret_key'] : false;
		// Return the saved data
		return $new_input;
	}

	/**
	* Print the Section text
	*/
	public function print_section_info() {
		// echo esc_html( 'Enter your settings below:' );
	}

	/**
	 * Render the checkbox to display the 'Admin Redirect' checkbox
	 */
	public function admin_redirect_callback() {
		/* Field */
		printf(
			'<input type="checkbox" id="admin_redirect" name="yikes_custom_login[admin_redirect]" value="1" %s />',
			checked( $this->options['admin_redirect'], 1, false )
		);
		/* Description */
		printf(
			'<p class="description">%s</p>',
			esc_attr__( 'Redirect admins to /wp-admin/ on login?', 'yikes-inc-custom-login' )
		);
	}

	/**
	 * Restrict dashboard access from certain users
	 */
	public function restrict_dashboard_access_callback() {
		/* Field */
		printf(
			'<input type="checkbox" id="restrict_dashboard_access" name="yikes_custom_login[restrict_dashboard_access]" value="1" %s />',
			checked( $this->options['restrict_dashboard_access'], 1, false )
		);
		/* Description */
		printf(
			'<p class="description">%s</p>',
			esc_attr__( 'Restrict access to the dashboaord (/wp-admin/) from non-admins?', 'yikes-inc-custom-login' )
		);
		/* Display notice about who will be blocked */
		printf(
			'<p class="description">%s %s</p>',
			esc_attr__( 'The following users will not have access to the dashboard:', 'yikes-inc-custom-login' ),
			wp_kses_post( self::get_restricted_users() )
		);
	}

	/**
	 * Restrict dashboard access from certain users
	 */
	public function password_strength_meter_callback() {
		/* Field */
		printf(
			'<input type="checkbox" id="password_strength_meter" name="yikes_custom_login[password_strength_meter]" value="1" %s />',
			checked( $this->options['password_strength_meter'], 1, false )
		);
		/* Description */
		printf(
			'<p class="description">%s</p>',
			esc_attr__( 'Display the WordPress strength meter and encforce strong passwords?', 'yikes-inc-custom-login' )
		);
	}
	/**
	 * Get a complete list of users who are going to be restricted from the dashboard
	 * @return string Comma delimited string of restricted user roles.
	 */
	private static function get_restricted_users() {
		$all_user_roles = get_editable_roles();
		/* Allow users to decide who can access the dashboard by capability */
		$user_cap = apply_filters( 'yikes-custom-login-restrict-dashboard-capability', 'manage_options' );
		/* Create empty array for user roles */
		$specific_user_roles = array();
		/* Loop and populate the array */
		foreach ( $all_user_roles as $user_role_name => $user_role_data ) {
			/* Loop over the capabilities and push to our array */
			if ( ! isset( $user_role_data['capabilities'][ $user_cap ] ) || 0 === $user_role_data['capabilities'][ $user_cap ] ) {
				$specific_user_roles[] = ucfirst( $user_role_name );
			}
		}
		/* Return our string of roles */
		return '<code>' . implode( ', ', $specific_user_roles ) . '</code>';
	}

	/**
	* Generate the dropdown for our animation styles
	*/
	public function notice_anmation_callback() {
		/* Create our possible animations array */
		$animations = apply_filters( 'yikes-custom-login-notice-animations', array(
			'none' => __( 'No Animation', 'yikes-inc-custom-login' ),
			'yikes-fadeIn' => __( 'Fade In', 'yikes-inc-custom-login' ),
			'yikes-fadeInDown' => __( 'Fade In Down', 'yikes-inc-custom-login' ),
		) );
		?>
			<select id="notice_animation" name="yikes_custom_login[notice_animation]">
				<?php
				/** Loop and generate our options from the array above **/
				foreach ( $animations as $animation => $animation_name ) {
					/* Print the option */
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $animation ),
						esc_attr( selected( $this->options['notice_animation'], $animation, false ) ),
						esc_attr( $animation_name )
					);
				}
				?>
			</select>
		<?php
		/* Description */
		printf(
			'<p class="description">%s</p>',
			esc_attr__( 'Why type of animation should be used when displaying notices to the user?', 'yikes-inc-custom-login' )
		);
	}

	/**
	 * Render our select 2 field
	 */
	public function page_select_callback( $args ) {
		// Check for an existing transient for page load times
		if ( false === ( $pages_query = get_transient( 'yikes_custom_login_pages_query' ) ) ) {
			/* Query all pages */
			$pages_query = new WP_Query( array(
				'post_type' => 'page',
				'post_status' => 'publish',
				'posts_per_page' => -1,
			) );
			/* Setup our transient for 24 hours */
			set_transient( 'yikes_custom_login_pages_query', $pages_query, 24 * HOUR_IN_SECONDS );
		}
		// if pages are found
		if ( $pages_query->have_posts() ) {
			?>
			<select class="yikes-select2" name="yikes_custom_login[<?php echo esc_attr( $args['field'] ); ?>]">
				<?php
				while ( $pages_query->have_posts() ) {
					$pages_query->the_post();
					// Loop over each page and create an option
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( get_the_ID() ),
						esc_attr( selected( $this->options[ $args['field'] ], get_the_ID() ) ),
						esc_attr( get_the_title() )
					);
				}
				?>
			</select>
			<?php
		}
	}

	/**
	 * Render our recaptcah site and secret key fields
	 */
	public function recaptcha_field_callback( $args ) {
		$recaptcha_key = $this->options[ $args['field'] ];
		/* Field */
		printf(
			'<input type="text" id="' . esc_attr( $args['field'] ) . '" name="yikes_custom_login[' . esc_attr( $args['field'] ) . ']" value="%s" class="widefat" placeholder="%s">',
			esc_attr( $recaptcha_key ),
			esc_attr( ucwords( str_replace( 'recaptcha ', '', str_replace( '_', ' ', $args['field'] ) ) ) )
		);
		/* Descriptions */
		printf(
			'<p class="description">%s</p>',
			sprintf( esc_attr__( 'Enter your %s in the field above.', 'yikes-inc-custom-login' ), '<strong>' . esc_attr( str_replace( '_', ' ', $args['field'] ) ) . '</strong>' )
		);
	}

	/**
	 * Enqueue our styles properly on the admin side options page
	 * @since 1.0.0
	 */
	public function yikes_custom_login_load_option_styles() {
		// Enqueue the options styles
		wp_enqueue_style( 'yikes-admin-styles', plugin_dir_url( __FILE__ ) . '../css/min/yikes-custom-login-admin.min.css', array(), YIKES_CUSTOM_LOGIN_VERSION );
	}
} // End Class


// Iniitalize the settings page
if ( is_admin() ) {
	$yikes_login_settings = new YIKES_Login_Settings();
}

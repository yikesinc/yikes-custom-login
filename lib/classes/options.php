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
		// Filter the tabs to allow for add-ons
		$tabs = apply_filters( 'yikes-custom-login-settings-tabs', array(
			'general' => __( 'General', 'yikes-inc-custom-login' ),
			'pages' => __( 'Pages', 'yikes-inc-custom-login' ),
			'recaptcha' => '<img class="recaptcha-icon" src="' . esc_url( plugin_dir_url( __FILE__ ) . '../images/recaptcha-icon.png' ) . '" /> reCAPTCHA',
			'branding' => __( 'Branding', 'yikes-inc-custom-login' ),
		) );
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

							<!-- YIKES Plugins Logo -->
							<div class="postbox">
								<div class="inside">
									<a href="https://yikesplugins.com/" title="YIKES Plugins" target="_blank" class="yikes-plugins-logo-link">
										<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../images/yikes-plugins-logo.png' ); ?>" class="yikes-plugins-logo" alt="<?php esc_attr_e( 'Yikes Plugins', 'yikes-inc-custom-login' ); ?>" />
									</a>
								</div>
								<!-- .inside -->
							</div>
							<!-- .postbox -->

							<!-- Rate & Review Box -->
							<div class="postbox">
								<div class="inside rate-and-review-container">
									<h2><?php esc_attr_e( 'Loving This Plugin?', 'yikes-inc-custom-login' ); ?></h2>
									<div class="rate-and-review">
										<a href="#" target="_blank">
											<span class="dashicons dashicons-star-filled"></span>
											<span class="dashicons dashicons-star-filled"></span>
											<span class="dashicons dashicons-star-filled"></span>
											<span class="dashicons dashicons-star-filled"></span>
											<span class="dashicons dashicons-star-filled"></span>
										</a>
										<p class="description rate-and-review-text">
											<a href="#" title="<?php esc_attr_e( 'Rate this plugin!', 'yikes-inc-custom-login' ); ?>" target="_blank">
												<?php esc_attr_e( 'Rate this plugin!', 'yikes-inc-custom-login' ); ?>
											</a>
										</p>
									</div>
									<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://#" data-text="I'm using the Custom Login plugin by @yikesinc - wow, it's powerful!" data-via="yikesinc">Tweet</a>
									<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
									<p class="description tweet-experience-text"><?php esc_attr_e( 'Tweet Your Experience', 'yikes-inc-custom-login' ); ?></p>
								</div>
								<!-- .inside -->
							</div>
							<!-- .postbox -->

							<!-- Documentation -->
							<div class="postbox">
								<div class="inside support-and-docs-container">
									<h2><?php esc_attr_e( 'Support & Documentation', 'yikes-inc-custom-login' ); ?></h2>
									<p class="support-and-docs-text description">
										<?php esc_attr_e(
											'YIKES Inc. proudly supports and stands behind all of our products. If you run into any issues, please reach out to our support staff and we can help get any issues sorted - or answer any questions you may have.',
											'yikes-inc-custom-login'
										); ?>
									</p>
									<a href="#" class="button button-secondary doc-link wp-bg"><?php esc_attr_e( 'WordPress.org', 'yikes-inc-custom-login' ); ?></a>
									<a href="#" class="button button-secondary doc-link github-bg"><?php esc_attr_e( 'Github.com', 'yikes-inc-custom-login' ); ?></a>
									<a href="#" class="button button-secondary doc-link docs-bg"><?php esc_attr_e( 'Documentation', 'yikes-inc-custom-login' ); ?></a>
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
			__( 'Notice Animation', 'yikes-inc-custom-login' ), // Title
			array( $this, 'notice_anmation_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_general_section' // Section
		);
		// Admin Redirection Setting
		add_settings_field(
			'powered_by_yikes', // ID
			__( 'Display "Powered by YIKES Plugins"', 'yikes-inc-custom-login' ), // Title
			array( $this, 'powered_by_yikes_callback' ), // Callback
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
			__( 'Login Page', 'yikes-inc-custom-login' ), // Title
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
			__( 'Account Page', 'yikes-inc-custom-login' ), // Title
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
			__( 'Registration Page', 'yikes-inc-custom-login' ), // Title
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
			__( 'Reset Password Page', 'yikes-inc-custom-login' ), // Title
			array( $this, 'page_select_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_pages_section', // Section
			array(
				'field' => 'password_lost_page',
			)
		);

		/* Login Page Option */
		add_settings_field(
			'pick_new_password_page', // ID
			__( 'Select New Password Page', 'yikes-inc-custom-login' ), // Title
			array( $this, 'page_select_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_pages_section', // Section
			array(
				'field' => 'pick_new_password_page',
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
			__( 'Site Key', 'yikes-inc-custom-login' ), // Title
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
			__( 'Secret Key', 'yikes-inc-custom-login' ), // Title
			array( $this, 'recaptcha_field_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_recaptcha_section', // Section
			array(
				'field' => 'recaptcha_secret_key',
			)
		);

		/** Add Branding Settings Section **/
		add_settings_section(
			'yikes_custom_login_branding_section', // ID
			'', // Title
			array( $this, 'print_section_info' ), // Callback
			'yikes-custom-login' // Page
		);

		/* Test Branding Option */
		add_settings_field(
			'branding_logo', // ID
			__( 'Site Logo', 'yikes-inc-custom-login' ), // Title
			array( $this, 'logo_field_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_branding_section' // Section
		);

		// Custom action hook to add additional settings sections/fields
		do_action( 'yikes-custom-login-add-settings' );
	}

	/**
	* Sanitize each setting field as needed
	*
	* @param array $input Contains all settings fields as array keys
	*/
	public function sanitize( $input ) {
		$new_input = array();
		// Admin Redirect Sanitization
		$new_input['admin_redirect'] = ( isset( $input['admin_redirect'] ) ) ? absint( $input['admin_redirect'] ) : (int) 0;
		// Restrict Dashboard Access Sanitization
		$new_input['restrict_dashboard_access'] = ( isset( $input['restrict_dashboard_access'] ) ) ? (int) 1 : (int) 0;
		// Use password strength meter and enforce strong passwords
		$new_input['password_strength_meter'] = ( isset( $input['password_strength_meter'] ) ) ? (int) 1 : (int) 0;
		// Notice animations
		$new_input['notice_animation'] = ( isset( $input['notice_animation'] ) ) ? sanitize_text_field( $input['notice_animation'] ) : 'none';
		// Full Width Page Templates
		$new_input['powered_by_yikes'] = ( isset( $input['powered_by_yikes'] ) ) ? (int) 1 : (int) 0;
		// Registration Page
		$new_input['register_page'] = ( isset( $input['register_page'] ) ) ? (int) $input['register_page'] : (int) $this->options['register_page'];
		// Login Page
		$new_input['login_page'] = ( isset( $input['login_page'] ) ) ? (int) $input['login_page'] : (int) $this->options['login_page'];
		// Account Info Page
		$new_input['account_info_page'] = ( isset( $input['account_info_page'] ) ) ? (int) $input['account_info_page'] : (int) $this->options['account_info_page'];
		// Password Lost Page
		$new_input['password_lost_page'] = ( isset( $input['password_lost_page'] ) ) ? (int) $input['password_lost_page'] : (int) $this->options['password_lost_page'];
		// Password Lost Page
		$new_input['pick_new_password_page'] = ( isset( $input['pick_new_password_page'] ) ) ? (int) $input['pick_new_password_page'] : (int) $this->options['pick_new_password_page'];
		// Recaptcha Site Key
		$new_input['recaptcha_site_key'] = ( isset( $input['recaptcha_site_key'] ) ) ? sanitize_text_field( $input['recaptcha_site_key'] ) : false;
		// Recaptcha Secret
		$new_input['recaptcha_secret_key'] = ( isset( $input['recaptcha_secret_key'] ) ) ? sanitize_text_field( $input['recaptcha_secret_key'] ) : false;
		// Branidng Logo
		$new_input['branding_logo'] = ( isset( $input['branding_logo'] ) ) ? sanitize_url( $input['branding_logo'] ) : '';
		// Branding Logo ID (hidden field)
		$new_input['branding_logo_id'] = ( isset( $input['branding_logo'] ) && '' !== $input['branding_logo'] ) ? (int) $input['branding_logo_id'] : '';
		// Return the saved data (filter to allow for additional settings to be saved)
		return apply_filters( 'yikes-custom-login-sanitize-settings', $new_input, $input );
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
	 * Render the checkbox to display the 'Powered by YIKES' checkbox
	 */
	public function powered_by_yikes_callback() {
		/* Field */
		printf(
			'<input type="checkbox" id="powered_by_yikes" name="yikes_custom_login[powered_by_yikes]" value="1" %s />',
			checked( $this->options['powered_by_yikes'], 1, false )
		);
		/* Description */
		printf(
			'<p class="description">%s</p>',
			esc_attr__( 'Display small text on the full width page templates linking back to YIKES?', 'yikes-inc-custom-login' )
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
	 * Render the 'Logo' field in the Branding Tab
	 */
	public function logo_field_callback() {
		// Branding Logo URL
		printf(
			'<input type="text" id="branding_logo" name="yikes_custom_login[branding_logo]" value="%s" class="widefat" placeholder="%s">',
			esc_attr( $this->options['branding_logo'] ),
			esc_attr__( 'Site Logo', 'yikes-inc-custom-login' )
		);
		// Branding Logo ID
		printf(
			'<input type="hidden" id="branding_logo_id" name="yikes_custom_login[branding_logo_id]" value="%s">',
			esc_attr( $this->options['branding_logo_id'] )
		);
		$branding_preview_class = ( '' !== $this->options['branding_logo_id'] ) ? 'preview-active' : '';
		// Branding Logo URL
		printf(
			'<div class="branding_logo_preview %s">%s %s</div>',
			esc_attr( $branding_preview_class ),
			wp_kses_post( '<span class="dashicons dashicons-no remove-branding-logo"></span>' ),
			wp_kses_post( '<img src="' . $this->options['branding_logo'] . '" />' )
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

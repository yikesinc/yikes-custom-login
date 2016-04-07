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
	* Options page callback
	*/
	public function create_admin_page() {
		// Store the options by retreiving it from our parent class
		$this->options = YIKES_Custom_Login::get_yikes_custom_login_options();
		?>
		<!-- Begin Options Content -->
		<div class="wrap">

			<div id="icon-options-general" class="icon32"></div>

			<h1><?php esc_attr_e( 'Custom Login Settings', 'yikes-inc-custom-login' ); ?></h1>

			<p class="description"><?php esc_attr_e( 'Adjust the settings for the custom login plugin below.', 'yikes-inc-custom-login' ); ?></p>
			<div id="poststuff">

				<div id="post-body" class="metabox-holder columns-2">

					<!-- main content -->
					<div id="post-body-content">

						<div class="meta-box-sortables ui-sortable">

							<div class="postbox">

								<div class="inside">

									<form method="post" action="options.php">
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
		register_setting(
			'yikes_custom_login_option_group', // Option group
			'yikes_custom_login', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

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
		/* Notice Animations */
		add_settings_field(
			'notice_anmation', // ID
			'Notice Animation', // Title
			array( $this, 'notice_anmation_callback' ), // Callback
			'yikes-custom-login', // Page
			'yikes_custom_login_general_section' // Section
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
		// Notice animations
		$new_input['notice_animation'] = ( isset( $input['notice_animation'] ) ) ? $input['notice_animation'] : 'none';
		// Title
		if ( isset( $input['title'] ) ) {
			$new_input['title'] = sanitize_text_field( $input['title'] );
		}
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
}

// Iniitalize the settings page
if ( is_admin() ) {
	wp_enqueue_style( 'yikes-admin-styles', plugin_dir_url( __FILE__ ) . '../css/min/yikes-custom-login-admin.min.css', array(), YIKES_CUSTOM_LOGIN_VERSION );
	$yikes_login_settings = new YIKES_Login_Settings();
}

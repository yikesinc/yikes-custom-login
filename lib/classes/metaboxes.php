<?php
/**
 * Full Width Page Metaboxes
 * @since 1.0
 */
class YIKES_Custom_Login_Metaboxes extends YIKES_Custom_Login {

	// store our options
	private $options;

	public function __construct( $options ) {
		$this->options = $options;
		add_action( 'add_meta_boxes', array( $this, 'yikes_custom_login_register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'yikes_custom_login_save_meta_box' ), 10, 2 );
		add_action( 'admin_init' , array( $this, 'remove_full_width_template_page_fields' ) );
		add_action( 'admin_notices', array( $this, 'full_width_template_notices' ) );
	}

	/**
	 * Return an array of page id's that metaboxes
	 * are enabled on.
	 * @return array Array of page IDs set on the settings page.
	 */
	public function get_full_width_page_ids() {
		return array(
			$this->options['login_page'],
			$this->options['pick_new_password_page'],
			$this->options['password_lost_page'],
			$this->options['register_page'],
		);
	}
	/**
	 * Register meta box(es).
	 */
	public function yikes_custom_login_register_meta_boxes() {
		global $post;
		// setup page ids array
		$page_ids = $this->get_full_width_page_ids();
		// if not one of our pages, abort
		if ( ! in_array( $post->ID, $page_ids ) ) {
			return;
		}
		add_meta_box(
			'full-width=page-template',
			__( 'YIKES Custom Login', 'textdomain' ),
			array( $this, 'yikes_custom_login_full_width_template_metabox_callback' ),
			'page'
		);
	}

	/**
	 * Clean up the page when full width templates is enabled
	 * @since 1.0
	 */
	public function remove_full_width_template_page_fields() {
		// setup page ids array
		$page_ids = $this->get_full_width_page_ids();
		$post_id = ( isset( $_GET['post'] ) ) ? $_GET['post'] : false;
		// if we're not on a post/page with an ID - abort
		if ( ! $post_id ) {
			return;
		}
		// if we are on one of our pages, and the full width page template is enabled,
		// remove the content editor (since it does nothing)
		if ( in_array( $post_id, $page_ids ) ) {
			// If the full page template is not set, abort
			if ( ! get_post_meta( $post_id, '_full_width_page_template', true ) ) {
				return;
			}
			// Remove the content editor - since it does nothing now
			remove_post_type_support( 'page', 'editor' );
			remove_post_type_support( 'page', 'thumbnail' );
			remove_post_type_support( 'page', 'pageparentdiv' );
			remove_meta_box( 'pageparentdiv', 'page', 'side' );
			remove_meta_box( 'revisionsdiv', 'page', 'normal' );
			remove_meta_box( 'commentsdiv', 'page', 'normal' );
			remove_meta_box( 'commentstatusdiv', 'page', 'normal' );
			remove_meta_box( 'slugdiv', 'page', 'normal' );
			remove_meta_box( 'authordiv', 'page', 'normal' );
			remove_meta_box( 'postcustom', 'page', 'normal' );
		}
	}

	/**
	 * Display an explination of why the admin section looks the way it does
	 * (eg: where did the meta fields go, how to disable, adjus settings etc.)
	 * @return [type] [description]
	 */
	public function full_width_template_notices() {
		global $post, $pagenow;
		// setup page ids array
		$page_ids = $this->get_full_width_page_ids();
		$post_id = ( isset( $post->ID ) ) ? $post->ID : false;
		// if were not on a new page / edit page screen - abort
		if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			return;
		}
		// if we're not on a post/page with an ID - abort
		if ( ! $post_id ) {
			return;
		}
		$page_name = $this->get_custom_login_page_name( $post_id );
		// if we are on one of our pages, and the full width page template is enabled,
		// remove the content editor (since it does nothing)
		if ( in_array( $post_id, $page_ids ) ) {
			// If the full page template is not set, abort
			if ( ! get_post_meta( $post_id, '_full_width_page_template', true ) ) {
				return;
			}
			// Display our admin notice
			$class = 'notice notice-warning';
			$message = __( 'Full width page template is now active. You can use the button below to customize this page.', 'sample-text-domain' );
			$message2 = sprintf(
				wp_kses_post(
					_x( 'To disable the full width page template, uncheck the setting below - and update this page. To switch which page is set as the %s page, visit the %s.', 'First: The Current Page Name (login, register etc.), Second: <a> tag linking to the settings page.', 'custom-wp-login' )
				),
				'<strong>' . $page_name . '</strong>',
				'<a href="' . esc_url( admin_url( 'options-general.php?page=yikes-custom-login&tab=pages' ) ) . '">' . __( 'settings page', 'custom-wp-login' ) . '</a>'
			);
			printf(
				'<div class="%1$s"><p>%2$s</p><p>%3$s</div>',
				esc_attr( $class ),
				wp_kses_post( $message ),
				wp_kses_post( $message2 )
			);
		}
	}

	/**
	 * Return the name of the current page, if it's set
	 * @param  int $page_id The page ID to retreive
	 * @return string       The name of the current page.
	 */
	public function get_custom_login_page_name( $page_id ) {
		if ( ! $page_id ) {
			return 'Whoops, you forgot to specify a page ID inside of your get_custom_login_page_name(); call.';
		}
		switch ( $page_id ) {
			case $this->options['register_page']:
				$page_name = __( 'Registration Page', 'custom-wp-login' );
				break;
			case $this->options['password_lost_page']:
				$page_name = __( 'Reset Password Page', 'custom-wp-login' );
				break;
			case $this->options['pick_new_password_page']:
				$page_name = __( 'Select New Password Page', 'custom-wp-login' );
				break;
			case $this->options['login_page']:
				$page_name = __( 'Login Page', 'custom-wp-login' );
				break;
			default:
				$page_name = false;
				break;
		}
		return $page_name;
	}

	/**
	 * Meta box display callback.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function yikes_custom_login_full_width_template_metabox_callback( $post ) {
		$page_name = $this->get_custom_login_page_name( $post->ID );
		$active_template = ( get_post_meta( $post->ID, '_full_width_page_template', true ) ) ? true : false;
		$page_ids = $this->get_full_width_page_ids();
		$sign_in_active_template = ( get_post_meta( $page_ids[0], '_full_width_page_template', true ) ) ? true : false;
		// Setup our customizer URL
		$customizer_link = add_query_arg( array(
			'url' => esc_url( get_the_permalink( $this->options['login_page'] ) ),
		), esc_url_raw( admin_url( 'customize.php' ) ) );
		// Nonce field
		wp_nonce_field( 'yikes_metabox_nonce_action', 'yikes_metabox_nonce' );
		// Display code/markup goes here. Don't forget to include nonces!
		?>
		<label for="_full_width_page_template">
			<input type="checkbox" id="_full_width_page_template" <?php if ( $active_template ) { echo 'checked="checked"'; } ?> name="_full_width_page_template" />
			<?php esc_attr_e( 'Use Full Width Template', 'custom-wp-login' ); ?>
		</label>
		<?php
		// If this is the login page template, display a 'Customize Template' link
		if ( $active_template && $page_ids[0] == $post->ID ) {
			printf(
				wp_kses_post( '<p><a href="' . $customizer_link . '" class="button button-primary yikes-login-customize-link">%s</a></p>' ),
				esc_attr__( 'Customize Page', 'custom-wp-login' )
			);
		}
		printf(
			wp_kses_post( '<p class="description" style="margin-top:1em;"><small>' . _x( 'This metabox appears here because you have this set as the %s on the custom login %s.', 'First: The Current Page Name (login, register etc.), Second: <a> tag linking to the settings page.', 'custom-wp-login' ) . '</small></p>' ),
			'<strong>' . esc_attr( $page_name ) . '</strong>',
			'<a href="' . esc_url( admin_url( 'options-general.php?page=yikes-custom-login&tab=pages' ) ) . '">' . esc_attr__( 'options page', 'custom-wp-login' ) . '</a>'
		);
		// If the sign in template page is active, this page template is active and the current page is NOT equal to the sign in page
		if ( $sign_in_active_template && $active_template && $page_ids[0] != $post->ID ) {
			printf(
				wp_kses_post( '<p class="descipriton"><small>' . _x( 'This template inherits the styles from the %s page. To adjust the styles, please customize the %s page.', 'First: name of the login page, Second: <a> tag linking to customize the login page.', 'custom-wp-login' ) . '</small></p>' ),
				'<strong>' . __( 'Login', 'custom-wp-login' ) . '</strong>',
				'<a href="' . $customizer_link . '">' . esc_attr__( 'Login', 'custom-wp-login' ) . '</a>'
			);
		}
	}

	/**
	 * Save meta box content.
	 *
	 * @param int $post_id Post ID
	 */
	public function yikes_custom_login_save_meta_box( $post_id, $post ) {
		// setup page ids array
		$page_ids = $this->get_full_width_page_ids();
		// if not one of our pages, abort
		if ( ! in_array( $post->ID, $page_ids ) ) {
			return;
		}
		// Validate nonce
		if ( ! isset( $_POST['yikes_metabox_nonce'] ) ) {
			return;
		}
		// check the validity of the nonce
		if ( ! wp_verify_nonce( $_POST['yikes_metabox_nonce'], 'yikes_metabox_nonce_action' ) ) {
			return;
		}
		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}
		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( isset( $_POST['_full_width_page_template'] ) && 'on' === $_POST['_full_width_page_template'] ) {
			// set/update the post meta
			update_post_meta( $post_id, '_full_width_page_template', 1 );
		} else {
			// remove the post meta
			delete_post_meta( $post_id, '_full_width_page_template' );
		}
	}
}
$yikes_metabox_class = new YIKES_Custom_Login_Metaboxes( $this->options );

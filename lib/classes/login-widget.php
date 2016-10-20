<?php
/**
 * Custom Login/Account Details Widget
 * @since 1.0
 */
// Creating the widget
class YIKES_Custom_Login_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'wpb_widget',

			// Widget name will appear in UI
			__( 'YIKES Custom Login Widget', 'custom-wp-login' ),

			// Widget description
			array( 'description' => __( 'Display a login form or account details if the user is logged in.', 'custom-wp-login' ), )
		);
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_styles' ) );
	}

	/**
	 * Enqueue our frontend styles where this widget is used
	 * @since 1.0
	 */
	public function enqueue_frontend_styles() {
		wp_enqueue_style( 'yikes-custom-login-public', YIKES_CUSTOM_LOGIN_URL . '/lib/css/min/yikes-custom-login-public.min.css', array(), YIKES_CUSTOM_LOGIN_VERSION );
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$title = ( is_user_logged_in() ) ? apply_filters( 'widget_title', $instance['logged_in_title'] ) : apply_filters( 'widget_title', $instance['non_logged_in_title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		echo do_shortcode( '[custom-login-form]' );
		echo $args['after_widget'];
	}

	// Widget Backend
	public function form( $instance ) {
		// not logged in user widget title
		$non_logged_in_title = ( isset( $instance[ 'non_logged_in_title' ] ) ) ? $instance[ 'non_logged_in_title' ] : __( 'Login', 'custom-wp-login' );
		// logged in user widget title
		$logged_in_title = ( isset( $instance[ 'logged_in_title' ] ) ) ? $instance[ 'logged_in_title' ] : __( 'Account Details', 'custom-wp-login' );
		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'non_logged_in_title' ); ?>"><?php _e( 'Non-Logged In Titlte:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'non_logged_in_title' ); ?>" name="<?php echo $this->get_field_name( 'non_logged_in_title' ); ?>" type="text" value="<?php echo esc_attr( $non_logged_in_title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'logged_in_title' ); ?>"><?php _e( 'Logged In Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'logged_in_title' ); ?>" name="<?php echo $this->get_field_name( 'logged_in_title' ); ?>" type="text" value="<?php echo esc_attr( $logged_in_title ); ?>" />
		</p>
		<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['non_logged_in_title'] = ( ! empty( $new_instance['non_logged_in_title'] ) ) ? strip_tags( $new_instance['non_logged_in_title'] ) : '';
		$instance['logged_in_title'] = ( ! empty( $new_instance['logged_in_title'] ) ) ? strip_tags( $new_instance['logged_in_title'] ) : '';
		return $instance;
	}
} // Class wpb_widget ends here

// Register and load the widget
function yikes_login_widget() {
	register_widget( 'YIKES_Custom_Login_Widget' );
}
add_action( 'widgets_init', 'yikes_login_widget' );

<?php
/**
 * Custom Login Form Template
 * @since 1.0.0
 */

// If accessed directly, abort
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="login-form-container section group">

	<?php
		/** Custom Action Hook - Before Login Form */
		do_action( 'yikes-custom-login-before-login-form' );
	?>

	<?php if ( $attributes['show_title'] ) : ?>
		<h2><?php esc_attr_e( 'Sign In', 'custom-wp-login' ); ?></h2>
	<?php endif; ?>

	<!-- Show errors if there are any -->
	<?php if ( count( $attributes['errors'] ) > 0 ) : ?>
		<?php foreach ( $attributes['errors'] as $error ) : ?>
			<p class="login-error yikes-custom-login-alert yikes-custom-login-alert-danger yikes-animated <?php echo esc_attr( $this->options['notice_animation'] ); ?>">
				<?php echo wp_kses_post( $error ); ?>
			</p>
		<?php endforeach; ?>
	<?php endif; ?>

	<!-- Show logged out message if user just logged out -->
	<?php if ( 'true' === $attributes['logged_out'] ) : ?>
		<p class="login-info yikes-custom-login-alert yikes-custom-login-alert-success yikes-animated <?php echo esc_attr( $this->options['notice_animation'] ); ?>">
			<?php esc_attr_e( 'You have logged out.', 'custom-wp-login' ); ?>
		</p>
	<?php endif; ?>

	<?php if ( $attributes['registered'] ) : ?>
		<p class="login-info yikes-custom-login-alert yikes-custom-login-alert-success yikes-animated <?php echo esc_attr( $this->options['notice_animation'] ); ?>">
			<?php
				printf(
					wp_kses_post( _x( 'You have successfully registered to <strong>%s</strong>. Your password has been emailed to %s.', 'First: The WordPress Site Name. Second: Registered User Email Address.', 'custom-wp-login' ) ),
					esc_attr( get_bloginfo( 'name' ) ),
					sanitize_email( $_GET['registered'] )
				);
			?>
		</p>
	<?php endif; ?>

	<?php if ( $attributes['lost_password_sent'] ) : ?>
		<p class="login-info yikes-custom-login-alert yikes-custom-login-alert-success yikes-animated <?php echo esc_attr( $this->options['notice_animation'] ); ?>">
			<?php esc_attr_e( 'Check your email for a link to reset your password.', 'custom-wp-login' ); ?>
		</p>
	<?php endif; ?>

	<?php if ( $attributes['password_updated'] ) : ?>
		<p class="login-info yikes-custom-login-alert yikes-custom-login-alert-success yikes-animated <?php echo esc_attr( $this->options['notice_animation'] ); ?>">
			<?php esc_attr_e( 'Your password has been changed. You can sign in now.', 'custom-wp-login' ); ?>
		</p>
	<?php endif; ?>

	<?php
		/* Render the login form */
		wp_login_form(
			array(
				'label_username' => __( 'Email or Username', 'custom-wp-login' ),
				'label_log_in' => __( 'Sign In', 'custom-wp-login' ),
				'form_id' => 'yikes-custom-login-form',
				'value_username' => null,
				'redirect' => $attributes['redirect'],
			)
		);
	?>

	<a class="forgot-password pull-left" href="<?php echo esc_url( wp_lostpassword_url() ); ?>">
		<?php apply_filters( 'yikes-custom-login-forgot-password-link-text', esc_attr_e( 'Forgot your password?', 'custom-wp-login' ) ); ?>
	</a>

	<?php
	/**
	 * Only display our 'Register' button when registeration is open to all users
	 * @since 1.0
	 */
	if ( get_option( 'users_can_register' ) ) { ?>
		<a class="register-account pull-right" href="<?php echo esc_url( get_the_permalink( $this->options['register_page'] ) ); ?>">
			<?php apply_filters( 'yikes-custom-login-register-link-text', esc_attr_e( 'Register', 'custom-wp-login' ) ); ?>
		</a>
	<?php } ?>

	<?php
		/** Custom Action Hook - After Login Form */
		do_action( 'yikes-custom-login-after-login-form' );
	?>

</div>

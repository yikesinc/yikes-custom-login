<?php
/**
 * Custom Login Form Template
 */
?>
<div class="login-form-container section group">

	<?php
		/** Custom Action Hook - Before Login Form */
		do_action( 'yikes-inc-custom-login-before-login-form' );
	?>

	<?php if ( $attributes['show_title'] ) : ?>
		<h2><?php esc_attr_e( 'Sign In', 'yikes-inc-custom-login' ); ?></h2>
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
			<?php esc_attr_e( 'You have logged out.', 'yikes-inc-custom-login' ); ?>
		</p>
	<?php endif; ?>

	<?php if ( $attributes['registered'] ) : ?>
		<p class="login-info yikes-custom-login-alert yikes-custom-login-alert-success yikes-animated <?php echo esc_attr( $this->options['notice_animation'] ); ?>">
			<?php
				printf(
					esc_attr__( 'You have successfully registered to <strong>%s</strong>. We have emailed your password to the email address you entered.', 'yikes-inc-custom-login' ),
					esc_attr( get_bloginfo( 'name' ) )
				);
			?>
		</p>
	<?php endif; ?>

	<?php if ( $attributes['lost_password_sent'] ) : ?>
		<p class="login-info yikes-custom-login-alert yikes-custom-login-alert-success yikes-animated <?php echo esc_attr( $this->options['notice_animation'] ); ?>">
			<?php esc_attr_e( 'Check your email for a link to reset your password.', 'yikes-inc-custom-login' ); ?>
		</p>
	<?php endif; ?>

	<?php if ( $attributes['password_updated'] ) : ?>
		<p class="login-info yikes-custom-login-alert yikes-custom-login-alert-success yikes-animated <?php echo esc_attr( $this->options['notice_animation'] ); ?>">
			<?php esc_attr_e( 'Your password has been changed. You can sign in now.', 'yikes-inc-custom-login' ); ?>
		</p>
	<?php endif; ?>

	<?php
		/* Render the login form */
		wp_login_form(
			array(
				'label_username' => __( 'Email or Username', 'yikes-inc-custom-login' ),
				'label_log_in' => __( 'Sign In', 'yikes-inc-custom-login' ),
				'form_id' => 'yikes-custom-login-form',
				'value_username' => null,
				'redirect' => $attributes['redirect'],
			)
		);
	?>

	<a class="forgot-password pull-left" href="<?php echo esc_url( wp_lostpassword_url() ); ?>">
		<?php esc_attr_e( 'Forgot your password?', 'yikes-inc-custom-login' ); ?>
	</a>

	<a class="register-account pull-right" href="<?php echo esc_url( get_the_permalink( $this->options['register_page'] ) ); ?>">
		<?php esc_attr_e( 'Signup', 'yikes-inc-custom-login' ); ?>
	</a>

	<?php
		/** Custom Action Hook - After Login Form */
		do_action( 'yikes-inc-custom-login-after-login-form' );
	?>

</div>

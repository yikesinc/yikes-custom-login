<div id="password-lost-form" class="widecolumn">
	<?php
		/** Custom Action Hook - Before Password Lost Form */
		do_action( 'yikes-custom-login-before-password-lost-form' );
	?>

	<?php if ( $attributes['show_title'] ) : ?>
		<h3><?php esc_attr_e( 'Reset Your Password', 'yikes-inc-custom-login' ); ?></h3>
	<?php endif; ?>

	<?php if ( count( $attributes['errors'] ) > 0 ) : ?>
		<?php foreach ( $attributes['errors'] as $error ) : ?>
			<p class="login-error yikes-custom-login-alert yikes-custom-login-alert-danger yikes-animated <?php echo esc_attr( $this->options['notice_animation'] ); ?>">
				<?php echo wp_kses_post( $error ); ?>
			</p>
		<?php endforeach; ?>
	<?php endif; ?>

	<p>
		<?php
			esc_attr_e(
				'Enter your email address and click Reset Password to send yourself a link to choose a new password.',
				'yikes-inc-custom-login'
			);
		?>
	</p>

	<form id="yikes-lost-password-form" action="<?php echo esc_url( wp_lostpassword_url() ); ?>" method="post">
		<p class="form-row">
			<label for="user_login"><?php esc_attr_e( 'Email', 'yikes-inc-custom-login' ); ?>
			<input type="text" name="user_login" id="user_login">
		</p>

		<p class="lostpassword-submit">
			<input type="submit" name="lostpassword-submit" class="lostpassword-button" value="<?php esc_attr_e( 'Reset Password', 'yikes-inc-custom-login' ); ?>"/>
		</p>
	</form>

	<?php
		/** Custom Action Hook - After Password Lost Form */
		do_action( 'yikes-custom-login-after-password-lost-form' );
	?>

</div>

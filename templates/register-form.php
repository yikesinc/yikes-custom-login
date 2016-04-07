<div id="register-form" class="widecolumn">

	<?php
		/** Custom Action Hook - Before Register Form */
		do_action( 'yikes-inc-custom-login-before-register-form' );
	?>

	<?php if ( $attributes['show_title'] ) : ?>
		<h3><?php esc_attr_e( 'Register', 'yikes-inc-custom-login' ); ?></h3>
	<?php endif; ?>

	<?php if ( count( $attributes['errors'] ) > 0 ) : ?>
		<?php foreach ( $attributes['errors'] as $error ) : ?>
			<p class="login-error yikes-custom-login-alert yikes-custom-login-alert-danger yikes-animated <?php echo esc_attr( $this->options['notice_animation'] ); ?>">
				<?php echo wp_kses_post( $error ); ?>
			</p>
		<?php endforeach; ?>
	<?php endif; ?>

	<form id="yikes-register-form" action="<?php echo esc_url( wp_registration_url() ); ?>" method="post">
		<!-- First Group -->
		<div class="section group">
			<p class="form-row col span_1_of_2">
				<label for="email"><?php esc_attr_e( 'Email', 'yikes-inc-custom-login' ); ?> <strong>*</strong></label>
				<input type="text" name="email" id="email">
			</p>

			<p class="form-row col span_1_of_2">
				<label for="first_name"><?php esc_attr_e( 'First name', 'yikes-inc-custom-login' ); ?></label>
				<input type="text" name="first_name" id="first-name">
			</p>
		</div>

		<div class="section group">
			<p class="form-row col span_1_of_2">
				<label for="last_name"><?php esc_attr_e( 'Last name', 'yikes-inc-custom-login' ); ?></label>
				<input type="text" name="last_name" id="last-name">
			</p>
		</div>

		<!-- Password generation note -->
		<p class="form-row span_2_of_2 yikes-register-note">
			<em>
				<?php
					printf(
						esc_attr__( '%s: Your password will be generated automatically and emailed to the address you specify above.', 'yikes-inc-custom-login' ),
						'<strong>Note</strong>'
					); ?>
			</em>
		</p>

		<?php if ( $attributes['recaptcha_site_key'] ) : ?>
			<div class="recaptcha-container">
				<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $attributes['recaptcha_site_key'] ); ?>"></div>
			</div>
		<?php endif; ?>

		<p></p>

		<p class="signup-submit span_2_of_2">
			<input type="submit" name="submit" class="register-button" value="<?php esc_attr_e( 'Register', 'yikes-inc-custom-login' ); ?>"/>
		</p>
	</form>

	<?php
		/** Custom Action Hook - After Register Form */
		do_action( 'yikes-inc-custom-login-after-register-form' );
	?>

</div>

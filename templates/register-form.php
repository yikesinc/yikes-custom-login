<?php
/**
 * Custom Registration Form
 * @since 1.0
 */

// If accessed directly, abort
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the form fields class
include_once( YIKES_CUSTOM_LOGIN_PATH . 'lib/classes/form-fields.php' );
$yikes_form_fields = new YIKES_Form_Fields( '', $this->options );
?>
<div id="register-form" class="yikes-register-form">

	<?php
		/** Custom Action Hook - Before Register Form */
		do_action( 'yikes-custom-login-before-register-form' );
	?>

	<?php if ( $attributes['show_title'] ) : ?>
		<h3><?php esc_attr_e( 'Register', 'custom-wp-login' ); ?></h3>
	<?php endif; ?>

	<?php if ( count( $attributes['errors'] ) > 0 ) : ?>
		<?php foreach ( $attributes['errors'] as $error ) : ?>
			<p class="login-error yikes-custom-login-alert yikes-custom-login-alert-danger yikes-animated <?php echo esc_attr( $this->options['notice_animation'] ); ?>">
				<?php echo wp_kses_post( $error ); ?>
			</p>
		<?php endforeach; ?>
	<?php endif; ?>

	<form id="yikes-register-form" action="<?php echo esc_url( wp_registration_url() ); ?>" method="post">

		<?php
		// Store the available fields
		$registration_fields = $yikes_form_fields->yikes_custom_login_registration_fields_array();
		// Setup integer value to setup columns
		$field_count = 1;
		$total_count = 1;
		// Get the length
		$field_length = count( $registration_fields );

		// Create nonce security checks
		wp_nonce_field( 'yikes_custom_login_register', 'yikes_custom_login_register' );

		// Loop over the available fields
		foreach ( $registration_fields as $field_data ) {
			// Add our row
			if ( 1 === $field_count ) {
				?><div class="section group"><?php
			}
			?>
			<p class="form-field col span_1_of_2">
				<label for="<?php echo esc_attr( $field_data['id'] ); ?>">
					<?php echo esc_attr( $field_data['label'] ); ?>
				</label>
				<?php
					// Render our field based on the field type
					$yikes_form_fields->render_form_field( $field_data, false );
				?>
			</p>
			<?php
			// Close our row
			if ( 2 === $field_count || $total_count === $field_length ) {
				?></div><?php
				// reset the count
				$field_count = 0;
			}
			// increment the field and total count
			$field_count++;
			$total_count++;
		}
		?>
		<!-- Password generation note -->
		<p class="form-row span_2_of_2 yikes-register-note">
			<em>
				<?php
					printf(
						esc_attr_x( '%s: Your password will be generated automatically and emailed to the address you entered above.', 'The word "Note" wrapped in <strong> tags.', 'custom-wp-login' ),
						'<strong>' . esc_attr__( 'Note', 'custom-wp-login' ) . '</strong>'
					); ?>
			</em>
		</p>

		<?php if ( $this->options['recaptcha_site_key'] && $this->options['recaptcha_secret_key'] ) { ?>
			<div class="recaptcha-container">
				<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $this->options['recaptcha_site_key'] ); ?>"></div>
			</div>
		<?php } ?>

		<p></p>

		<p class="signup-submit span_2_of_2">
			<input type="submit" name="submit-new-user" class="register-button" value="<?php esc_attr_e( 'Register', 'custom-wp-login' ); ?>"/>
		</p>
	</form>

		<?php
		/** Custom Action Hook - After Register Form */
		do_action( 'yikes-custom-login-after-register-form' );
		?>

</div>

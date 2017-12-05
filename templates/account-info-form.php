<?php
/**
 *  Setup our current user global
 *  @since 1.0
 */

// If accessed directly, abort
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
// Include the form fields class
include_once( YIKES_CUSTOM_LOGIN_PATH . 'lib/classes/form-fields.php' );
$yikes_form_fields = new YIKES_Form_Fields( $current_user->ID, $this->options );
?>

<!-- Profile Edit Template -->
<div id="post-<?php the_ID(); ?>">
	<div class="entry-content entry">

		<!-- If the user is not logged in, abort and display an error. -->
		<?php
		if ( ! is_user_logged_in() ) {

			printf(
				'<p class="warning">%s</p>',
				esc_attr__( 'You must be logged in to edit your profile.', 'custom-wp-login' )
			);

			// Generate the register/login buttons
			generate_yikes_register_button();
			generate_yikes_login_button();

		} else { // If the user is logged in.
			// display the errors if present
			$this->yikes_custom_login_display_alerts( $attributes['errors'] );

			/**
			*	'yikes-custom-login-password-reset-above-form'
			*
			*	Control whether the password reset button is above the form or below the form.
			*
			*	Return true to place the button above the form. 
			*	Return false to place the button below the form.
			*/
			$password_reset_above_form = apply_filters( 'yikes-custom-login-password-reset-above-form', false );
			
			/**
			*	'yikes-custom-login-password-reset-text'
			*
			*	Control the button text for the password reset button. Default is "New Password"
			*/
			$default_password_reset_text = __( 'New Password', 'yikes-inc-custom-login' );
			$password_reset_text = apply_filters( 'yikes-custom-login-password-reset-text', $default_password_reset_text );

			if ( $password_reset_above_form === true ) {
				?>
					<a href="#new-password" class="button reset-pass">
						<input type="submit" value="<?php echo esc_attr( $password_reset_text ); ?>" onclick="window.location.hash = '#new-password';return false;" />
					</a>
				<?php
			}

			?>
			<!-- YIKES Inc. Custom Account Info Form -->
			<form id="yikes-account-info-form" method="post" class="section group" action="<?php the_permalink(); ?>">
					<?php
					// Store the available fields
					$available_meta_fields = $yikes_form_fields->yikes_custom_login_profile_fields_array( $current_user->ID );

					// Setup integer value to setup columns
					$field_count = 1;
					$total_count = 1;

					// Get the length
					$field_length = count( $available_meta_fields );

					// Loop over the available fields
					foreach ( $available_meta_fields as $field_data ) {
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
								$yikes_form_fields->render_form_field( $field_data, $current_user->ID );
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

					<br />
					<!-- Submit button and nonces -->
					<p class="form-submit span_2_of_2">
						<input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php esc_attr_e( 'Update Profile', 'custom-wp-login' ); ?>" />

						<?php if ( $password_reset_above_form === false ): ?>
							<a href="#new-password" class="button reset-pass">
								<input type="submit" value="<?php echo esc_attr( $password_reset_text ); ?>" onclick="window.location.hash = '#new-password';return false;" />
							</a>
						<?php endif; ?>

						<?php wp_nonce_field( 'update-user' ) ?>
						<input name="action" type="hidden" id="action" value="update-user" />
					</p><!-- .form-submit -->

			</form><!-- #adduser -->

			<!-- Testing Pure CSS Popups -->
			<?php 
				echo $this->get_template_html( 'account-password-reset-popup', null );
			?>


		<?php } /* End Else */ ?>

	</div><!-- .entry-content -->
</div><!-- .hentry .post -->

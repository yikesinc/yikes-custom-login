<?php
/**
 *  Setup our current user global
 *  @since 1.0
 */
global $current_user;
get_currentuserinfo();
?>

<!-- Profile Edit Template -->
<div id="post-<?php the_ID(); ?>">
	<div class="entry-content entry">

		<!-- If the user is not logged in, abort and display an error. -->
		<?php
		if ( ! is_user_logged_in() ) {

			printf(
				'<p class="warning">%s</p>',
				esc_attr__( 'You must be logged in to edit your profile.', 'yikes-inc-custom-login' )
			);

		} else { // If the user is logged in.
			// display the errors if present
			$this->yikes_custom_login_display_alerts( $error );
			?>
			<!-- YIKES Inc. Custom Account Info Form -->
			<form id="yikes-account-info-form" method="post" class="section group" action="<?php the_permalink(); ?>">
					<?php
					// Store the available fields
					$available_meta_fields = $this->yikes_custom_login_profile_fields( $current_user->ID );
					// Setup integer value to setup columns
					$field_count = 1;
					$total_count = 1;

					// Get the length
					$field_length = count( $available_meta_fields );

					// Loop over the available fields
					foreach ( $available_meta_fields as $field_key => $field_data ) {
						// Add our row
						if ( 1 === $field_count ) {
							?><div class="section group"><?php
						}
						?>
						<p class="form-field col span_1_of_2">
							<label for="<?php echo esc_attr( $field_key ); ?>">
								<?php echo esc_attr( $field_data['label'] ); ?>
							</label>
							<?php
							// instantiate our profile fields class
							$yikes_profile_fields = new YIKES_Profile_Fields( $current_user->ID, $this->options );
							// Render our field based on the field type
							$yikes_profile_fields->render_profile_field( $field_key, $current_user->ID, $field_data['type'], str_replace( 'user-', '', $field_key ) );
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
						<?php echo esc_attr( $referer ); ?>
						<input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php esc_attr_e( 'Update Profile', 'yikes-inc-custom-login' ); ?>" />
						<a href="#new-password-popup" class="button reset-pass"><?php esc_attr_e( 'New Password', 'yikes-inc-custom-login' ); ?></a>
						<?php wp_nonce_field( 'update-user' ) ?>
						<input name="action" type="hidden" id="action" value="update-user" />
					</p><!-- .form-submit -->

			</form><!-- #adduser -->

			<!-- Testing Pure CSS Popups -->
			<?php echo $this->get_template_html( 'account-password-reset-popup', null ); ?>


		<?php } /* End Else */ ?>

	</div><!-- .entry-content -->
</div><!-- .hentry .post -->
<?php
/**
 *  Setup our current user global
 *  @since 1.0
 */
global $current_user;
get_currentuserinfo();
$available_meta_fields = $this->yikes_custom_login_get_profile_fields( $current_user->ID );
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

			/* If errors are found, display them */
			if ( count( $error ) > 0 ) {
				echo '<p class="error">' . esc_html( implode( '<br />', $error ) ) . '</p>';
			}
			?>
			<!-- YIKES Inc. Custom Account Info Form -->
			<form id="yikes-account-info-form" method="post" class="section group" action="<?php the_permalink(); ?>">
					<?php
					// setup integer value to setup columns
					$field_count = 1;
					foreach ( $available_meta_fields as $field_key => $field_data ) {
						// Add our row
						if ( 1 === $field_count ) {
							?><div class="section group"><?php
						}
						?>
						<p class="form-field col span_1_of_2">
							<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo esc_attr( $field_data['label'] ); ?></label>
							<?php
							// Setup the field key (name/id attributes on the form field)
							$field_name = str_replace( 'user-', '', $field_key );
							switch ( $field_data['type'] ) {
								default:
								case 'text':
								case 'email':
								case 'url':
									printf(
										'<input type="%s" class="text-input" name="%s" id="%s" value="%s" />',
										esc_attr( $field_data['type'] ),
										esc_attr( $field_name ),
										esc_attr( $field_name ),
										esc_textarea( get_the_author_meta( $field_key, $current_user->ID ) )
									);
									break;
								case 'textarea':
									printf(
										'<textarea name="%s" id="%s" rows="3" cols="50">%s</textarea>',
										esc_attr( $field_name ),
										esc_attr( $field_name ),
										esc_textarea( get_the_author_meta( $field_key, $current_user->ID ) )
									);
									break;
							}
							?>
						</p>
						<?php
						// Close our row
						if ( 2 === $field_count ) {
							?></div><?php
							// reset the count
							$field_count = 0;
						}
						// increment the field count
						$field_count++;
					}
					?>

					<br />
					<!-- Submit button and nonces -->
					<p class="form-submit span_2_of_2">
						<?php echo esc_attr( $referer ); ?>
						<input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php esc_attr_e( 'Update Profile', 'yikes-inc-custom-login' ); ?>" />
						<?php wp_nonce_field( 'update-user' ) ?>
						<input name="action" type="hidden" id="action" value="update-user" />
					</p><!-- .form-submit -->

					<hr />

					<h2>Static Fields (to delete)</h2>
			    <p class="form-username">
			        <label for="first-name"><?php _e('First Name', 'yikes-inc-custom-login'); ?></label>
			        <input class="text-input" name="first-name" type="text" id="first-name" value="<?php the_author_meta( 'first_name', $current_user->ID ); ?>" />
			    </p><!-- .form-username -->
			    <p class="form-username">
			        <label for="last-name"><?php _e('Last Name', 'yikes-inc-custom-login'); ?></label>
			        <input class="text-input" name="last-name" type="text" id="last-name" value="<?php the_author_meta( 'last_name', $current_user->ID ); ?>" />
			    </p><!-- .form-username -->
			    <p class="form-email">
			        <label for="email"><?php _e('E-mail *', 'yikes-inc-custom-login'); ?></label>
			        <input class="text-input" name="email" type="text" id="email" value="<?php the_author_meta( 'user_email', $current_user->ID ); ?>" />
			    </p><!-- .form-email -->
			    <p class="form-url">
			        <label for="url"><?php _e('Website', 'yikes-inc-custom-login'); ?></label>
			        <input class="text-input" name="url" type="text" id="url" value="<?php the_author_meta( 'user_url', $current_user->ID ); ?>" />
			    </p><!-- .form-url -->
			    <p class="form-password">
			        <label for="pass1"><?php _e('Password *', 'yikes-inc-custom-login'); ?> </label>
			        <input class="text-input" name="pass1" type="password" id="pass1" />
			    </p><!-- .form-password -->
			    <p class="form-password">
			        <label for="pass2"><?php _e('Repeat Password *', 'yikes-inc-custom-login'); ?></label>
			        <input class="text-input" name="pass2" type="password" id="pass2" />
			    </p><!-- .form-password -->
			    <p class="form-textarea">
			        <label for="description"><?php _e('Biographical Information', 'yikes-inc-custom-login') ?></label>
			        <textarea name="description" id="description" rows="3" cols="50"><?php the_author_meta( 'description', $current_user->ID ); ?></textarea>
			    </p><!-- .form-textarea -->

			</form><!-- #adduser -->

		<?php } /* End Else */ ?>

	</div><!-- .entry-content -->
</div><!-- .hentry .post -->

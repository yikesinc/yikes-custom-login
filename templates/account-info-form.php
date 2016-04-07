<?php
/**
 *  Setup our current user global
 *  @since 1.0
 */
global $current_user;
get_currentuserinfo()
?>

<div class="section group">
	<div class="col span_1_of_2">
	This is column 1
	</div>
	<div class="col span_1_of_2">
	This is column 2
	</div>
</div>

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

			<form method="post" class="section group" id="adduser" action="<?php the_permalink(); ?>">
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

			    <?php
			      // Custom hook for additional form fieilds
			      do_action( 'edit_user_profile', $current_user );
			    ?>
			    <p class="form-submit">
			        <?php echo $referer; ?>
			        <input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('Update', 'yikes-inc-custom-login'); ?>" />
			        <?php wp_nonce_field( 'update-user' ) ?>
			        <input name="action" type="hidden" id="action" value="update-user" />
			    </p><!-- .form-submit -->
			</form><!-- #adduser -->

		<?php } /* End Else */ ?>

	</div><!-- .entry-content -->
</div><!-- .hentry .post -->

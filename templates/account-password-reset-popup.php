<?php
/**
 * Update current password on the account info page
 * The form displays in a popup, and will not update until there is a strong password entered.
 * @since 1.0
 */

// If accessed directly, abort
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Store user details, for use in the form.
$user_details = wp_get_current_user();
?>
<div id="new-password" class="overlay">
	<div class="yikes-custom-login-popup">
		<h2><?php esc_attr_e( 'New Password', 'custom-wp-login' ); ?></h2>
		<p><?php esc_attr_e( 'Enter your new password in the fields below. You must enter a strong password before you can update it.', 'custom-wp-login' ); ?></p>
		<a class="close" href="#">&times;</a>
		<div class="content">
			<!-- Reset Form -->
			<form name="resetpasswordform" action="<?php echo esc_url( site_url( 'wp-login.php?action=resetpass', 'login_post' ) ); ?>" method="post">

				<!-- Hidden Fields -->
				<input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( $user_details->user_login ); ?>" />
				<input type="hidden" id="user_id" name="user_id" value="<?php echo esc_attr( $user_details->ID ); ?>" />
				<input type="hidden" name="user_manual_reset" value="true" />

				<p class="form-password">
					<label for="pass1"><?php esc_attr_e( 'New Password', 'custom-wp-login' ); ?></label>
					<input class="text-input" name="pass1" type="password" id="pass1">
				</p>
				<p class="form-password">
					<label for="pass2"><?php esc_attr_e( 'Confirm Password', 'custom-wp-login' ); ?></label>
					<input class="text-input" name="pass2" type="password" id="pass2">
				</p>
				<!-- Display the password strength meter, if enabled -->
				<?php
				if ( 1 === $this->options['password_strength_meter'] ) {
					// enqueue our strength meter scripts
					wp_enqueue_script( 'password-strength-meter' );
					wp_enqueue_script( 'yikes-password-strength-meter', plugin_dir_url( __FILE__ ) . '../lib/js/yikes-password-strength-meter.js', array( 'password-strength-meter' ) );
					printf( '<span id="pass-strength-result" aria-live="polite"></span>' );
				}
				?>
				<input type="hidden" name="redirect_to" value="<?php echo esc_url( get_the_permalink( $this->options['login_page'] ) ) ?>?action=resetpass&amp;success=1">
				<p class="submit">
					<input type="submit" disabled name="wp-submit" id="wp-submit" value="<?php esc_attr_e( 'Set New Password', 'custom-wp-login' ); ?>" />
				</p>
			</form>
			<p class="description">
				<em>
					<?php esc_attr_e( 'Note: After you change your password, you will need to login again.', 'custom-wp-login' ); ?>
				</em>
			</p>
		</div>
	</div>
</div>

<?php
/**
 * Display logged in user details
 * Mainly used outside of the 'login' page set in the options
 * @since 1.0
 */
$current_user = wp_get_current_user();
?>
<div id="yikes-logged-in-user-details">
	<strong><?php echo esc_attr( $current_user->user_login ); ?></strong>
	<span class="links">
		<a href="<?php echo esc_url( get_the_permalink( $this->options['account_info_page'] ) ); ?>">
			<?php esc_attr_e( 'Account', 'yikes-inc-custom-login' ); ?>
		</a> |
		<a href="<?php echo esc_url( wp_logout_url( $this->options['login_page'] ) ); ?>">
			<?php esc_attr_e( 'Log Out', 'yikes-inc-custom-login' ); ?>
		</a>
	</span>
</div>

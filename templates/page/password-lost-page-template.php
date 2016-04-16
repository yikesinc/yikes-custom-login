<?php
/**
 * Password Lost Page Template
 * @since 1.0
 */
wp_head();
?>

<div id="yikes-custom-password-lost-template">
	<?php do_action( 'yikes-custom-login-password-lost-page-top' ); ?>
	<div class="interior yikes-animated yikes-fadeIn">
		<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" title="preloader" class="login-preloader" />
		<?php
			/* Display the login form and necessary actions */
			do_action( 'yikes-custom-login-password-lost-page-before-form' );
			echo do_shortcode( '[custom-password-lost-form]' );
			do_action( 'yikes-custom-login-password-lost-page-after-form' );
		?>
	</div>
	<?php do_action( 'yikes-custom-login-password-lost-page-bottom' ); ?>
</div>

<?php wp_footer(); ?>

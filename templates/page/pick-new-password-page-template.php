<?php
/**
 * Custom Pick New Password Template
 * @since 1.0
 */
wp_head();
?>

<body class="yikes-custom-page-template">
	<div id="yikes-custom-user-registration-template" class="yikes-custom-page-template-interior">

		<?php do_action( 'yikes-custom-login-pick-new-password-page-top' ); ?>

		<div class="page-container">

			<?php do_action( 'yikes-custom-login-branding' ); ?>

			<div class="interior yikes-animated yikes-fadeIn">
				<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" title="preloader" class="login-preloader" />
				<?php
					/* Display the 'Pick New Password' form and necessary actions */
					do_action( 'yikes-custom-login-pick-new-password-page-before-form' );
					echo do_shortcode( '[custom-password-reset-form]' );
					do_action( 'yikes-custom-login-pick-new-password-page-after-form' );
				?>
			</div>

		</div>

		<?php do_action( 'yikes-custom-login-pick-new-password-page-bottom' ); ?>

	</div>
</body>

<?php wp_footer(); ?>

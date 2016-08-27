<?php
/**
 * Custom Login Page - Full Width Page Template
 * @since 1.0
 */

// If accessed directly, abort
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_head();
?>

<body class="yikes-custom-page-template">
	<div id="yikes-custom-user-registration-template" class="yikes-custom-page-template-interior">

		<?php do_action( 'yikes-custom-login-login-page-top' ); ?>

		<div class="page-container">

			<?php do_action( 'yikes-custom-login-branding' ); ?>

			<div class="interior yikes-animated yikes-fadeIn">
				<!-- Login Form -->
				<?php
					do_action( 'yikes-custom-login-login-page-before-form' );
					echo do_shortcode( '[custom-login-form]' );
					do_action( 'yikes-custom-login-login-page-after-form' );
				?>

				<!-- Preloader -->
				<div class="preloader-container">
					<img src="<?php echo apply_filters( 'yikes-custom-login-preloader', esc_url( admin_url( 'images/wpspin_light.gif' ) ) ); ?>" title="preloader" class="login-preloader" />
				</div>
			</div>

		</div>

		<?php do_action( 'yikes-custom-login-login-page-bottom' ); ?>

	</div>
</body>

<?php wp_footer(); ?>

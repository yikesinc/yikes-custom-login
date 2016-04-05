<div id="register-form" class="widecolumn">
	<?php if ( $attributes['show_title'] ) : ?>
		<h3><?php _e( 'Register', 'yikes-inc-custom-login' ); ?></h3>
	<?php endif; ?>

	<?php if ( count( $attributes['errors'] ) > 0 ) : ?>
		<?php foreach ( $attributes['errors'] as $error ) : ?>
			<p>
				<?php echo $error; ?>
			</p>
		<?php endforeach; ?>
	<?php endif; ?>

	<form id="signupform" action="<?php echo wp_registration_url(); ?>" method="post">
		<p class="form-row">
			<label for="email"><?php _e( 'Email', 'yikes-inc-custom-login' ); ?> <strong>*</strong></label>
			<input type="text" name="email" id="email">
		</p>

		<p class="form-row">
			<label for="first_name"><?php _e( 'First name', 'yikes-inc-custom-login' ); ?></label>
			<input type="text" name="first_name" id="first-name">
		</p>

		<p class="form-row">
			<label for="last_name"><?php _e( 'Last name', 'yikes-inc-custom-login' ); ?></label>
			<input type="text" name="last_name" id="last-name">
		</p>

		<p class="form-row">
			<?php _e( 'Note: Your password will be generated automatically and emailed to the address you specify above.', 'yikes-inc-custom-login' ); ?>
		</p>

		<?php if ( $attributes['recaptcha_site_key'] ) : ?>
			<div class="recaptcha-container">
				<div class="g-recaptcha" data-sitekey="<?php echo $attributes['recaptcha_site_key']; ?>"></div>
			</div>
		<?php endif; ?>

		<p></p>

		<p class="signup-submit">
			<input type="submit" name="submit" class="register-button" value="<?php _e( 'Register', 'yikes-inc-custom-login' ); ?>"/>
		</p>
	</form>
</div>

/**
 * Login Page Scripts
 * @since 1.0
 */
jQuery( document ).ready( function() {
	/* Form Submissions */
	jQuery( 'body' ).on( 'submit', '#yikes-custom-login-form, #yikes-register-form, #yikes-lost-password-form, #yikes-reset-password-form', function( e ) {
		delay_and_submit_form( e, this );
	});
});

/**
 * Delay, show our preloader and then submit
 * @param  {[type]} e              [description]
 * @param  {[type]} submitted_form [description]
 * @return {[type]}                [description]
 */
function delay_and_submit_form( e, submitted_form ) {
	var clicked_form = submitted_form;
	e.preventDefault();
	jQuery( '.preloader-container' ).show();
	jQuery( '.login-form-container, #password-lost-form, #register-form, #password-reset-form' ).fadeTo( 'fast', 0.5 );
	jQuery( '#register-form' ).next().fadeTo( 'fast', 0.5 );
	setTimeout(function () {
		clicked_form.submit();
	}, 1000); // in milliseconds
}

/**
 * Login Page Scripts
 * @since 1.0
 */
jQuery( document ).ready( function() {
	jQuery( 'body' ).on( 'submit', '#yikes-custom-login-form', function( e ) {
		delay_and_submit_form( e, this );
	});
});

function delay_and_submit_form( e, submitted_form ) {
	var clicked_form = submitted_form;
	e.preventDefault();
	jQuery( '.login-preloader' ).show();
	jQuery( '.login-form-container' ).fadeTo( 'fast', 0.5 );
	setTimeout(function () {
			clicked_form.submit();
	}, 1000); // in milliseconds
}

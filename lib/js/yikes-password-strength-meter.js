function checkPasswordStrength( $pass1, $pass2, $strengthResult, $submitButton, blacklistArray, minimum_password_strength ) {
	var pass1 = $pass1.val();
	var pass2 = $pass2.val();

	// Reset the form & meter
	$submitButton.attr( 'disabled', 'disabled' );
	$strengthResult.removeClass( 'short bad good strong' );

	// Extend our blacklist array with those from the inputs & site data
	blacklistArray = blacklistArray.concat( wp.passwordStrength.userInputBlacklist() );

	// Get the password strength
	var strength = wp.passwordStrength.meter( pass1, blacklistArray, pass2 );


	// Add the strength meter results
	switch ( strength ) {

	case 2:
		$strengthResult.addClass( 'bad' ).html( pwsL10n.bad );
		break;

	case 3:
		$strengthResult.addClass( 'good' ).html( pwsL10n.good );
		break;

	case 4:
		$strengthResult.addClass( 'strong' ).html( pwsL10n.strong );
		break;

	case 5:
		$strengthResult.addClass( 'short' ).html( pwsL10n.mismatch );
		break;

	default:
		$strengthResult.addClass( 'short' ).html( pwsL10n.short );
		break;
	}

	// The meter function returns a result even if pass2 is empty,
	// enable only the submit button if the password matches the strength level passed in and
	// both passwords are filled up
	if ( ( strength === 2 || strength === 3 || strength === 4 ) && strength >= minimum_password_strength && '' !== pass2.trim() ) {
		$submitButton.removeAttr( 'disabled' );
	}
	return strength;
}

function get_minimum_allowed_password_strength() {
	switch( password_strength_meter.strength ) {
		case 'weak': 
			return 2;
		break;
		case 'medium':
			return 3;
		default:
		case 'strong':
			return 4;
		break;
	}
}

/**
 * Initialize the password strength meter on key press
 * @since 1.0
 */
jQuery( document ).ready( function() {

	var minimum_password_strength = get_minimum_allowed_password_strength();

	// Binding to trigger checkPasswordStrength
	jQuery( 'body' ).on( 'keyup', '#pass1, #pass2',
		function( event ) {
			checkPasswordStrength(
				jQuery('#pass1'),                              // First password field
				jQuery('#pass2'),                              // Second password field
				jQuery('#pass-strength-result'),               // Strength meter
				jQuery('#new-password').find( '#wp-submit' ),  // Submit button
				[],                                            // Blacklisted words
				minimum_password_strength					   // Minimum Password Strength Enforced
			);
		}
	);
});

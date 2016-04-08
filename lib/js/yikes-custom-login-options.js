/**
 * YIKES Inc. Custom Login Options scripts
 * @since 1.0.0
 */
jQuery( document ).ready( function() {
	/* Initialize our select2 fields */
	yikes_init_select2_fields();
});

/**
 * Initialize all found select2 fields on the page
 * @since 1.0.0
 */
function yikes_init_select2_fields() {
	jQuery( '.yikes-select2' ).each( function() {
		jQuery( this ).select2();
	});
}

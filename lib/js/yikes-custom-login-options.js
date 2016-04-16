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

(function( $ ) {
	'use strict';

	/* global wp, console */

	var file_frame, image_data;

	$(function() {
		console.log( file_frame );
		/**
		 * If an instance of file_frame already exists, then we can open it
		 * rather than creating a new instance.
		 */
		if ( undefined !== file_frame ) {

			file_frame.open();
			return;

		}

		/**
		 * If we're this far, then an instance does not exist, so we need to
		 * create our own.
		 *
		 * Here, use the wp.media library to define the settings of the Media
		 * Uploader implementation by setting the title and the upload button
		 * text. We're also not allowing the user to select more than one image.
		 */
		file_frame = wp.media.frames.file_frame = wp.media({
			title:    "Insert Media",    // For production, this needs i18n.
			button:   {
				text: "Upload Image"     // For production, this needs i18n.
			},
			multiple: false
		});

		/**
		 * Setup an event handler for what to do when an image has been
		 * selected.
		 */
		file_frame.on( 'select', function() {

			image_data = file_frame.state().get( 'selection' ).first().toJSON();
			for ( var image_property in image_data ) {

				/**
				 * Here, you have access to all of the properties
				 * provided by WordPress to the selected image.
				 *
				 * This is generally where you take the data and so whatever
				 * it is that you want to do.
				 *
				 * For purposes of example, we're just going to dump the
				 * properties into the console.
				 */
				console.log( image_property + ': ' + image_data[ image_property ] );
			}
			jQuery( '#branding_logo' ).val( image_data.url );
			jQuery( '#branding_logo_id' ).val( image_data.id );
			jQuery( '.branding_logo_preview' ).addClass( 'preview-active' ).find( 'img' ).attr( 'src', image_data.url );
		});

		jQuery( 'body' ).on( 'click', '#branding_logo', function() {
			// Now display the actual file_frame
			file_frame.open();
		});

		jQuery( 'body' ).on( 'click', '.remove-branding-logo', function() {
			// remove the image and image ids;
			jQuery( '#branding_logo' ).val( '' );
			jQuery( '#branding_logo_id' ).val( '' );
			jQuery( '.branding_logo_preview' ).removeClass( 'preview-active' ).find( 'img' ).attr( 'src', '' );
		});

	});

})( jQuery );

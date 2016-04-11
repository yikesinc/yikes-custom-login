<?php
/**
 * Profile Fields Class
 * Account Info Form Fields
 * @since 1.0
 */
class YIKES_Profile_Fields {

	// Store private var
	private $options;

	/**
	 * Initializes the plugin.
	 *
	 * To keep the initialization fast, only add filter and action
	 * hooks in the constructor.
	 */
	public function __construct( $user_id, $options ) {
		// Store our options in the global
		$this->options = $options;
	}

	/**
	 * Get all of the available profile meta fieilds
	 * Used when building the 'Update Profile' form
	 * @return array The fields to be used when rendering the 'Update Profile' form.
	 */
	public function yikes_custom_login_get_profile_fields( $user_id ) {
		// If no user id is specified
		if ( ! $user_id ) {
			return false;
		}

		/* Fields that we should NOT display on the frontend for users to edit */
		$excluded_fields = apply_filters( 'yikes-custom-login-excluded-profile-fields', array(
			'rich_editing',
			'comment_shortcuts',
			'admin_color',
			'use_ssl',
			'show_admin_bar_front',
			'wp_capabilities',
			'wp_user_level',
			'dismissed_wp_pointers',
			'show_welcome_panel',
			'session_tokens',
			'wp_dashboard_quick_press_last_post_id',
			'wp_user-settings',
			'wp_user-settings-time',
		) );

		// Get ALL meta fields
		$user_meta_fields = get_user_meta( $user_id );

		// Loop and unset our fields
		foreach ( $excluded_fields as $meta_id ) {
			unset( $user_meta_fields[ $meta_id ] );
		}

		// Create an array to loop over for the_author_meta()
		$author_meta_array = apply_filters( 'yikes-custom-login-author-meta-fields', array(
			'user_email',
			'user_url',
		) );

		// Loop over and push the additional fields to our fields array
		foreach ( $author_meta_array as $meta_id ) {
			$user_meta_fields[ $meta_id ] = array( get_the_author_meta( $meta_id, $user_id ) );
		}

		// store an int value to increment and retreive our keys (for label parameter)
		$key_location = 0;
		$user_meta_field_keys = array_keys( $user_meta_fields );

		// Loop over the keys and push the field label into our array
		foreach ( $user_meta_field_keys as $key => $meta_id ) {
			$user_meta_fields[ $meta_id ][] = $meta_id;
		}
		// Finally loop over our final fields array, and push two new parameters - 'type' and 'label'
		$user_meta_fields = array_map( function( $user_meta_fields ) {
			return array(
				'label' => esc_attr( $this->yikes_custom_login_get_form_field_label( $user_meta_fields[1], $user_meta_fields[1] ) ),
				'type' => $this->yikes_custom_login_get_form_field_type( $user_meta_fields[1] ),
				'data' => $this->yikes_custom_login_escape_form_field_data( $user_meta_fields[1], $user_meta_fields[0] ),
			);
		}, $user_meta_fields );

		/* Re-arrange the 'Biography' textarea field to the end of the form */
		$biography_field = $user_meta_fields['description'];
		unset( $user_meta_fields['description'] );
		// append the description to the end of the array
		$user_meta_fields['description'] = $biography_field;

		// Return the newly formed array
		return apply_filters( 'yikes-custom-login-profile-fields', $user_meta_fields, $user_id );
	}

	/**
	 * Switch statement to return an appropriate form field label for a given key
	 * @param  string $field_key The name of the field (eg first_name)
	 * @return string            The newly formatted field label
	 * @since 1.0
	 */
	public function yikes_custom_login_get_form_field_label( $field_key, $field_key ) {
		// if no field key is set, abort
		if ( ! $field_key ) {
			return;
		}
		// Switch statement over the field key to dictate the label
		switch ( $field_key ) {
			case 'nickname':
				$label = __( 'Nickname', 'yikes-inc-custom-login' );
				break;
			case 'first_name':
				$label = __( 'First Name', 'yikes-inc-custom-login' );
				break;
			case 'last_name':
				$label = __( 'Last Name', 'yikes-inc-custom-login' );
				break;
			case 'description':
				$label = __( 'Biography', 'yikes-inc-custom-login' );
				break;
			case 'user_email':
				$label = __( 'Email Address', 'yikes-inc-custom-login' );
				break;
			case 'user_url':
				$label = __( 'Website', 'yikes-inc-custom-login' );
				break;
			default:
				$label = $field_key; /* Default is to use the field key, should use the filter below. */
				break;
		}
		return apply_filters( 'yikes-custom-login-' . $field_key . '-label', $label );
	}

	/**
	 * Set the appropriate field type based on the field key passed in
	 * @param  string $field_key The key/name of the field.
	 * @return string            The field type to be used.
	 */
	public function yikes_custom_login_get_form_field_type( $field_key ) {
		// if no field key is set, abort
		if ( ! $field_key ) {
			return;
		}
		// Switch statement over the field key to dictate the label
		switch ( $field_key ) {
			default:
			case 'nickname':
			case 'first_name':
			case 'last_name':
				$type = 'text';
				break;
			case 'description':
				$type = 'textarea';
				break;
			case 'user_email':
				$type = 'email';
				break;
			case 'user_url':
				$type = 'url';
				break;
		}
		return apply_filters( 'yikes-custom-login-' . $field_key . '-type', $type );
	}

	/**
	 * Escape and return the field data for use in the form
	 * @param  string $field_key  The field key, used to decide how to sanitize
	 * @param  string $field_data The field data that will be sanitized and returned
	 * @return string             The escaped form data that will be used
	 */
	public function yikes_custom_login_escape_form_field_data( $field_key, $field_data ) {
		// if no field key is set, abort
		if ( ! $field_key ) {
			return;
		}
		// Switch statement over the field key to dictate the label
		switch ( $field_key ) {
			default:
			case 'text':
			case 'textarea':
				return esc_textarea( $field_data );
				break;
			case 'url':
				return esc_url( $field_data );
				break;
			case 'email':
				return sanitize_email( $field_data );
				break;
		}
	}

	/**
	 * Render the appropriate profile field
	 * @param  [type] $field_data [description]
	 * @param  [type] $field_type [description]
	 * @return [type]             [description]
	 */
	public function render_profile_field( $field_key, $user_id, $field_type, $field_name ) {
		switch ( $field_type ) {
			default:
			case 'text':
			case 'email':
			case 'url':
				printf(
					'<input type="%s" class="text-input" name="%s" id="%s" value="%s" />',
					esc_attr( $field_type ),
					esc_attr( $field_name ),
					esc_attr( $field_name ),
					esc_textarea( get_the_author_meta( $field_key, $user_id ) )
				);
				break;
			case 'textarea':
				printf(
					'<textarea name="%s" id="%s" rows="3" cols="50">%s</textarea>',
					esc_attr( $field_name ),
					esc_attr( $field_name ),
					esc_textarea( get_the_author_meta( $field_key, $user_id ) )
				);
				break;
		}
	} // End
}

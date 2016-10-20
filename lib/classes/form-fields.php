<?php
/**
 * Profile Fields Class
 * Account Info Form Fields
 * @since 1.0
 */
class YIKES_Form_Fields {

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
	 * Get the profile fields that we want to use
	 * @param  integer $user_id The ID of the user whos meta you want to retreive.
	 * @return array            An array fields we want to display on the profile page.
	 */
	public function yikes_custom_login_profile_fields_array( $user_id ) {
		// if no user id was specifie - abort
		if ( ! $user_id ) {
			return;
		}
		// Default profile fields out of the box
		$default_profile_fields = array(
			array(
				'id' => 'nickname',
				'label' => __( 'Nickname', 'custom-wp-login' ),
				'type' => 'text',
			),
			array(
				'id' => 'first_name',
				'label' => __( 'First Name', 'custom-wp-login' ),
				'type' => 'text',
			),
			array(
				'id' => 'last_name',
				'label' => __( 'Last Name', 'custom-wp-login' ),
				'type' => 'text',
			),
			array(
				'id' => 'user_email',
				'label' => __( 'Email Address', 'custom-wp-login' ),
				'type' => 'text',
			),
			array(
				'id' => 'user_url',
				'label' => __( 'Website', 'custom-wp-login' ),
				'type' => 'text',
			),
			array(
				'id' => 'description',
				'label' => __( 'Biography', 'custom-wp-login' ),
				'type' => 'textarea',
			),
		);
		// return the default profile fields
		return apply_filters( 'yikes-login-profile-fields', $default_profile_fields, $user_id );
	}
	/**
	 * Get the registration fields that we want to use
	 * @param  integer $user_id The ID of the user whos meta you want to retreive.
	 * @return array            An array fields we want to display on the profile page.
	 */
	public function yikes_custom_login_registration_fields_array() {
		// Default profile fields out of the box
		$default_registration_fields = array(
			array(
				'id' => 'email',
				'label' => __( 'Email', 'custom-wp-login' ),
				'type' => 'email',
				'atts' => array(
					'required' => 'required',
				),
			),
			array(
				'id' => 'first_name',
				'label' => __( 'First Name', 'custom-wp-login' ),
				'type' => 'text',
			),
			array(
				'id' => 'last_name',
				'label' => __( 'Last Name', 'custom-wp-login' ),
				'type' => 'text',
			),
		);
		// return the default profile fields
		return apply_filters( 'yikes-login-registration-fields', $default_registration_fields, '' ); // empty 2nd parametr so we can hook into same location as above
	}
	/**
	 * Render a given form field
	 * @param  array   $field_data The array of form field data.
	 * @param  integer $user_id    The given users ID.
	 * @param  string  $field_type The type for the given form field.
	 * @param  string  $field_id   The ID of the form field.
	 * @return html               Markup for the given profile form field
	 */
	public function render_form_field( $field_data, $user_id ) {
		$attributes = ( isset( $field_data['atts'] ) ) ? $this->build_field_atts( $field_data['atts'] ) : array();
		switch ( $field_data['type'] ) {
			default:
			case 'text':
			case 'email':
			case 'url':
				printf(
					'<input type="%s" name="%s" id="%s" value="%s" %s />',
					esc_attr( $field_data['type'] ),
					esc_attr( $field_data['id'] ),
					esc_attr( $field_data['id'] ),
					esc_textarea( get_the_author_meta( $field_data['id'], $user_id ) ),
					wp_kses_post( implode( ' ', $attributes ) )
				);
				break;
			case 'textarea':
				printf(
					'<textarea name="%s" id="%s" rows="3" cols="50" %s>%s</textarea>',
					esc_attr( $field_data['id'] ),
					esc_attr( $field_data['id'] ),
					wp_kses_post( implode( ' ', $attributes ) ),
					esc_textarea( get_the_author_meta( $field_data['id'], $user_id ) )
				);
				break;
		}
	}

	/**
	 * Build an array that we can explode onto our fields
	 * @param  array $field_attributes  Field attributes passed in
	 * @return [type]                   A new array of attributes to use on the field.
	 */
	public function build_field_atts( $field_attributes ) {
		// empty array
		$attributes_array = array();
		// loop and build the array
		foreach ( $field_attributes as $attribute => $value ) {
			$attributes_array[] = $attribute. '="' . esc_attr( $value ) . '"';
		}
		// return our array
		return $attributes_array;
	}
}

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
				'label' => __( 'Nickname', 'yikes-inc-custom-login' ),
				'type' => 'text',
			),
			array(
				'id' => 'first_name',
				'label' => __( 'First Name', 'yikes-inc-custom-login' ),
				'type' => 'text',
			),
			array(
				'id' => 'last_name',
				'label' => __( 'Last Name', 'yikes-inc-custom-login' ),
				'type' => 'text',
			),
			array(
				'id' => 'user_email',
				'label' => __( 'Email Address', 'yikes-inc-custom-login' ),
				'type' => 'text',
			),
			array(
				'id' => 'user_url',
				'label' => __( 'Website', 'yikes-inc-custom-login' ),
				'type' => 'text',
			),
			array(
				'id' => 'description',
				'label' => __( 'Biography', 'yikes-inc-custom-login' ),
				'type' => 'textarea',
			),
		);
		// return the default profile fields
		return apply_filters( 'yikes-login-profile-fields', $default_profile_fields, $user_id );
	}
	/**
	 * Render a given form field
	 * @param  array   $field_data The array of form field data.
	 * @param  integer $user_id    The given users ID.
	 * @param  string  $field_type The type for the given form field.
	 * @param  string  $field_id   The ID of the form field.
	 * @return html               Markup for the given profile form field
	 */
	public function render_profile_field( $field_data, $user_id ) {
		switch ( $field_data['type'] ) {
			default:
			case 'text':
			case 'email':
			case 'url':
				printf(
					'<input type="%s" class="text-input" name="%s" id="%s" value="%s" />',
					esc_attr( $field_data['type'] ),
					esc_attr( $field_data['id'] ),
					esc_attr( $field_data['id'] ),
					esc_textarea( get_the_author_meta( $field_data['id'], $user_id ) )
				);
				break;
			case 'textarea':
				printf(
					'<textarea name="%s" id="%s" rows="3" cols="50">%s</textarea>',
					esc_attr( $field_data['id'] ),
					esc_attr( $field_data['id'] ),
					esc_textarea( get_the_author_meta( $field_data['id'], $user_id ) )
				);
				break;
		}
	}
}

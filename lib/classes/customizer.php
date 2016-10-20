<?php
/**
 * WordPress Customizer Functionality
 * @param  array $wp_customize Array of customizer panels.
 * @return nuLL
 * @since 1.0
 */
function yikes_custom_login_customizer_register( $wp_customize ) {

	/**
	 * Main YIKES Custom Login Panel (section)
	 */
	$wp_customize->add_panel( 'yikes_custom_login', array(
		'priority' => 10,
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => __( 'Custom Login by YIKES', 'custom-wp-login' ),
		'description' => __( 'Fully style the custom login screen.', 'custom-wp-login' ),
	) );

	/**
	 * Login Logo Section
	 */
	$wp_customize->add_section( 'login_logo_section', array(
		'priority' => 10,
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => __( 'Logo', 'custom-wp-login' ),
		'description' => __( 'Add your company/site logo.', 'custom-wp-login' ),
		'panel' => 'yikes_custom_login',
	) );

	$wp_customize->add_setting( 'login_logo', array(
		'default' => '',
		'type' => 'theme_mod',
		'capability' => 'edit_theme_options',
	) );

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'login_logo',
			array(
				'label'      => __( 'Background Image', 'custom-wp-login' ),
				'section'    => 'login_logo_section',
				'settings'   => 'login_logo',
				'context'    => 'login_logo',
			)
		)
	);

	/**
	 * Login Container Section
	 */
	$wp_customize->add_section( 'login_container', array(
			'priority' => 10,
			'capability' => 'edit_theme_options',
			'theme_supports' => '',
			'title' => __( 'Login Container', 'custom-wp-login' ),
			'description' => __( 'Alter the login container styles.', 'custom-wp-login' ),
			'panel' => 'yikes_custom_login',
	) );

	/**
	 * Login Background color
	 */
	$wp_customize->add_setting( 'login_container_background', array(
		'default' => '#F0E7DE',
		'type' => 'theme_mod',
		'capability' => 'edit_theme_options',
		'transport' => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'login_container_background',
			array(
				'default' => '#F0E7DE',
				'label'      => __( 'Login Container Background Color', 'custom-wp-login' ),
				'section'    => 'login_container',
				'settings'   => 'login_container_background',
			)
		)
	);

	/**
	 * Login Opacity Range Slider
	 */
	$wp_customize->add_setting( 'login_container_opacity', array(
		'default' => '.8',
		'type' => 'theme_mod',
		'capability' => 'edit_theme_options',
		'transport' => '',
	) );

	$wp_customize->add_control( 'login_container_opacity', array(
		'type' => 'range',
		'default' => '.8',
		'priority' => 10,
		'section' => 'login_container',
		'label' => __( 'Login Container Opacity', 'custom-wp-login' ),
		'description' => '',
		'input_attrs' => array(
			'min' => 0,
			'max' => 1,
			'step' => .1,
			'class' => 'yikes-custom-login-opacity-slider widefat',
		),
	) );

	/**
	 * Login Container Border Color
	 */
	$wp_customize->add_setting( 'login_container_border_color', array(
		'default' => '',
		'type' => 'theme_mod',
		'capability' => 'edit_theme_options',
		'transport' => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'login_container_border_color',
			array(
				'default' => '',
				'label'      => __( 'Login Container Border Color', 'custom-wp-login' ),
				'section'    => 'login_container',
				'settings'   => 'login_container_border_color',
			)
		)
	);

	/**
	 * Border Style (solid, dahsed, dotted)
	 */
	$wp_customize->add_setting( 'login_container_border_style', array(
		'default'        => 'solid',
		'capability'     => 'edit_theme_options',
	) );

  $wp_customize->add_control( 'login_container_border_style', array(
		'default' => 'solid',
		'settings' => 'login_container_border_style',
		'label'   => __( 'Login Container Border Style:', 'custom-wp-login' ),
		'section' => 'login_container',
		'type'    => 'select',
		'choices'    => array(
			'solid' => __( 'Solid', 'custom-wp-login' ),
			'dashed' => __( 'Dashed', 'custom-wp-login' ),
			'dotted' => __( 'Dotted', 'custom-wp-login' ),
		),
  ) );

	/**
	 * Login Container Border Opacity Range Slider
	 */
	$wp_customize->add_setting( 'login_container_border_opacity', array(
		'default' => '.8',
		'type' => 'theme_mod',
		'capability' => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'login_container_border_opacity', array(
		'type' => 'range',
		'default' => '.8',
		'priority' => 10,
		'section' => 'login_container',
		'label' => __( 'Login Container Border Opacity', 'custom-wp-login' ),
		'description' => '',
		'input_attrs' => array(
			'min' => 0,
			'max' => 1,
			'step' => .1,
			'class' => 'yikes-custom-login-opacity-slider widefat',
		),
	) );

	/**
	 * Login Border Width Input
	 */
	 $wp_customize->add_setting( 'login_container_border_width', array(
		'default' => 0,
		'type' => 'theme_mod',
		'capability' => 'edit_theme_options',
		'transport' => '',
		'sanitize_callback' => ''
	) );

 	$wp_customize->add_control( 'login_container_border_width', array(
		'default' => 0,
		'type' => 'number',
		'priority' => 10,
		'section' => 'login_container',
		'label' => __( 'Login Container Border Width', 'custom-wp-login' ),
		'description' => '',
		'input_attrs' => array(
			'min' => 0,
			'max' => 10,
			'step' => 1,
		),
	) );

	/**
	 * Login Container Border Radius
	 */
	$wp_customize->add_setting( 'login_container_border_radius', array(
		'default' => '12',
		'capability' => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'login_container_border_radius', array(
		'type' => 'range',
		'default' => '12',
		'priority' => 10,
		'section' => 'login_container',
		'label' => __( 'Login Container Border Radius', 'custom-wp-login' ),
		'description' => '',
		'input_attrs' => array(
			'min' => 0,
			'max' => 100,
			'step' => 5,
			'class' => 'yikes-custom-login-border-radius-slider widefat',
		),
	) );

	/**
	 * Login Container Text Color
	 */
	$wp_customize->add_setting( 'login_container_text_color', array(
		'default' => '#2d2d2d',
		'type' => 'theme_mod',
		'capability' => 'edit_theme_options',
		'transport' => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'login_container_text_color',
			array(
				'default' => '#2d2d2d',
				'label'      => __( 'Login Container Text Color', 'custom-wp-login' ),
				'description' => __( 'Set the color of the field labels and "Remember Me" label.', 'custom-wp-login' ),
				'section'    => 'login_container',
				'settings'   => 'login_container_text_color',
			)
		)
	);

	/**
	 * Sign In Button Text
	 */
	$wp_customize->add_setting('login_container_sign_in_button_text', array(
		'capability' => 'edit_theme_options',
		'type'       => 'theme_mod',
	));

	$wp_customize->add_control('login_container_sign_in_button_text', array(
		'settings' => 'login_container_sign_in_button_text',
		'section'  => 'login_container',
		'type'     => 'text',
		'label' => __( 'Sign In Button Text', 'custom-wp-login' ),
		'description' => __( 'Customize the sign in button text.', 'custom-wp-login' ),
	));

	/**
	 * Full Width Sign In Button
	 */
	$wp_customize->add_setting('login_container_full_width_sign_in_button', array(
		'capability' => 'edit_theme_options',
		'type'       => 'theme_mod',
	));

	$wp_customize->add_control('login_container_full_width_sign_in_button', array(
		'settings' => 'login_container_full_width_sign_in_button',
		'section'  => 'login_container',
		'type'     => 'checkbox',
		'label' => __( 'Full Width Sign In Button', 'custom-wp-login' ),
		'description' => __( 'Set the "Sign In" button to full width.', 'custom-wp-login' ),
	));

	/**
	 * Login Background color
	 */
	$wp_customize->add_setting( 'login_container_link_color', array(
		'default' => '#0000EE',
		'type' => 'theme_mod',
		'capability' => 'edit_theme_options',
		'transport' => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'login_container_link_color',
			array(
				'default' => '#0000EE',
				'label'      => __( 'Login Container Link Color', 'custom-wp-login' ),
				'description' => __( 'Set the color of the "Forgot Password" and "Register" links.', 'custom-wp-login' ),
				'section'    => 'login_container',
				'settings'   => 'login_container_link_color',
			)
		)
	);

	/**
	 * Hide 'Forgot your password?' link
	 */
	$wp_customize->add_setting('login_container_hide_forgot_password_link', array(
		'capability' => 'edit_theme_options',
		'type'       => 'theme_mod',
	));

	$wp_customize->add_control('login_container_hide_forgot_password_link', array(
		'settings' => 'login_container_hide_forgot_password_link',
		'section'  => 'login_container',
		'type'     => 'checkbox',
		'label' => __( 'Hide "Forgot Password" Link', 'custom-wp-login' ),
		'description' => __( 'Toggle the visiblity of the "Forgot your password?" link.', 'custom-wp-login' ),
	));

	/**
	 * Hide 'Register' link
	 */
	$wp_customize->add_setting('login_container_hide_register_link', array(
		'capability' => 'edit_theme_options',
		'type'       => 'theme_mod',
	));

	$wp_customize->add_control('login_container_hide_register_link', array(
		'settings' => 'login_container_hide_register_link',
		'section'  => 'login_container',
		'type'     => 'checkbox',
		'label' => __( 'Hide "Register" Link', 'custom-wp-login' ),
		'description' => __( 'Toggle the visiblity of the "Register" link.', 'custom-wp-login' ),
	));

	/**
	 * Background Section
	 */
	$wp_customize->add_section( 'login_background', array(
		'priority' => 10,
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => __( 'Background', 'custom-wp-login' ),
		'description' => '',
		'panel' => 'yikes_custom_login',
	) );

	$wp_customize->add_setting( 'login_background_image', array(
		'default' => '',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'login_background_image',
			array(
				'label'      => __( 'Background Image', 'custom-wp-login' ),
				'section'    => 'login_background',
				'settings'   => 'login_background_image',
			)
		)
	);

	/**
	 * Background Size
	 */
	$wp_customize->add_setting('login_background_size', array(
		'default'        => 'cover',
		'capability'     => 'edit_theme_options',
	));

	$wp_customize->add_control( 'login_background_size', array(
		'settings' => 'login_background_size',
		'label'   => __( 'Background Size:', 'custom-wp-login' ),
		'section' => 'login_background',
		'type'    => 'select',
		'choices'    => array(
			'cover' => __( 'Cover', 'custom-wp-login' ),
			'contain' => __( 'Contain', 'custom-wp-login' ),
			'default' => __( 'Default Size', 'custom-wp-login' ),
		),
		'input_attrs' => array(
			'class' => 'widefat',
		),
	));

	/**
	 * Background Position
	 */
	$wp_customize->add_setting( 'login_background_position', array(
		'default'        => 'center center',
		'capability'     => 'edit_theme_options',
	));

	$wp_customize->add_control( 'login_background_position', array(
		'settings' => 'login_background_position',
		'label'   => __( 'Background Position:', 'custom-wp-login' ),
		'section' => 'login_background',
		'type'    => 'select',
		'choices'    => array(
			'left top' => __( 'Left Top', 'custom-wp-login' ),
			'left center' => __( 'Left Center', 'custom-wp-login' ),
			'left bottom' => __( 'Left Bottom', 'custom-wp-login' ),
			'right top' => __( 'Right Top', 'custom-wp-login' ),
			'right center' => __( 'Right Center', 'custom-wp-login' ),
			'right bottom' => __( 'Right Bottom', 'custom-wp-login' ),
			'center top' => __( 'Center Top', 'custom-wp-login' ),
			'center center' => __( 'Center Center', 'custom-wp-login' ),
			'center bottom' => __( 'Center Bottom', 'custom-wp-login' ),
		),
	));

	/**
	 * Background Repeat
	 */
	$wp_customize->add_setting( 'login_background_repeat', array(
		'default'        => 'no-repeat',
		'capability'     => 'edit_theme_options',
	));

	$wp_customize->add_control( 'login_background_repeat', array(
		'default' => 'no-repeat',
		'settings' => 'login_background_repeat',
		'label'   => __( 'Background Repeat:', 'custom-wp-login' ),
		'section' => 'login_background',
		'type'    => 'select',
		'choices'    => array(
			'no-repeat' => __( 'No Repeat', 'custom-wp-login' ),
			'repeat-x' => __( 'Repeat-X (horizontal)', 'custom-wp-login' ),
			'repeat-y' => __( 'Repeat-Y (vertical)', 'custom-wp-login' ),
			'repeat' => __( 'Repeat', 'custom-wp-login' ),
		),
	));

	/**
	 * Additional custom stuff (css/js)
	 */
	$wp_customize->add_section( 'custom_scripts_and_styles', array(
		'priority' => 10,
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => __( 'Custom Scripts & Styles', 'custom-wp-login' ),
		'description' => '',
		'panel' => 'yikes_custom_login',
	) );

	$wp_customize->add_setting( 'yikes_login_custom_styles', array(
		'default' => '',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'esc_textarea',
	) );

	$wp_customize->add_control( 'yikes_login_custom_styles', array(
		'type' => 'textarea',
		'priority' => 10,
		'section' => 'custom_scripts_and_styles',
		'label' => __( 'Custom CSS', 'custom-wp-login' ),
		'description' => __( 'Enter custom styles in this field.', 'custom-wp-login' ),
	) );

	$wp_customize->add_setting( 'yikes_login_custom_scripts', array(
		'default' => '',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'esc_textarea',
	) );

	$wp_customize->add_control( 'yikes_login_custom_scripts', array(
			'type' => 'textarea',
			'priority' => 10,
			'section' => 'custom_scripts_and_styles',
			'label' => __( 'Custom Scripts', 'custom-wp-login' ),
			'description' => sprintf(
				_x( 'Enter custom scripts in this field. Anything entered here will be wrapped wrapped in %s.', 'Sample JavaScript code snippet.', 'custom-wp-login' ),
				'<code>jQuery( document ).ready( function() { /* Code will go here */ })</code>'
			),
	) );
}
add_action( 'customize_register', 'yikes_custom_login_customizer_register' );

[ ![Codeship Status for yikesinc/yikes-inc-custom-login](https://codeship.com/projects/81bd4740-e3b0-0133-076d-5e6dd4ce3e38/status?branch=master)](https://codeship.com/projects/146087)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-custom-login/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-custom-login/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-custom-login/badges/build.png?b=master)](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-custom-login/build-status/master)
<!-- When we have our unit tests set up, uncomment this line
[![Code Coverage](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-custom-login/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yikesinc/yikes-inc-custom-login/?branch=master)
-->

### YIKES Inc. Custom Login

A WordPress plugin that replaces the login flow with custom pages.

#### Shortcodes:

* `[custom-login-form]`
* `[custom-register-form]`
* `[custom-password-lost-form]`
* `[custom-password-reset-form]`
* `[account-info]` <small>(in the works)</small>

#### Custom Templates:

Users can override the default templates by copying `wp-content/plugins/yikes-custom-login/templates/` into the theme root, and renaming the directory `/yikes-custom-login/`. You can remove any of the templates you don't need, and customize the ones you will be using.

#### Additional Profile Fields

Users can customize what fields are displayed and editable on the profile page using some of the built in filters.

To get custom fields to display on the 'Update Profile' page, you need to assign a custom `user_meta` **with a value** to all the users you want to have access to the fields. If no data is assigned to the user, the field will not display. (You can assign a null or empty value, to get things to display).

Here is a simple example to create a `twitter` text input field, where users can enter their Twitter handle.

**Example**
```php
/**
 * Assign 'twitter' meta data to all of your users, and thus have it appear on the profile form
 */
function add_twitter_usermeta() {
	$users = get_users();
	foreach ( $users as $user ) {
		update_user_meta( $user->ID, 'twitter', ' ' );
	}
}
add_action( 'admin_init', 'add_twitter_usermeta' );
```

Additionally, if you no longer want a specific field to display on the field **and** don't need the data stored in the database anymore, you can use the same function as above, but swap `update_user_meta` to `delete_user_meta` and delete the third parameter. Deleting the user meta will also remove the field from the 'Profile' form.

**Example**
```php
/**
 * Delete the 'twitter' meta data we created above, and thus remove it from the profile form
 */
function delete_twitter_usermeta() {
	$users = get_users();
	foreach ( $users as $user ) {
		delete_user_meta( $user->ID, 'twitter' );
	}
}
add_action( 'admin_init', 'delete_twitter_usermeta' );
```

**Note:** The above two functions will run every time the dashboard is loaded, so you'll want to load the dashboard once or twice, and then remove this snippet from your **functions.php** file. Once run, you should see a text input field appear on the 'Profile' page. You may need to update the label <small>(see below)</small>.

To update the new form field label you'll want to use the built in filter `yikes-custom-login-KEY-label`, where KEY is the field `meta_id` or 'twitter' (2nd parameter in `update_uesr_meta` above). So the filter we'll use for the 'twitter' field is `yikes-custom-login-twitter-label`.

**Example Usage**

```php
/**
 * Alter the Twitter 'Profile' form field label to 'Twitter Handle'
 * @param  string $label The initial label that we will be filtering.
 * @return string        The new field label to be used for the given key.
 */
function filter_profile_field_labels( $label ) {
	// Twitter field label
	if ( 'twitter' === $label ) {
		return 'Twitter Handle';
	}
}
add_filter( 'yikes-custom-login-twitter-label', 'filter_profile_field_labels' );
```

#### Filters

* `yikes-inc-custom-login-redirect`

Page to redirect non-admin users to when they successfully login. **Note:** This URL must be a URL of a page on this site. `wp_validate_redirect` is used, to confirm a valid URL before redirecting the user.

Default redirects to the Account Info ( 'member-account' ) page.

**Example Usage:**
```php
function yikes_custom_login_redirect_non_admins() {
	// Redirect non-admins to page 8
	return esc_url( get_the_permalink( 8 ) );
}
add_filter( 'yikes-inc-custom-login-redirect', 'yikes_custom_login_redirect_non_admins' );
```

* `yikes-custom-login-restrict-dashboard-capability`

Filter who can access the dashboard when 'Restrict Dashboard Access' is enabled. The dashboard is limited by user capability, and defaults to 'manage_options'. This means that anyone who does **not** have the 'manage_options' capability will not be able to access the dashboard (ie: all users who are not admins).

**Example Usage:**
```php
/**
 * Switch which users are restricted from the dashboard
 * @param  string $user_cap The highest user capability that should have access to the dashboard.
 * @return string 					The new user capability to limit.
 */
function yikes_custom_login_custom_restrictions( $user_cap ) {
	/**
	 * Limit all users who do not have the 'publish_posts' capability
	 * Contributors, Subscribers
	 */
	return 'publish_posts';
}
add_filter( 'yikes-custom-login-restrict-dashboard-capability', 'yikes_custom_login_custom_restrictions' );
```

* `yikes-custom-login-profile-fields`

Filter the profile fields however you need. Alter the form field labels, field type and the data associated with the field. This can also be used to rearrange the profile form fields how you need.

**Example Usage**
```php
/**
 * Rearange the form fields on the profile page (some fields are excluded for brevity - and a custom twitter field has been added)
 */
function yikes_rearrange_profile_fields( $fields, $user_id ) {
	/* Re-arrange our fields */
	$fields = array(
		'first_name' => $fields['first_name'],
		'nickname' => $fields['nickname'],
		'last_name' => $fields['last_name'],
		'user_email' => $fields['user_email'],
		'description' => $fields['description'],
		'twitter' => $fields['twitter'],
	);
	/* Return the fields */
	return $fields;
}
add_filter( 'yikes-custom-login-profile-fields', 'yikes_rearrange_profile_fields' );
```

* `yikes-custom-login-twitter-label`

Filter a profile field label. Helpful when you have added custom form fields to your profile page, and want to alter the field label.

**Example Usage**
```php
/**
 *  Filter the label for custom Twitter field
 */
function filter_profile_field_labels( $label ) {
	// Twitter field label
	if ( 'twitter' === $label ) {
		return 'Twitter Handle';
	}
}
add_filter( 'yikes-custom-login-twitter-label', 'filter_profile_field_labels' );
```

* `yikes-custom-login-preloader`

Use a custom preloader image for all full width page templates (Login, Password Lost/Reset, New User Registration).

**Example Usage**
```php
/**
 *  Use a custom preloader instead of the standard wpspin_light.gif
 *  In this example, we'll use a cute cupcake
 */
function custom_login_preloader_image( $preloader_url ) {
	// Return a new URL to use as the preloader
	return esc_url( 'https://media.giphy.com/media/4Lg39ddcSzBIY/giphy.gif' );
}
add_filter( 'yikes-custom-login-preloader', 'custom_login_preloader_image' );
```

* `yikes-login-pages-query-post-type`

Alter what post types are queried and displayed in the drop down on the 'Pages' option page.

**Example Usage**
```php
/**
 * Add 'post' post types to the drop down, to allow users to redirect there.
 */
function enable_posts_in_pages_dropdown( $post_types ) {
	$post_types[] = 'post';
	return $post_types;
}
add_filter( 'yikes-login-pages-query-post-type', 'enable_posts_in_pages_dropdown' );
```

#### Actions

###### Login Form

* `yikes-inc-custom-login-before-login-form`
* `yikes-inc-custom-login-after-login-form`


###### Password Lost form

* `yikes-inc-custom-login-before-password-lost-form`
* `yikes-inc-custom-login-after-password-lost-form`

###### Password Reset form

* `yikes-inc-custom-login-before-password-reset-form`
* `yikes-inc-custom-login-after-password-reset-form`

###### Register Form

* `yikes-inc-custom-login-before-register-form`
* `yikes-inc-custom-login-after-register-form`


#### FAQs

* Can I move the logo from above the container, to inside the container - above the form?

Yes! First you'll want to remove the action from the location it currently hooks into (`yikes-custom-login-branding`). After we unhook it from the default location, we'll just re-hook it into the new location where we want it to appear (`yikes-custom-login-login-page-before-form`).

The function that generates the logo above the login, password reset, set new password and registration forms is `yikes_custom_login_generate_branding_logo()`.

**Example**
```php
/**
 * Move the logo from above the container, to inside the container above the forms.
 * This function should be placed at the bottom of your functions.php file, inside of php tags.
 */
global $yikes_custom_login;
// remove the action
remove_action( 'yikes-custom-login-branding', array( $yikes_custom_login, 'yikes_custom_login_generate_branding_logo' ) );
// re-hook the function into a new location
add_action( 'yikes-custom-login-login-page-before-form', array( $yikes_custom_login, 'yikes_custom_login_generate_branding_logo' ) );
```

* How do I add placeholder values to the email and/or passowrd fields on the login form?

Using the 'Custom Scripts' field on the login page customizer, you can add some JavaScript to populate the placeholder values with whatever text is needed. Feel free to copy the example below, and paste it into the 'Custom Scripts' text area field.

**Example**

```javascript
jQuery( '#user_login' ).attr( 'placeholder', 'Username/Email' );
jQuery( '#user_pass' ).attr( 'placeholder', '********' );
```

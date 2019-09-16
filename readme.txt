=== Custom WP Login ===
Contributors: yikesinc, eherman24, liljimmi, yikesitskevin
Tags: custom, login, forms, redirects, member, guest, members, register
Requires at least: 4.0
Tested up to: 4.9.1
Stable tag: 1.2.3
License: GPLv2 or later

Quickly generate custom front-end login, register, password reset and account profile forms for a WordPress site. 

== Description ==

Custom WP Login replaces the WordPress login flow with custom pages. It allows you to have a complete user registration and login experience on the public-facing pages of your WordPress site. This is useful for sites who want to use WordPress' built-in users functionality, but only want to keep users on the front-end of the site and not in the WordPress dashboard.

**Features**

* Set pages for Login form, Registration form, Reset Password form, Select New Password for and Account Profile form.
* Customize the Login Page, with a logo, background, image, form colors and more!
* Add reCaptcha to the Login form.
* Customize the welcome email users receive after they register.


Shortcodes:
`[custom-login-form]`
`[custom-register-form]`
`[custom-password-lost-form]`
`[custom-password-reset-form]`
`[account-info]`

== Installation ==

1. Download the plugin .zip file and make note of where on your computer you downloaded it to.
2. In the WordPress admin (yourdomain.com/wp-admin) go to Plugins > Add New or click the "Add New" button on the main plugins screen.
3. On the following screen, click the "Upload Plugin" button.
4. Browse your computer to where you downloaded the plugin .zip file, select it and click the "Install Now" button.
5. After the plugin has successfully installed, click "Activate Plugin" and enjoy!

== Screenshots ==

1. Customize your login screen using the WordPress Customizer
2. Customizer: Change login form background color
3. Customizer: Add an image above the form
4. Customizer: Add a background image
5. Customizer: Form with background image and form image
6. Activating the plugin will automatically create 5 new WordPress pages
7. The settings page
8. Change which WordPress pages correspond to the default Login / Account / Registration / Reset Password / Select New Password screens
9. You can create and choose your own page to act as the login screen 

== Changelog ==

= 1.2.3 - July 29, 2019 =
* Bug fixed with `[account-info]` that caused red error box to appear.

= 1.2.2 - December 5th, 2017 =
* Added an option to set the minimum password strength required for a user to change their password.
* Added a filter to allow changes to the placement of the password reset button (`yikes-custom-login-password-reset-above-form`).
* Added a filter to allow changes to the reset password text (`yikes-custom-login-password-reset-text`).
* Added a filter to allow changes to the default password reset text instructions (`yikes-custom-login-password-reset-instructions`).
* Added an action hook to the password reset email (`yikes-custom-login-email-above-disclaimer`).
* Added action hooks to the backend when updating a user's profile (`yikes-custom-login-before-profile-update`, `yikes-custom-login-before-profile-update`).

= 1.2.0, 1.2.1 =
* Enabled i18n/l10n (fully)

= 1.1.0 =
* Enabled i18n/l10n (partially)

= 1.0.0 =
* Initial Release
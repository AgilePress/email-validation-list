<?php
/*
Plugin Name: Email Validation List
Plugin URI: https://martinenrique.com/email-validation-list/
Description: Validates that the email entered by the user matches an email within a list of emails during the registration process.
Version: 1.0
Author: Martín Enrique
Author URI: https://martinenrique.com
License: GPL2
*/

// Action that runs during the user registration process
add_action('register_post', 'validate_email');

function validate_email($post_data) {
    // Get the email entered by the user in the registration form
    $user_email = sanitize_email($_POST['user_email']);

    // Get the list of allowed emails from the plugin settings
    $allowed_emails = get_option('email_validation_allowed_emails');

    // Convert the list of allowed emails into an array
    $allowed_emails = explode("\n", $allowed_emails);

    // Remove whitespace around each allowed email
    $allowed_emails = array_map('trim', $allowed_emails);

    // Check if the entered email is in the list of allowed emails
    if (!in_array($user_email, $allowed_emails)) {
        // If the email does not match, display an error message and stop the registration
        $error_message = get_option('email_validation_error_message', 'The entered email is not valid. <a href="' . wp_registration_url() . '">Go back to the registration page</a>.');
        wp_die($error_message);
    }
}

// Plugin settings function
function email_validation_plugin_settings() {
	add_settings_section("email_validation_list_section", "Email Validation List", null, "general");
    // Register the setting option for the list of allowed emails
    register_setting('general', 'email_validation_allowed_emails');
    add_settings_field(
        'email_validation_allowed_emails',
        'Allowed Emails',
        'email_validation_allowed_emails_callback',
        'general',
		"email_validation_list_section"
    );

    // Register the setting option for the error message
    register_setting('general', 'email_validation_error_message');
    add_settings_field(
        'email_validation_error_message',
        'Error Message',
        'email_validation_error_message_callback',
        'general',
		"email_validation_list_section"
    );
}

// Callback function to display the textarea for the list of allowed emails
function email_validation_allowed_emails_callback() {
    $allowed_emails = get_option('email_validation_allowed_emails');
    echo '<textarea name="email_validation_allowed_emails" rows="10" cols="50">' . esc_textarea($allowed_emails) . '</textarea>';
}

// Callback function to display the textarea for the error message
function email_validation_error_message_callback() {
    $error_message = get_option('email_validation_error_message', 'The entered email is not valid. <a href="' . wp_registration_url() . '">Go back to the registration page</a>.');
    echo '<textarea name="email_validation_error_message" rows="10" cols="50">' . esc_textarea($error_message) . '</textarea>';
}

// Register the plugin settings function
add_action('admin_init', 'email_validation_plugin_settings');

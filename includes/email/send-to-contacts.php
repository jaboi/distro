<?php
// Ensure this file is being accessed through WordPress
if (!defined('ABSPATH')) {
    exit;
}

function send_to_sel_contacts() {
    global $wpdb;

    // Log received data for debugging
    error_log('Received POST data: ' . print_r($_POST, true));

    // Include the email template functions file
    //include_once(plugin_dir_path(__FILE__) . 'email-template.php');

    // Set custom email name and address
    //add_filter('wp_mail_from_name', 'custom_mail_from_name');
    //add_filter('wp_mail_from', 'custom_mail_from_email');

    // Verify the nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'send_to_sel_contacts_nonce')) {
        wp_send_json_error('Nonce verification failed');
        return;
    }

    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error('You do not have sufficient permissions to access this page.');
        return;
    }

    // Check if the 'contacts' field is present and is an array
    if (!isset($_POST['contacts']) || !is_array($_POST['contacts'])) {
        wp_send_json_error('No contacts selected');
        return;
    }

    // Sanitize and collect selected email addresses
    $selected_contacts = array_map('sanitize_email', $_POST['contacts']);

    // cc emails
    // Sanitize and collect selected email addresses for CC
    $cc_contacts = isset($_POST['cc_emails']) && is_array($_POST['cc_emails']) 
        ? array_map('sanitize_email', $_POST['cc_emails']) 
        : array();


    // Validate email addresses
    foreach ($selected_contacts as $email) {
        if (!is_email($email)) {
            wp_send_json_error('Invalid email address detected: ' . $email);
            return;
        }
    }

    foreach ($cc_contacts as $email) {
        if (!is_email($email)) {
            wp_send_json_error('Invalid CC email address detected: ' . $email);
            return;
        }
    }

    // If no valid email addresses are found, return an error
    if (empty($selected_contacts)) {
        wp_send_json_error('No valid email addresses selected');
        return;
    }

    
    

    

    // Subject of the email (can be customized as needed)
    $email_subject = 'Press Release ';

    // Set up email headers to handle HTML content
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Add CC recipients if there are any
    if (!empty($cc_contacts)) {
        $cc_header = 'Cc: ' . implode(',', $cc_contacts);
        $headers[] = $cc_header; // Add the CC header to the headers array
    }

    require plugin_dir_path(__FILE__) . 'email-content.php';

    

    // Add a filter to modify the "from" email address and "from" name
    /*add_filter('', function() use ($profile_email) {
        return $profile_email; // Use the email address from the selected profile
    });*/
    add_filter('wp_mail_from', function() use ($profile_email) {
        return $profile_email;
    });

    add_filter('wp_mail_from_name', function() use ($sender_name) {
        return $sender_name; // You can replace this with the profile's sender name if available
    });

    // Loop through each selected contact and send an email
    foreach ($selected_contacts as $email) {
        //wp_mail($email, $email_subject, $post_content);
        wp_mail($email, $email_subject, $emailBody, $headers);
    }

    // Remove custom filters to prevent them from affecting other emails
    remove_filter('wp_mail_from', function() use ($profile_email) {});
    remove_filter('wp_mail_from_name', function() use ($sender_name) {});

    // Return success response
    wp_send_json_success('Emails sent successfully to selected contacts');
}


// Hook the function to handle form submission (this assumes an AJAX form submission)
add_action('wp_ajax_send_to_sel_contacts', 'send_to_sel_contacts');
add_action('wp_ajax_nopriv_send_to_sel_contacts', 'send_to_sel_contacts');
?>
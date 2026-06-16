<?php
// Ensure this file is being accessed through WordPress
if (!defined('ABSPATH')) {
    exit;
}

function ajax_send_to_orgs() {
    // Verify the nonce
    check_ajax_referer('send_to_orgs_nonce', 'nonce');

    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error('You do not have sufficient permissions to access this page.');
    }

    // Check if post_id is valid
    if (isset($_POST['post_id']) && is_numeric($_POST['post_id'])) {
        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);

        // Debugging output
        error_log('Sending email for post ID: ' . $post_id);

        // Include the email template functions file
        include_once(plugin_dir_path(__FILE__) . 'email-template.php');

        // Set custom email name and address
        add_filter('wp_mail_from_name', 'custom_mail_from_name');
        add_filter('wp_mail_from', 'custom_mail_from_email');

        global $wpdb;
        $table_name_organization = $wpdb->prefix . 'organizations';
        $organizations = $wpdb->get_results("SELECT contact_email FROM $table_name_organization WHERE contact_email != ''", ARRAY_A);

        $all_sent = true;
        $errors = array();

        foreach ($organizations as $org) {
            $email_content = get_email_template($post); // Generate the email content using the template

            $headers = array('Content-Type: text/html; charset=UTF-8');

            $success = wp_mail(
                $org['contact_email'],
                'Org Press Release: ' . $post->post_title,
                $email_content,
                $headers
            );

            if ($success) {
                error_log('Email sent to organization: ' . $org['contact_email']);
            } else {
                error_log('Failed to send email to organization: ' . $org['contact_email']);
                $all_sent = false;
                $errors[] = $org['contact_email'];
            }
        }

        // Remove the custom filters after sending the email
        remove_filter('wp_mail_from_name', 'custom_mail_from_name');
        remove_filter('wp_mail_from', 'custom_mail_from_email');

        // Update the post meta to indicate emails were sent
        if ($all_sent) {
            if (!add_post_meta($post_id, '_press_release_status', 'Sent to Orgs', true)) {
                update_post_meta($post_id, '_press_release_status', 'Sent to Orgs');
            }
            wp_send_json_success('Sent to all organizations.');
        } else {
            $error_message = 'Some emails failed to send to the following addresses: ' . implode(', ', $errors);
            wp_send_json_error($error_message);
        }
    } else {
        wp_send_json_error('Invalid post ID.');
    }
}

// Custom email "from" name
/*function custom_mail_from_name($original_name) {
    $from_name = get_option('email_from_name', '');
    return empty($from_name) ? get_option('blogname') : $from_name;
}

// Custom email "from" address
function custom_mail_from_email($original_email) {
    $from_email = get_option('email_from_address', '');
    return empty($from_email) ? get_option('admin_email') : $from_email;
}
*/
// Hook into WordPress AJAX
add_action('wp_ajax_send_to_orgs', 'ajax_send_to_orgs');

?>
<?php
// Ensure this file is being accessed through WordPress
if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_send_to_contacts_action', 'send_to_contacts_handler'); // For logged-in users
add_action('wp_ajax_nopriv_send_to_contacts_action', 'send_to_contacts_handler'); // For non-logged-in users

function send_to_contacts_handler() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'send_to_contacts_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    // Check if post_release_id is valid
    if (isset($_POST['post_release_id']) && is_numeric($_POST['post_release_id'])) {
        $post_id = intval($_POST['post_release_id']);
        $post = get_post($post_id);

        if (!$post) {
            wp_send_json_error('Invalid Post ID.');
        }

        // Retrieve selected contacts from POST request
        if (!isset($_POST['contacts']) || !is_array($_POST['contacts'])) {
            wp_send_json_error('No contacts selected.');
        }

        // Sanitize and validate contacts
        $selected_contacts = array_map('sanitize_email', $_POST['contacts']);
        $subject = get_the_title($post_id); // Email subject is the post title
        $message = apply_filters('the_content', $post->post_content); // Get post content as email message

        // Send email to each contact
        foreach ($selected_contacts as $contact_email) {
            if (is_email($contact_email)) {
                wp_mail($contact_email, $subject, $message);
            } else {
                wp_send_json_error('Invalid email: ' . esc_html($contact_email));
            }
        }

        // Send JSON response on success
        wp_send_json_success('Emails sent successfully!');
    } else {
        wp_send_json_error('Post ID is not set or invalid.');
    }
}
?>
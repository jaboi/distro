<?php
add_action('wp_ajax_update_sender_profile_ajax', 'update_sender_profile_ajax');
add_action('wp_ajax_nopriv_update_sender_profile_ajax', 'update_sender_profile_ajax'); // For non-logged-in users, if needed

function update_sender_profile_ajax() {
    // Check if the user has the right capabilities (optional, but recommended)
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'You do not have permission to update this profile.'));
    }

    // Verify the nonce
    if (!isset($_POST['update_profile_nonce']) || !wp_verify_nonce($_POST['update_profile_nonce'], 'update_profile_action')) {
        wp_send_json_error(array('message' => 'Security check failed.'));
    }

    // Ensure required data is present
    if (!isset($_POST['update_profile_id']) || !isset($_POST['header_content']) || !isset($_POST['about_content']) || !isset($_POST['footer_content'])) {
        wp_send_json_error(array('message' => 'Missing form data.'));
    }

    global $wpdb;
    $sender_profiles_table = $wpdb->prefix . 'sender_profiles';

    // Sanitize inputs
    $update_profile_id = intval($_POST['update_profile_id']);
    $header_id = intval($_POST['header_content']);
    $about_id = intval($_POST['about_content']);
    $footer_id = intval($_POST['footer_content']);

    // Update the sender_profiles table with the selected section IDs
    $updated = $wpdb->update(
        $sender_profiles_table,
        array(
            'header_id' => $header_id,
            'about_id' => $about_id,
            'footer_id' => $footer_id
        ),
        array('id' => $update_profile_id),
        array('%d', '%d', '%d'),
        array('%d')
    );

    // Check if the update was successful
    if ($updated !== false) {
        // Send a JSON response indicating success
        wp_send_json_success(array('message' => 'Profile updated successfully!'));
    } else {
        // Send a JSON response indicating failure
        wp_send_json_error(array('message' => 'Failed to update sender profile.'));
    }
}
?>
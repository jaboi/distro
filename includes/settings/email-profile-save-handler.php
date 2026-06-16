<?php
// Ensure this is being run within WordPress context
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (isset($_POST['save_email_settings'])) {
    // Verify the nonce for security
    if (!isset($_POST['email_settings_nonce']) || !wp_verify_nonce($_POST['email_settings_nonce'], 'save_email_settings')) {
        wp_die(__('Security check failed', 'textdomain'));
    }

    // Get the form values and sanitize them
    $profile_name = sanitize_text_field($_POST['profile_name']);
    $sender_name = sanitize_text_field($_POST['sender_name']);
    $sender_email = sanitize_email($_POST['sender_email']);

    // New fields
    $featured_img_pos = sanitize_text_field($_POST['featured_img_pos']);
    $font_color = sanitize_hex_color($_POST['font_color']); // Sanitizing color input
    $link_color = sanitize_hex_color($_POST['link_color']); // Sanitizing color input
    $font = sanitize_text_field($_POST['font_opt']);

    // Validate required fields (optional)
    if (empty($profile_name) || empty($sender_name) || empty($sender_email)) {
        echo '<div class="error">' . __('All fields are required', 'textdomain') . '</div>';
    } else {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sender_profiles'; // Ensure your table name is correct

        // Insert the form data into the sender_profiles table
        $result = $wpdb->insert(
            $table_name,
            array(
                'profile_name' => $profile_name,
                'sender_name' => $sender_name,
                'email_address' => $sender_email,
                'featured_img_pos' => $featured_img_pos, // Save new fields
                'font_color' => $font_color,
                'link_color' => $link_color,
                'font_opt' => $font,
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s') // Data format: string for all
        );

        // Check if insert was successful
        if ($result) {
            echo '<div class="updated">' . __('Profile saved successfully', 'textdomain') . '</div>';
        } else {
            echo '<div class="error">' . __('Failed to save profile', 'textdomain') . '</div>';
        }
    }
}
?>
<?php
/*// Ensure this is being run within WordPress context
if (!defined('ABSPATH')) {
    exit;
}

// Handle the AJAX request
add_action('wp_ajax_save_email_settings', 'save_email_settings_ajax_handler');
add_action('wp_ajax_nopriv_save_email_settings', 'save_email_settings_ajax_handler'); // For non-logged-in users (if needed)

function save_email_settings_ajax_handler() {
    // Verify the nonce for security
    if (!isset($_POST['email_settings_nonce']) || !wp_verify_nonce($_POST['email_settings_nonce'], 'save_email_settings')) {
        wp_send_json_error(array('message' => __('Security check failed.', 'textdomain')));
    }

    // Sanitize form inputs
    $profile_name = sanitize_text_field($_POST['profile_name']);
    $sender_name = sanitize_text_field($_POST['sender_name']);
    $sender_email = sanitize_email($_POST['sender_email']);

    // Validate required fields
    if (empty($profile_name) || empty($sender_name) || empty($sender_email)) {
        wp_send_json_error(array('message' => __('All fields are required.', 'textdomain')));
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'sender_profiles'; // Ensure the correct table name

    // Insert data into the database
    $result = $wpdb->insert(
        $table_name,
        array(
            'profile_name' => $profile_name,
            'sender_name' => $sender_name,
            'email_address' => $sender_email,
        ),
        array('%s', '%s', '%s') // Data format
    );

    // Check if the insert was successful
    if ($result) {
        wp_send_json_success(array('message' => __('Profile saved successfully.', 'textdomain')));
    } else {
        wp_send_json_error(array('message' => __('Failed to save profile.', 'textdomain')));
    }
}*/
?>

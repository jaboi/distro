<?php
// Hook into WordPress AJAX
add_action('wp_ajax_generate_email_preview', 'generate_email_preview');
add_action('wp_ajax_nopriv_generate_email_preview', 'generate_email_preview');

function generate_email_preview() {
    // Verify if profile_id and post_id are set
    if (!isset($_POST['profile_id']) || !isset($_POST['post_id'])) {
        wp_send_json_error('Invalid data received.');
        exit;
    }

    // Sanitize the received data
    $profile_id = intval($_POST['profile_id']);
    $post_id = intval($_POST['post_id']);
    $img_pos = sanitize_text_field($_POST['img_pos']);

    // Include the email-content.php file where $emailBody is generated
    require plugin_dir_path(__FILE__) . '../email/email-content.php'; // Adjust path accordingly

    // Assuming email-content.php generates $emailBody
    if (isset($emailBody)) {
        // Send the generated email body back as a JSON response
        wp_send_json_success(array('emailBody' => $emailBody));
    } else {
        wp_send_json_error('Failed to generate the email content.');
    }

    exit; // Ensure no extra output is sent
}
?>

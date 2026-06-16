<?php
// Ensure that this file is being accessed through WordPress.
if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_download_eml', 'download_eml_function');
add_action('wp_ajax_nopriv_download_eml', 'download_eml_function');

function download_eml_function() {
    // Check if all required data is present
    if (isset($_POST['post_id']) ) {
        $post_id = sanitize_text_field($_POST['post_id']);
        $post_title = sanitize_text_field($_POST['post_title']);
        $email_from = stripslashes($_POST['email_from']);
        $email_cc = stripslashes($_POST['cc_emails']);
        $email_sender = stripslashes($_POST['profile_sender']);
        //$email_header = stripslashes($_POST['email_header']);
        //$email_about = stripslashes($_POST['email_about']);
        //$email_footer = stripslashes($_POST['email_footer']);
        
        $site_name = get_bloginfo('name');
        $post_link = get_permalink($post_id); // Get the post link

        // Generate email content as JSON
        ob_start(); // Start output buffering
        //include 'email-content.php'; // Include the file that returns JSON
        require plugin_dir_path(__FILE__) . 'email-content.php';
        $email_body_json = ob_get_clean(); // Get the JSON string from email-content.php

        // Set the email subject line
        $subject_line = 'Press Release: ' . $post_title;

        // Dummy email values (replace these with actual email data)
        $to = $email_cc;
        $cc = $email_from . "," . $email_cc;
        $from_email = $email_from;

        // Construct the email content
        $email_content = '<html><body style="font-family:'.$font_family.'!important;'.$display_text_color.'">';
        $email_content .= '<div>' . $emailBody . '</div>';
        $email_content .= '</body></html>';


        // Create the .eml content
        $eml_content = 'To: ' . $to . "\n";
        $eml_content .= 'Cc: ' . $cc . "\n";
        $eml_content .= 'From: ' . esc_html($email_sender) . ' <' . esc_html($email_from) . '>' . "\n";
        //$eml_content .= 'Bcc: ' . $bcc . "\n"; // Make sure $bcc is defined if needed
        //$eml_content .= 'From: ' . $site_name . ' <' . $from_email . '>' . "\n"; // Include website name as the sender
        $eml_content .= 'Subject: ' . $subject_line . "\n";
        $eml_content .= 'X-Unsent: 1' . "\n";
        $eml_content .= 'Content-Type: text/html' . "\n\n";
        $eml_content .= $email_content;

        // Send the headers for file download
        header('Content-Description: File Transfer');
        header('Content-Type: text/plain'); // Correct MIME type for binary data
        header('Content-Disposition: attachment; filename=PressRelease-' . sanitize_title($_POST['post_title']) . '.eml');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($eml_content)); // Include the length of the content

        echo $eml_content;

        exit;
    } else {
        wp_send_json_error('Missing data to generate email.');
    }
}
?>
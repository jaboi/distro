<?php
// Ensure that this file is being accessed through WordPress.
if (!defined('ABSPATH')) {
    exit;
}
function send_to() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the post_release_id
        $post_release_id = isset($_POST['post_release_id']) ? $_POST['post_release_id'] : '';
        $sending_opt = isset($_POST['sending_opt']) ? $_POST['sending_opt'] : '';
        $contacts = isset($_POST['contacts']) ? $_POST['contacts'] : [];

        // Check if necessary data is present
        if (empty($post_release_id) || empty($contacts)) {
            echo "Missing required information.";
            exit;
        }

        // Process the contacts (example: send emails)
        foreach ($contacts as $contact) {
            // You can customize the email subject and content
            $subject = "Press Release for Post ID: " . $post_release_id;
            $message = "Here is the content of the press release (ID: $post_release_id).";
            $headers = "From: pressrelease@example.com";

            // Example to send an email (can be customized)
            // mail($contact, $subject, $message, $headers);

            // For now, let's just simulate and print the contact
            echo "Email sent to: " . $contact . "<br>";
        }

        // Return success message (can be more elaborate)
        echo "Press release has been sent to all selected contacts.";
    } else {
        echo "Invalid request method.";
    }
}
?>
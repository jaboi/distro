<?php
// Ensure this file is being accessed through WordPress
if (!defined('ABSPATH')) {
    exit;
}

function delete_contact() {
    // Verify the user's capability
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have permission to perform this action', 'textdomain'));
    }

    global $wpdb;
    $table_name_contact = $wpdb->prefix . 'contacts';
    $table_name_group_relationships = $wpdb->prefix . 'group_relationships';

    // Validate the contact_id parameter
    if (isset($_GET['contact_id']) && is_numeric($_GET['contact_id'])) {
        $contact_id = intval($_GET['contact_id']);

        // First, delete any entries in the group_relationships table where object_id matches the organization ID
        $wpdb->delete(
            $table_name_group_relationships,
            array('object_id' => $contact_id),
            array('%d')
        );
        
        // Delete the contact from the database
        $wpdb->delete(
            $table_name_contact,
            array('id' => $contact_id),
            array('%d')
        );

        // Redirect back to the contact page
        wp_redirect(admin_url('admin.php?page=contact_page'));
        exit;
    } else {
        wp_die(__('Invalid contact ID.', 'textdomain'));
    }
}

// Hook into admin-post.php to handle the delete action
add_action('admin_post_delete_contact', 'delete_contact');
?>
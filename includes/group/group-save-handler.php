<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function save_group() {
    global $wpdb;

    // Check if the form was submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Capture and sanitize input data
        $group_name = sanitize_text_field($_POST['group_name']);
        $description = sanitize_textarea_field($_POST['description']);
        $group_type = sanitize_text_field($_POST['group_type']);

        // Define table names
        $table_name_groups = $wpdb->prefix . 'groups';
        $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';

        // Insert new group into the groups table
        $wpdb->insert(
            $table_name_groups,
            array(
                'group_name' => $group_name,
                'description' => $description,
            ),
            array('%s', '%s')
        );

        // Get the inserted group ID
        $new_group_id = $wpdb->insert_id;

        // Insert the group_type into the group_taxonomy table
        $wpdb->insert(
            $table_name_group_taxonomy,
            array(
                'groups_id' => $new_group_id,
                'group_type' => $group_type,
            ),
            array('%d', '%s')
        );

        // Redirect to avoid form resubmission and show success message
        wp_redirect(admin_url('admin.php?page=group_page&status=success'));
        exit;
    }
}

// Hook to handle form submissions for logged-in users
add_action('admin_post_save_group', 'save_group');
// Hook to handle form submissions for non-logged-in users (not typically necessary in admin context)
add_action('admin_post_nopriv_save_group', 'save_group');
?>
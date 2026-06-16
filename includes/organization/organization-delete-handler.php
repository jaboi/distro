<?php
// Ensure this file is being accessed through WordPress
if (!defined('ABSPATH')) {
    exit;
}

function delete_organization() {
    // Check if the current user has the required capability
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have permission to perform this action', 'textdomain'));
    }

    global $wpdb;
    $table_name_org = $wpdb->prefix . 'organizations';
    $table_name_group_relationships = $wpdb->prefix . 'group_relationships';

    // Validate the org_id parameter
    if (isset($_GET['org_id']) && is_numeric($_GET['org_id'])) {
        $org_id = intval($_GET['org_id']);

        // First, delete any entries in the group_relationships table where object_id matches the organization ID
        $wpdb->delete(
            $table_name_group_relationships,
            array('object_id' => $org_id),
            array('%d')
        );

        // Then, delete the organization from the organizations table
        $wpdb->delete(
            $table_name_org,
            array('id' => $org_id),
            array('%d')
        );

        // Redirect back to the organization page
        wp_redirect(admin_url('admin.php?page=organization_page'));
        exit;
    } else {
        wp_die(__('Invalid organization ID.', 'textdomain'));
    }
}

// Hook this function to the appropriate action if necessary
add_action('admin_post_delete_organization', 'delete_organization');
?>
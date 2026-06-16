<?php
// Ensure that this file is being accessed through WordPress.
if (!defined('ABSPATH')) {
    exit;
}

function delete_group() {
    global $wpdb;

    // Log entry to see if function is triggered
    error_log('delete_group function triggered.');

    // Check for required parameters
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        error_log('Invalid ID parameter.');
        wp_redirect(admin_url('admin.php?page=group_page'));
        exit;
    }

    $group_id = intval($_GET['id']);
    error_log('Deleting group with ID: ' . $group_id);

    // Define table names
    $table_name_groups = $wpdb->prefix . 'groups';
    $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';

    // Delete group from the groups table
    $deleted = $wpdb->delete($table_name_groups, array('id' => $group_id), array('%d'));
    if ($deleted === false) {
        error_log('Failed to delete group from groups table.');
    }

    // Delete group taxonomy entry
    $deleted_taxonomy = $wpdb->delete($table_name_group_taxonomy, array('groups_id' => $group_id), array('%d'));
    if ($deleted_taxonomy === false) {
        error_log('Failed to delete group taxonomy entry.');
    }

    // Redirect to the group page
    wp_redirect(admin_url('admin.php?page=group_page'));
    exit;
}

// Hook to handle delete requests
add_action('admin_post_delete_group', 'delete_group');
add_action('admin_post_nopriv_delete_group', 'delete_group');
?>
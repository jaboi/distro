<?php
// Ensure that this file is being accessed through WordPress.
if (!defined('ABSPATH')) {
    exit;
}

function update_group() {
    global $wpdb;

    // Check if group_id is set to determine if this is an edit or an add
    if (!isset($_POST['group_id']) || !isset($_POST['group_name']) || !isset($_POST['group_type'])) {
        wp_redirect(admin_url('admin.php?page=group_page'));
        exit;
    }

    $group_id = intval($_POST['group_id']);
    $group_name = sanitize_text_field($_POST['group_name']);
    $description = sanitize_textarea_field($_POST['description']);
    $group_type = sanitize_text_field($_POST['group_type']);

    if ($group_id > 0) {
        // This is an update
        $wpdb->update(
            $wpdb->prefix . 'groups',
            array(
                'group_name' => $group_name,
                'description' => $description,
            ),
            array('id' => $group_id),
            array('%s', '%s'),
            array('%d')
        );

        // Update group taxonomy
        $wpdb->update(
            $wpdb->prefix . 'group_taxonomy',
            array('group_type' => $group_type),
            array('groups_id' => $group_id),
            array('%s'),
            array('%d')
        );
    } else {
        // This is an insert (this case shouldn't happen in edit mode, but for completeness)
        $wpdb->insert(
            $wpdb->prefix . 'groups',
            array(
                'group_name' => $group_name,
                'description' => $description,
            ),
            array('%s', '%s')
        );

        // Insert into group taxonomy
        $new_group_id = $wpdb->insert_id;
        $wpdb->insert(
            $wpdb->prefix . 'group_taxonomy',
            array(
                'groups_id' => $new_group_id,
                'group_type' => $group_type,
            ),
            array('%d', '%s')
        );
    }

    // Redirect back to the group page
    wp_redirect(admin_url('admin.php?page=group_page'));
    exit;
}

// Hook to handle form submission
add_action('admin_post_update_group', 'update_group');
add_action('admin_post_nopriv_update_group', 'update_group');

?>
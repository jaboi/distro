<?php
// Ensure this file is being loaded from within WordPress
if (!defined('ABSPATH')) {
    exit;
}

// Include WordPress' necessary files if not already included
require_once(ABSPATH . 'wp-admin/includes/post.php');

// Add your save_organization function here
function save_organization() {
    global $wpdb;

    // Verify nonce
    check_admin_referer('save_organization', 'organization_nonce');

    // Get the form data
    $name = sanitize_text_field($_POST['organization_name']);
    $address = sanitize_text_field($_POST['organization_address']);
    $org_groups = isset($_POST['org_groups']) ? array_map('intval', $_POST['org_groups']) : array();

    // Insert the new organization into the 'organizations' table
    $table_name_org = $wpdb->prefix . 'organizations';
    $wpdb->insert(
        $table_name_org,
        array(
            'name' => $name,
            'address' => $address,
        ),
        array('%s', '%s')
    );

    // Get the ID of the newly inserted organization
    $organization_id = $wpdb->insert_id;

    // Insert relationships into the group_relationships table
    $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';
    $table_name_group_relationships = $wpdb->prefix . 'group_relationships';

    foreach ($org_groups as $org_group_id) {
        // Retrieve the taxonomy ID for the organization group
        $group_taxonomy_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name_group_taxonomy WHERE groups_id = %d AND group_type = %s",
            $org_group_id, 'organization'
        ));

        // If the group taxonomy ID exists, insert a relationship into the group_relationships table
        if ($group_taxonomy_id) {
            $wpdb->insert(
                $table_name_group_relationships,
                array(
                    'object_id' => $organization_id,
                    'group_taxonomy_id' => $group_taxonomy_id
                ),
                array('%d', '%d')
            );
        }
    }

    // Redirect or show a success message
    wp_redirect(admin_url('admin.php?page=organization_page'));
    exit;
}
// Hook the function to the admin_post actions
add_action('admin_post_save_organization', 'save_organization');
add_action('admin_post_nopriv_save_organization', 'save_organization');
?>
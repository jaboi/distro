<?php
// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function edit_organization() {
    // Verify nonce
    if ( ! isset( $_POST['organization_nonce'] ) || ! wp_verify_nonce( $_POST['organization_nonce'], 'edit_organization' ) ) {
        wp_die( __( 'Invalid nonce', 'textdomain' ) );
    }

    // Check user permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have permission to perform this action', 'textdomain' ) );
    }

    global $wpdb;
    $table_name_org = $wpdb->prefix . 'organizations';
    $table_name_groups = $wpdb->prefix . 'groups';
    $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';
    $table_name_group_relationships = $wpdb->prefix . 'group_relationships';

    // Sanitize and prepare data
    $org_id = intval( $_POST['org_id'] );
    $name = sanitize_text_field( $_POST['organization_name'] );
    $address = sanitize_text_field( $_POST['organization_address'] );
    $org_group_ids = isset($_POST['org_group']) ? array_map('intval', $_POST['org_group']) : array();

    // Update the organization
    $updated = $wpdb->update(
        $table_name_org,
        array(
            'name' => $name,
            'address' => $address
        ),
        array( 'id' => $org_id ),
        array( '%s', '%s' ),
        array( '%d' )
    );

    if ( false === $updated ) {
        error_log( 'Failed to update organization: ' . $wpdb->last_error );
        wp_die( __( 'Failed to update organization.', 'textdomain' ) );
    }

    // Update the group_relationships table
    // First, delete all existing relationships for this organization
    $wpdb->delete(
        $table_name_group_relationships,
        array( 'object_id' => $org_id ),
        array( '%d' )
    );

    // Now, insert new relationships for the selected groups
    foreach ($org_group_ids as $group_id) {
        // Retrieve the taxonomy ID for the group
        $group_taxonomy_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name_group_taxonomy WHERE groups_id = %d AND group_type = %s",
            $group_id, 'organization'
        ));

        if ($group_taxonomy_id) {
            $wpdb->insert(
                $table_name_group_relationships,
                array(
                    'object_id' => $org_id,
                    'group_taxonomy_id' => $group_taxonomy_id
                ),
                array('%d', '%d')
            );
        }
    }

    // Redirect back to the organization page
    wp_redirect( admin_url( 'admin.php?page=organization_page' ) );
    exit;
}

// Hook to handle the form submission
add_action('admin_post_edit_organization', 'edit_organization');
?>
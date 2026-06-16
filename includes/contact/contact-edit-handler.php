<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function update_contact() {
    global $wpdb;
    $table_name_contact = $wpdb->prefix . 'contacts';
    $table_name_group_relationships = $wpdb->prefix . 'group_relationships';
    $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';

    // Sanitize and capture form data
    $contact_id = intval($_POST['contact_id']);
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $organization_id = intval($_POST['organization_id']);
    
    // Capture selected groups (array of group_taxonomy_ids)
    $selected_group_taxonomy_ids = isset($_POST['contact_groups']) ? array_map('intval', $_POST['contact_groups']) : array();

    // Update the contact in the database
    $wpdb->update(
        $table_name_contact,
        array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'organization_id' => $organization_id,
        ),
        array('id' => $contact_id)
    );

    // Remove all existing group relationships for the contact
    $wpdb->delete(
        $table_name_group_relationships,
        array('object_id' => $contact_id)
    );

    // Insert the new group relationships based on selected checkboxes
    if (!empty($selected_group_taxonomy_ids)) {
        foreach ($selected_group_taxonomy_ids as $group_taxonomy_id) {
            $wpdb->insert(
                $table_name_group_relationships,
                array(
                    'object_id' => $contact_id,
                    'group_taxonomy_id' => $group_taxonomy_id,
                ),
                array('%d', '%d')
            );
        }
    }

    // Redirect after updating
    wp_redirect(admin_url('admin.php?page=contact_page&message=contact_updated'));
    exit;
}

// Hook the function to the admin_post actions
add_action('admin_post_update_contact', 'update_contact');
add_action('admin_post_nopriv_update_contact', 'update_contact');
?>
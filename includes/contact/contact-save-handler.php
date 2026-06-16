<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function save_contact($selected_group_id = null) {
    global $wpdb;
    $table_name_contact = $wpdb->prefix . 'contacts';
    $table_name_group_relationships = $wpdb->prefix . 'group_relationships';
    $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';

    // Check if the method is triggered and that nonce is valid
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_contact_nonce']) && wp_verify_nonce($_POST['save_contact_nonce'], 'save_contact_action')) {
        error_log('save_contact triggered');

        // Sanitize and capture form data
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $organization_id = isset($_POST['organization_id']) && intval($_POST['organization_id']) > 0 ? intval($_POST['organization_id']) : null;  // Use null if no valid org selected
        $contact_groups = isset($_POST['contact_groups']) ? array_map('intval', $_POST['contact_groups']) : array(); // Capture multiple selected groups

        // Debug the captured data
        error_log('Name: ' . $name);
        error_log('Email: ' . $email);
        error_log('Phone: ' . $phone);
        error_log('Organization ID: ' . $organization_id);
        error_log('Contact Groups: ' . implode(',', $contact_groups));

        // Insert the contact data into the contacts table
        $result = $wpdb->insert($table_name_contact, array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'organization_id' => $organization_id
        ));

        // Check if the contact was saved successfully
        if ($result !== false) {
            $contact_id = $wpdb->insert_id; // Get the ID of the inserted contact
            error_log('Contact saved successfully. ID: ' . $contact_id);

            // Insert into group_relationships for each selected group
            foreach ($contact_groups as $contact_group_id) {
                // Get the group_taxonomy_id for the selected contact group
                $group_taxonomy_id = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM $table_name_group_taxonomy WHERE groups_id = %d",
                    $contact_group_id
                ));

                // Insert the relationship if a valid group_taxonomy_id is found
                if ($group_taxonomy_id) {
                    $wpdb->insert($table_name_group_relationships, array(
                        'object_id' => $contact_id,
                        'group_taxonomy_id' => $group_taxonomy_id
                    ));
                    error_log('Group relationship saved successfully.');
                } else {
                    error_log('Failed to find group_taxonomy_id.');
                }
            }
        } else {
            error_log('Failed to save contact. Error: ' . $wpdb->last_error);
        }
    } else {
        error_log('save_contact not triggered or nonce verification failed');
    }

    // Redirect back to the contacts page
    wp_redirect(admin_url('admin.php?page=contact_page&message=contact_added'));
    exit;
}



// Hook the function to the admin_post actions
add_action('admin_post_save_contact', 'save_contact');
add_action('admin_post_nopriv_save_contact', 'save_contact');
?>
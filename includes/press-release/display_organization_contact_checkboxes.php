<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

function display_organization_contact_checkboxes() {
    global $wpdb;

    // Fetch all organizations that have contacts
    $organizations = $wpdb->get_results("
        SELECT o.id AS org_id, o.name AS org_name, c.id AS contact_id, c.name AS contact_name, c.email AS contact_email
        FROM {$wpdb->prefix}organizations o
        INNER JOIN {$wpdb->prefix}contacts c ON o.id = c.organization_id
        ORDER BY o.name, c.name
    ", ARRAY_A);

    $current_org_id = 0;

    if (!empty($organizations)) {
        foreach ($organizations as $organization) {
            // Display organization name once
            if ($organization['org_id'] !== $current_org_id) {
                // Close previous organization div if needed
                if ($current_org_id !== 0) {
                    echo '</div>'; // Close contact list for the previous organization
                }
                
                // Display organization name
                echo '<div class="organization-group">';
                echo '<h4>' . esc_html($organization['org_name']) . '</h4>';
                $current_org_id = $organization['org_id'];
            }

            // Display each contact associated with the current organization
            echo '<label><input type="checkbox" checked name="contacts[]" value="' . esc_attr($organization['contact_email']) . '"> ' . esc_html($organization['contact_name']) . ' (' . esc_html($organization['contact_email']) . ')</label><br>';
        }
        // Close the last organization group div
        echo '</div>';
    } else {
        echo '<p>' . __('No organizations or contacts found.', 'textdomain') . '</p>';
    }
}
?>
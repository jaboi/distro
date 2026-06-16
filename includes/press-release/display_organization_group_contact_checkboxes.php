<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Displays checkboxes for organizations and their contacts, grouped by groups.
 */
function display_organization_group_contact_checkboxes() {
    global $wpdb;

    // Fetch contact organization groups with group taxonomy
    $contact_org_groups = $wpdb->get_results("
        SELECT
            gt.id AS group_taxonomy_id,
            g.id AS group_id, 
            g.group_name
        FROM {$wpdb->prefix}group_taxonomy gt
        INNER JOIN {$wpdb->prefix}groups g ON gt.groups_id = g.id
        ORDER BY g.group_name
    ", ARRAY_A);

    // Fetch organizations with their groups and contacts
    $organizations = $wpdb->get_results("
        SELECT g.group_name, g.id AS group_id, o.id AS org_id, o.name AS org_name, c.id AS contact_id, c.name AS contact_name, c.email AS contact_email
        FROM {$wpdb->prefix}organizations o
        INNER JOIN {$wpdb->prefix}contacts c ON o.id = c.organization_id
        INNER JOIN {$wpdb->prefix}group_relationships gr ON o.id = gr.object_id
        INNER JOIN {$wpdb->prefix}group_taxonomy gt ON gr.group_taxonomy_id = gt.id
        INNER JOIN {$wpdb->prefix}groups g ON gt.groups_id = g.id
        ORDER BY g.group_name, o.name, c.name
    ", ARRAY_A);

    // Initialize variables for tracking the current group and organization
    $current_group_name = '';
    $current_org_id = 0;

    // Dropdown for selecting groups
    echo '<select name="oug_group_select" id="oug_group_select">';
    echo '<option value="0">' . __('Select Group', 'textdomain') . '</option>'; // Option to show all groups
    foreach ($contact_org_groups as $group_org) {
        echo '<option value="' . esc_attr($group_org['group_taxonomy_id']) . '">' . esc_html(wp_unslash($group_org['group_name'])) . '</option>';
    }
    echo '</select>';

    if (!empty($organizations)) {
        foreach ($organizations as $organization) {
            // Display group name once for each group
            if ($organization['group_name'] !== $current_group_name) {
                // Close previous group and organization div if needed
                if ($current_group_name !== '') {
                    echo '</div></div>'; // Close organization and group divs
                }

                // Display new group with `data-group_id`
                echo '<div data-group_id="' . esc_attr($organization['group_id']) . '" class="group group-hide">';
                echo '<p class="this_group_name">' . esc_html($organization['group_name']) . '</p>';
                $current_group_name = $organization['group_name'];
                $current_org_id = 0; // Reset organization ID for the new group
            }

            // Check if organization has contacts, if not, skip rendering
            if (!empty($organization['contact_id'])) {
                // Display organization name once under each group
                if ($organization['org_id'] !== $current_org_id) {
                    // Close previous organization div if needed
                    if ($current_org_id !== 0) {
                        echo '</div>'; // Close contact list for the previous organization
                    }

                    // Display new organization
                    echo '<div class="organization-group">';
                    echo '<h4 style="margin-bottom: 0;margin-top: 5px;">' . esc_html($organization['org_name']) . '</h4>';
                    $current_org_id = $organization['org_id'];
                }

                // Display each contact associated with the current organization
                echo '<label><input type="checkbox" name="contacts[]" value="' . esc_attr($organization['contact_email']) . '"> ' . esc_html($organization['contact_name']) . ' (' . esc_html($organization['contact_email']) . ')</label><br>';
            }
        }

        // Close the last organization and group divs
        echo '</div></div>';
    } else {
        echo '<p>' . __('No organizations or contacts found.', 'textdomain') . '</p>';
    }
}
?>
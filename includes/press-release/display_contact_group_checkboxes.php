<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Displays checkboxes for organizations and their contacts, grouped by contact groups.
 */
function display_contact_group_checkboxes() {
    global $wpdb;

    // Fetch contact groups with their IDs
    $contact_groups_select = $wpdb->get_results("
        SELECT
            gt.id AS group_taxonomy_id,
            g.group_name
        FROM {$wpdb->prefix}group_taxonomy gt
        INNER JOIN {$wpdb->prefix}groups g ON gt.groups_id = g.id
        WHERE gt.group_type = 'contact'
        ORDER BY g.group_name
    ", ARRAY_A);

    // Fetch all contact groups with associated contacts
    $contact_groups = $wpdb->get_results("
        SELECT
            gt.id AS group_taxonomy_id,
            g.group_name,
            c.id AS contact_id,
            c.name AS contact_name,
            c.email AS contact_email,
            c.organization_id,
            o.name AS org_name
        FROM {$wpdb->prefix}group_taxonomy gt
        INNER JOIN {$wpdb->prefix}groups g ON gt.groups_id = g.id
        INNER JOIN {$wpdb->prefix}group_relationships gr ON gt.id = gr.group_taxonomy_id
        INNER JOIN {$wpdb->prefix}contacts c ON gr.object_id = c.id
        LEFT JOIN {$wpdb->prefix}organizations o ON c.organization_id = o.id
        WHERE gt.group_type = 'contact'
        ORDER BY g.group_name, IFNULL(o.name, ''), c.name
    ", ARRAY_A);

    // Display the select dropdown for contact groups
    echo '<select name="contact_group_opt" id="contact_group_opt">';
    echo '<option value="0">Select Group</option>'; // Option to show all groups
    foreach ($contact_groups_select as $group_opt) {
        echo '<option value="' . esc_attr($group_opt['group_taxonomy_id']) . '">' . esc_html(wp_unslash($group_opt['group_name'])) . '</option>';
    }
    echo '</select>';

    // Group the contacts by organization for each contact group
    if (!empty($contact_groups)) {
        $current_group_name = '';
        $current_org_name = '';
        $first_group = true;

        foreach ($contact_groups as $group) {
            // Display the contact group name if it's a new group
            if ($group['group_name'] !== $current_group_name) {
                // Close the previous contact group's section if necessary
                if (!$first_group) {
                    echo '</div>'; // Close the previous organization group if open
                    echo '</div>'; // Close the previous contact group section
                }
                $first_group = false;

                // Display the new contact group name
                echo '<div data-group_contact_id="' . esc_html($group['group_taxonomy_id']) . '" class="contact-group group-hide">';
                echo '<p class="this_group_name">' . esc_html(wp_unslash($group['group_name'])) . '</p>';
                $current_group_name = $group['group_name'];
                $current_org_name = '';
            }

            // Check if we need to start a new organization group
            $org_name = $group['org_name'] ? $group['org_name'] : __('No Organization', 'textdomain');
            if ($org_name !== $current_org_name) {
                // Close the previous organization group if it exists
                if ($current_org_name !== '') {
                    echo '</div>';
                }
                
                // Start a new organization group
                echo '<div class="organization-group">';
                echo '<h4>' . esc_html($org_name) . '</h4>';
                $current_org_name = $org_name;
            }

            // Display the contact
            echo '<label><input type="checkbox" name="contacts[]" value="' . esc_attr($group['contact_email']) . '"> ' . 
                 esc_html($group['contact_name']) . ' (' . esc_html($group['contact_email']) . ')</label><br>';
        }

        // Close the last organization and contact group sections
        if (!$first_group) {
            echo '</div>'; // Close the last organization group
            echo '</div>'; // Close the last contact group section
        }
    } else {
        echo '<p>' . __('No contact groups, contacts, or organizations found.', 'textdomain') . '</p>';
    }
}
?>
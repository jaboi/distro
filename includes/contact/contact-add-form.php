<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function render_add_new_contact_page($contact = null) { // Initialize $contact to null by default
    global $wpdb;
    $table_name_contact = $wpdb->prefix . 'contacts';
    $contact_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name_contact");
    $contact_limit = 25;

    if ($contact_count >= $contact_limit) {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p>' . __( 'You have reached the limit of 25 contacts. You cannot add more contacts.', 'textdomain' ) . '</p>';
        echo '</div>';
        return;
    }

    $table_name_org = $wpdb->prefix . 'organizations';
    $table_name_groups = $wpdb->prefix . 'groups';
    $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';
    $table_name_group_relationships = $wpdb->prefix . 'group_relationships';

    // Fetch organizations for the dropdown
    $organizations = $wpdb->get_results("SELECT id, name FROM $table_name_org", ARRAY_A);

    // Fetch groups with group_type 'contact'
    $contact_groups_query = "
        SELECT g.id, g.group_name 
        FROM $table_name_groups g
        INNER JOIN $table_name_group_taxonomy t ON g.id = t.groups_id
        WHERE t.group_type = %s
    ";
    $contact_groups = $wpdb->get_results($wpdb->prepare($contact_groups_query, 'contact'), ARRAY_A);

    // Fetch selected organization and groups if editing
    $selected_org_id = $contact ? intval($contact['organization_id']) : '';
    
    // Initialize an array to hold selected group IDs
    $selected_group_ids = array();

    if ($contact) {
        // Assuming group relationships are stored in group_relationships table
        $selected_group_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT tax.groups_id 
             FROM $table_name_group_relationships rel
             INNER JOIN $table_name_group_taxonomy tax ON rel.group_taxonomy_id = tax.id
             WHERE rel.object_id = %d",
            $contact['id']
        ));
    }

    if (isset($_GET['org_id'])) {
        // Get org_id from URL if available
        $selected_org_id = intval($_GET['org_id']);
        $organization_name = '';

        // Find the organization name based on the selected org_id
        if ($selected_org_id) {
            foreach ($organizations as $org) {
                if ($org['id'] == $selected_org_id) {
                    $organization_name = $org['name'];
                    break;
                }
            }
        }
    }

    echo '<div class="wrap">';
    echo '<div class="data_card-list">';
    echo '<div class="card-header card-header-primary">';
    if (isset($_GET['org_id']) && $selected_org_id && isset($organization_name) && $organization_name) {
        echo '<div><h4 class="card-title">Add New Contact for ' . esc_html($organization_name) . '</h4>';
    } else {
        echo '<div><h4 class="card-title">Add New Contact</h4>';
    }

    echo '<p class="card-category">Add a new contact to your organization.</p></div>';
    echo '<a class="btn btn-success" href="' . esc_url(admin_url('admin.php?page=contact_page')) . '">' . __('View List', 'textdomain') . '</a>';
    echo '</div>';

    echo '<form id="save_contact" class="simple_form" method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    wp_nonce_field('save_contact_action', 'save_contact_nonce');
    echo '<input type="hidden" name="action" value="save_contact">';

    // Contact Name
    echo '<label for="name">' . __('Name', 'textdomain') . '</label>';
    echo '<input type="text" id="name" name="name" value="' . ($contact ? esc_attr($contact['name']) : '') . '" required>';
    echo '<br>';

    // Contact Email
    echo '<label for="email">' . __('Email', 'textdomain') . '</label>';
    echo '<input type="email" id="email" name="email" value="' . ($contact ? esc_attr($contact['email']) : '') . '" required>';
    echo '<br>';

    // Contact Phone
    echo '<label for="phone">' . __('Phone', 'textdomain') . '</label>';
    echo '<input type="text" id="phone" name="phone" value="' . ($contact ? esc_attr($contact['phone']) : '') . '">';
    echo '<br>';

    // Organization Dropdown
    echo '<label for="organization_id">' . __('Organization', 'textdomain') . '</label>';
    echo '<select id="organization_id" name="organization_id" style="width: 100%;">';
    echo '<option value="">' . __('Select an Organization', 'textdomain') . '</option>';
    foreach ($organizations as $org) {
        $selected = ($org['id'] == $selected_org_id) ? 'selected' : '';
        echo '<option value="' . esc_attr($org['id']) . '" ' . $selected . '>' . esc_html($org['name']) . '</option>';
    }
    echo '</select>';
    echo '<br>';

    // Contact Group Checkboxes
    echo '<label>' . __('Contact Groups:', 'textdomain') . '</label>';
    ?>
    <div class="org_list_box" id="contact_group" name="contact_group">
        <?php
        if (!empty($contact_groups)) {
            foreach ($contact_groups as $group) {
                $checked = (in_array($group['id'], $selected_group_ids)) ? 'checked' : '';
                echo '<label>';
                echo '<input type="checkbox" name="contact_groups[]" value="' . esc_attr($group['id']) . '" ' . $checked . '>';
                echo esc_html(wp_unslash($group['group_name']));
                echo '</label>';
            }
        } else {
            echo '<p>' . __('No contact groups available.', 'textdomain') . '</p>';
        } ?>
    </div>
    <?php
    echo '<br>';

    // Submit Button
    echo '<input class="btn btn-primary" type="submit" value="' . __('Save Contact', 'textdomain') . '">';
    echo '</form>';

    echo '</div>';
    echo '</div>';
}
?>
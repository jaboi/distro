<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function render_contact_edit_form($contact_id) {
    global $wpdb;
    $table_name_contact = $wpdb->prefix . 'contacts';
    $table_name_org = $wpdb->prefix . 'organizations';
    $table_name_group_relationships = $wpdb->prefix . 'group_relationships';
    $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';
    $table_name_groups = $wpdb->prefix . 'groups';

    // Fetch contact data
    $contact = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name_contact WHERE id = %d", $contact_id), ARRAY_A);
    
    // Fetch all organizations
    $organizations = $wpdb->get_results("SELECT id, name FROM $table_name_org", ARRAY_A);

    // Fetch all contact groups
    $contact_groups = $wpdb->get_results("
        SELECT g.id, g.group_name 
        FROM $table_name_groups g
        JOIN $table_name_group_taxonomy t ON g.id = t.groups_id 
        WHERE t.group_type = 'contact'", 
        ARRAY_A
    );

    // Fetch the selected group_taxonomy_ids for the contact
    $selected_group_taxonomy_ids = $wpdb->get_col($wpdb->prepare("
        SELECT group_taxonomy_id 
        FROM $table_name_group_relationships 
        WHERE object_id = %d", 
        $contact_id
    ));

    if (!$contact) {
        echo '<p>' . __('Contact not found.', 'textdomain') . '</p>';
        return;
    }

    echo '<div class="data_card-list active_tab_content">';
    /*echo '<div class="card-header card-header-primary">';
    echo '<div><h4 class="card-title ">Edit Contact</h4>';
    echo '<p class="card-category">Edit contact</p></div>';
    echo '</div>';*/

    echo '<form method="post" class="simple_form" action="' . esc_url(admin_url('admin-post.php')) . '">';
    echo '<h3>Edit Contact ' . esc_attr($contact['name']) . '</h3>';
    echo '<input type="hidden" name="action" value="update_contact">';
    echo '<input type="hidden" name="contact_id" value="' . esc_attr($contact['id']) . '">';

    echo '<label for="name">' . __('Name', 'textdomain') . '</label>';
    echo '<input type="text" name="name" value="' . esc_attr($contact['name']) . '">';
    echo '<br>';

    echo '<label for="email">' . __('Email', 'textdomain') . '</label>';
    echo '<input type="email" name="email" value="' . esc_attr($contact['email']) . '">';
    echo '<br>';

    echo '<label for="phone">' . __('Phone', 'textdomain') . '</label>';
    echo '<input type="text" name="phone" value="' . esc_attr($contact['phone']) . '">';
    echo '<br>';

    // Organization dropdown
    echo '<label for="organization_id">' . __('Organization', 'textdomain') . '</label>';
    echo '<select name="organization_id" id="edit_contact_info">';
    echo '<option value="">' . __('Select an Organization', 'textdomain') . '</option>';
    foreach ($organizations as $org) {
        $selected = ($contact['organization_id'] == $org['id']) ? 'selected' : '';
        echo '<option value="' . esc_attr($org['id']) . '" ' . $selected . '>' . esc_html($org['name']) . '</option>';
    }
    echo '</select>';
    echo '<br>';

    // Contact Group Checkboxes
    echo '<label for="contact_group">' . __('Contact Groups', 'textdomain') . '</label>';
    echo '<div class="org_list_box" name="contact_group" id="contact_group">';
    foreach ($contact_groups as $group) {
        // Get group_taxonomy_id for the current group
        $group_taxonomy_id = $wpdb->get_var($wpdb->prepare("
            SELECT id 
            FROM $table_name_group_taxonomy 
            WHERE groups_id = %d AND group_type = 'contact'", 
            $group['id']
        ));

        // Check if this group is selected
        $checked = in_array($group_taxonomy_id, $selected_group_taxonomy_ids) ? 'checked' : '';
        
        // Output checkbox
        echo '<label>';
        echo '<input type="checkbox" name="contact_groups[]" value="' . esc_attr($group_taxonomy_id) . '" ' . $checked . '>';
        echo esc_html(wp_unslash($group['group_name']));
        echo '</label>';
    }
    
    echo '</div>';
    echo '<br>';
    ?>
    <div class="btn-container">
        <a href="<?php echo esc_url(admin_url('admin.php?page=contact_page')); ?>" type="button" class="btn btn-danger text-center">Cancel</a>
        <input class="btn btn-primary" type="submit" value="Update Contact">
    </div>
    <?php
    echo '';
    echo '</form>';
    echo '</div>';
    echo '</div>';
}
?>
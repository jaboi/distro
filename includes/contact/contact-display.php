<?php
// Ensure that this file is being accessed through WordPress.
if (!defined('ABSPATH')) {
    exit;
}

function render_contacts_page() {
    global $wpdb;
    $table_name_contact = $wpdb->prefix . 'contacts';
    $table_name_org = $wpdb->prefix . 'organizations';
    $table_name_group_relationships = $wpdb->prefix . 'group_relationships';
    $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';
    $table_name_groups = $wpdb->prefix . 'groups';

    // Query to count total organizations
    $total_orgs = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name_contact" );
    
    $contact_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name_contact");
    $contact_limit = 25;

    // Check if a contact_id or contact_group_id is set and valid
    $contact_id = isset($_GET['contact_id']) ? intval($_GET['contact_id']) : 0;
    $view_contact_id = isset($_GET['view_contact_id']) ? intval($_GET['view_contact_id']) : 0;
    $contact_group_id = isset($_GET['contact_group_id']) ? intval($_GET['contact_group_id']) : 0;

    // add new contact code start 
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

    $contact = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name_contact WHERE id = %d", $contact_id), ARRAY_A);
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
    // add new contact code end 

    if ($contact_count >= $contact_limit) {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p>' . __( 'You have reached the limit of 25 contacts. You cannot add more contacts.', 'textdomain' ) . '</p>';
        echo '</div>';
    }
    ?>
    <div class="wrap">
        <h2>Contacts</h2> 

        <?php
        $editing_mode = "";
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['contact_id'])) {
            $editing_mode = "editing_on";
        }
        ?>

        <div class="press_release_tabs <?php echo $editing_mode; ?>"> 
            <a href="#all_contacts" class="btn active_tab"> Contacts <span><?php echo esc_html($total_orgs); ?></span> </a>
            <a href="#add_new_contacts" class="btn add_btn"> Add New Contact &nbsp; <i class="fa fa-plus"></i> </a>
        </div>

         <div class="data-card_title" data-tab_name="all_contacts">
            <?php
                if (isset($_GET['org_id'])) {
                    $organization = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name_org WHERE id = %d", $org_id), ARRAY_A);
                    echo '<h4 class="card-title ">Contact List for ' . esc_html($organization['name']) . '</h4>';
                } elseif ($view_contact_id) {
                    echo '<h4 class="card-title ">View Contact ID: ' . esc_html($view_contact_id) . '</h4>';
                } elseif ($contact_group_id) {
                    $group = $wpdb->get_row($wpdb->prepare("SELECT group_name FROM $table_name_groups WHERE id = %d", $contact_group_id), ARRAY_A);
                    echo '<h4 class="card-title ">Directory of Media Contacts for ' . esc_html(wp_unslash($group['group_name'])) . '</h4>';
                } else {
                    echo '<h4 class="card-title ">Directory of Media Contacts</h4>';
                }

                if (!isset($_GET['org_id'])) {
                    //echo '<a class="btn btn-success" href="' . esc_url(admin_url('admin.php?page=add_new_contact')) . '">' . __( 'Add New', 'textdomain' ) . '</a>';
                } else {
                    echo '<a class="btn btn-success" href="' . esc_url(admin_url('admin.php?page=add_new_contact')) . '&org_id=' . $org_id . '">' . __( 'Add New', 'textdomain' ) . '</a>';
                }
                ?>
        </div>

        <div class="data-card_title" data-tab_name="add_new_contacts" style="display:none;">
            <h4 class="card-title ">Add New Contact</h4>
        </div>

        <?php
        if ($contact_id) {
            // Ensure the contact-edit-form.php file is included
            require_once plugin_dir_path(__FILE__) . 'contact-edit-form.php';
            
            // Call the render_contact_edit_form function
            render_contact_edit_form($contact_id);
        } else {
            //echo '<p>' . __('Invalid Contact ID.', 'textdomain') . '</p>';
        }
        
        

        if (!$contact_id) {
        ?>
    
        <?php
        global $wpdb;

        // Check if org_id, view_contact_id, or contact_group_id is provided
        /*$query = "
            SELECT c.*, o.name AS organization_name, GROUP_CONCAT(g.group_name SEPARATOR ', ') AS group_names
            FROM {$wpdb->prefix}contacts c
            LEFT JOIN {$wpdb->prefix}organizations o ON c.organization_id = o.id
            LEFT JOIN $table_name_group_relationships gr ON c.id = gr.object_id
            LEFT JOIN $table_name_group_taxonomy gt ON gr.group_taxonomy_id = gt.id
            LEFT JOIN $table_name_groups g ON gt.groups_id = g.id
            WHERE 1=1
        ";*/

        $query = "
            SELECT c.*, o.name AS organization_name, 
                   GROUP_CONCAT(CONCAT(g.group_name, ':', g.id) SEPARATOR ', ') AS group_names
            FROM {$wpdb->prefix}contacts c
            LEFT JOIN {$wpdb->prefix}organizations o ON c.organization_id = o.id
            LEFT JOIN $table_name_group_relationships gr ON c.id = gr.object_id
            LEFT JOIN $table_name_group_taxonomy gt ON gr.group_taxonomy_id = gt.id
            LEFT JOIN $table_name_groups g ON gt.groups_id = g.id
            WHERE 1=1
        ";

        // If organization id is set
        if (isset($_GET['org_id'])) {
            $org_id = intval($_GET['org_id']);
            $query .= $wpdb->prepare(" AND o.id = %d", $org_id);
        }

        // If specific contact is viewed by view_contact_id
        if ($view_contact_id) {
            $query .= $wpdb->prepare(" AND c.id = %d", $view_contact_id);
        }

        // If specific group is set
        if ($contact_group_id) {
            $query .= $wpdb->prepare(" AND g.id = %d", $contact_group_id);
        }

        $query .= " GROUP BY c.id";

        // Fetch the contacts based on the filters
        $contacts = $wpdb->get_results($query, ARRAY_A);
        ?>
        

        
        <?php

        echo '<div class="data_card-list" id="add_new_contacts">';

        
    /*echo '<div class="card-header card-header-primary">';
    if (isset($_GET['org_id']) && $selected_org_id && isset($organization_name) && $organization_name) {
        echo '<div><h4 class="card-title">Add New Contact for ' . esc_html($organization_name) . '</h4>';
    } else {
        echo '<div><h4 class="card-title">Add New Contact</h4>';
    }

    echo '<p class="card-category">Add a new contact to your organization.</p></div>';
    //echo '<a class="btn btn-success" href="' . esc_url(admin_url('admin.php?page=contact_page')) . '">' . __('View List', 'textdomain') . '</a>';
    echo '</div>';*/

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

    echo '<div id="csv_upload">';
    require_once plugin_dir_path(__FILE__) . 'contact-csv-upload.php';
        contact_csv_upload();
    echo '</div>';

    echo '</div>';

        echo '<div class="data_card-list active_tab_content" id="all_contacts">';
        /*echo '<div class="card-header card-header-primary">';
        if (isset($_GET['org_id'])) {
            $organization = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name_org WHERE id = %d", $org_id), ARRAY_A);
            echo '<div><h4 class="card-title ">Contact List for ' . esc_html($organization['name']) . '</h4>';
        } elseif ($view_contact_id) {
            echo '<div><h4 class="card-title ">View Contact ID: ' . esc_html($view_contact_id) . '</h4>';
        } elseif ($contact_group_id) {
            $group = $wpdb->get_row($wpdb->prepare("SELECT group_name FROM $table_name_groups WHERE id = %d", $contact_group_id), ARRAY_A);
            echo '<div><h4 class="card-title ">Directory of Media Contacts for ' . esc_html($group['group_name']) . '</h4>';
        } else {
            echo '<div><h4 class="card-title ">Directory of Media Contacts</h4>';
        }
        echo '<p class="card-category">Find and edit media contact</p></div>';
        if (!isset($_GET['org_id'])) {
            //echo '<a class="btn btn-success" href="' . esc_url(admin_url('admin.php?page=add_new_contact')) . '">' . __( 'Add New', 'textdomain' ) . '</a>';
        } else {
            echo '<a class="btn btn-success" href="' . esc_url(admin_url('admin.php?page=add_new_contact')) . '&org_id=' . $org_id . '">' . __( 'Add New', 'textdomain' ) . '</a>';
        }
        echo '</div>';*/

        if (empty($contacts)) {
            echo '<p>' . __('No contacts found.', 'textdomain') . '</p>';
            return;
        }

        echo '<table id="contacts-table" class="wp-list-table fixed striped">';
        echo '<thead><tr><th>' . __('Name', 'textdomain') . '</th><th>' . __('Organization', 'textdomain') . '</th><th>' . __('Email', 'textdomain') . '</th><th>' . __('Phone', 'textdomain') . '</th><th>' . __('Groups', 'textdomain') . '</th><th>' . __('Actions', 'textdomain') . '</th></tr></thead>';
        echo '<tbody>';

        foreach ($contacts as $contact) {
            echo '<tr>';
            echo '<td>' . esc_html($contact['name']) . '</td>';
            echo '<td>' . esc_html($contact['organization_name']) . '</td>';
            echo '<td style="line-break: anywhere;">' . esc_html($contact['email']) . '</td>';
            echo '<td>' . esc_html($contact['phone']) . '</td>'; ?>

            <td class="group_list_col"><?php 
            //echo esc_html($contact['group_names']);
            $groups = explode(', ', wp_unslash($contact['group_names']));  // Split group names by ", "
            if (!empty($groups)) {
                $group_output = [];
                foreach ($groups as $group) {
                    // Skip if group doesn't contain the expected delimiter
                    if (strpos($group, ':') === false) {
                        continue;
                    }
                    
                    list($group_name, $group_id) = explode(':', $group, 2);  // Limit to 2 parts for safety
                    
                    // Generate the URL for the group
                    $group_link = esc_url(add_query_arg([
                        'page' => 'contact_page',
                        'contact_group_id' => $group_id
                    ], admin_url('admin.php')));
                    
                    // Store formatted group link
                    $group_output[] = '<a href="' . $group_link . '">' . esc_html($group_name) . '</a>';
                }
                
                // Output all groups separated by commas
                echo implode(', ', $group_output);
            } else {
                echo '<span class="no-group">NO GROUP</span>';
            }
            

        ?></td>

            <?php
            echo '<td><a href="' . esc_url(add_query_arg(array('page' => 'contact_page', 'action' => 'edit', 'contact_id' => $contact['id']), admin_url('admin.php'))) . '">' . __('Edit', 'textdomain') . '</a> | <a href="' . wp_nonce_url(admin_url('admin-post.php?action=delete_contact&contact_id=' . $contact['id']), 'delete_contact') . '">' . __('Delete', 'textdomain') . '</a></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        echo '<div class="table_footer">';
        if (isset($_GET['org_id'])) {
            echo '<a class="btn btn-primary" href="' . esc_url(admin_url('admin.php?page=organization_page')) . '">View All Organizations</a>';
            echo '<a class="btn btn-primary" href="' . esc_url(admin_url('admin.php?page=contact_page')) . '">View All Contacts</a>';
        }
        echo '</div>';
        echo '</div>';
        ?>
    </div>
    <?php
    }
}
?>
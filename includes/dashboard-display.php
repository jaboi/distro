<?php
// Ensure that this file is being accessed through WordPress.
if (!defined('ABSPATH')) {
    exit;
}

function render_manager_page() {
    echo '<div class="wrap"><h1>' . __( 'Organization and Contacts Manager', 'textdomain' ) . '</h1>';
    echo '</div>';
    echo '<div class="wrap"><div id="dashboard-opts">';

    // Organization Section
    echo '<div class="dashboard-opt organization">';
    echo '<div class="dashboard-opt-title">';
    echo '<i><svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24" viewBox="0 0 24 24" width="24"><rect fill="none" height="24" width="24"/><path d="M12,7V3H2v18h20V7H12z M10,19H4v-2h6V19z M10,15H4v-2h6V15z M10,11H4V9h6V11z M10,7H4V5h6V7z M20,19h-8V9h8V19z M18,11h-4v2 h4V11z M18,15h-4v2h4V15z"/></svg></i>';
    echo '<h3>' . __( 'Media Organizations', 'textdomain' ) . '</h3>';
    echo '</div>';
    echo '<div class="dashboard-opt-content">';
    // Link to the organization page with "add new" action
    echo '<a class="btn btn-primary" href="' . esc_url( admin_url( 'admin.php?page=organization_page&action=add_new' ) ) . '">' . __( 'Add New', 'textdomain' ) . '</a>';
    //echo $this->render_organization_dropdown();
    global $wpdb;

    // Define default values for the variables
    $selected_org_id = isset($_POST['organization_id']) ? intval($_POST['organization_id']) : ''; // or set to null if desired
    $selected_group_id = isset($_POST['org_group']) ? intval($_POST['org_group']) : ''; // or set to null

        // Define table names
        $table_name_groups = $wpdb->prefix . 'groups';
        $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';

        // Query to get groups with type 'organization'
        $query = $wpdb->prepare(
            "SELECT g.id, g.group_name 
            FROM $table_name_groups g
            INNER JOIN $table_name_group_taxonomy t ON g.id = t.groups_id
            WHERE t.group_type = %s",
            'organization'
        );
        $org_groups = $wpdb->get_results($query, ARRAY_A);

        $table_name_org = $wpdb->prefix . 'organizations';
        $organizations = $wpdb->get_results("SELECT id, name FROM $table_name_org", ARRAY_A);

        echo '<select id="organization_id" name="organization_id" style="width: 100%;">';
        echo '<option value="">' . __('Select Organization', 'textdomain') . '</option>';
        foreach ($organizations as $org) {
            $selected = ($org['id'] == $selected_org_id) ? 'selected' : '';
            echo '<option value="' . esc_attr($org['id']) . '" ' . $selected . '>' . esc_html($org['name']) . '</option>';
        }
        echo '</select>';


        // Render the dropdown
        echo '<select id="dashboardorg_group" name="org_group" class="regular-text">';
        echo '<option value="">' . __('Select a Group', 'textdomain') . '</option>';
        foreach ($org_groups as $group) {
            $selected = ($group['id'] == $selected_group_id) ? 'selected' : '';
            echo '<option value="' . esc_attr($group['id']) . '" ' . $selected . '>' . esc_html($group['group_name']) . '</option>';
        }
        echo '</select>';
    echo '<a data-btn_link="' . esc_url( admin_url( 'admin.php?page=organization_page' ) ) . '" href="#" data-selected_org="" data-selected_org_grp="" class="dashboard-search btn btn-primary btn-small white-svg"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg></a>';
    echo '<div></div>';
    echo '</div>';
    echo '</div>';

    // Contacts Section
    echo '<div class="dashboard-opt contact">';
    echo '<div class="dashboard-opt-title">';
    echo '<i><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm6 12H6v-1c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1z"/></svg></i>';
    echo '<h3>' . __( 'Media Contacts', 'textdomain' ) . '</h3>';
    echo '</div>';
    echo '<div class="dashboard-opt-content ">';
    // Link to the contact page with "add new" action
    echo '<a class="btn btn-primary" href="' . esc_url( admin_url( 'admin.php?page=contact_page&action=add_new' ) ) . '">' . __( 'Add New', 'textdomain' ) . '</a>';
    //echo $this->render_contact_view_dropdown();
    global $wpdb;
        $table_name_contact = $wpdb->prefix . 'contacts';
        $contacts = $wpdb->get_results("SELECT id, name, email FROM $table_name_contact");
        //$contacts = $wpdb->get_results( "SELECT email FROM $table_name_contact", ARRAY_A );

        echo '<select id="view_contact_email" name="contact_email" style="width: 100%;">';
        echo '<option value="">Select a Contact</option>';
        foreach ($contacts as $conts) {
            echo '<option value="' . esc_attr($conts->id) . '">' . esc_html($conts->name) . '</option>';
        }
        echo '</select>';

    $table_name_groups = $wpdb->prefix . 'groups';
    $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';
    $query = $wpdb->prepare(
        "SELECT g.id, g.group_name 
        FROM $table_name_groups g
        INNER JOIN $table_name_group_taxonomy t ON g.id = t.groups_id
        WHERE t.group_type = %s",
        'contact'
    );
    $org_groups = $wpdb->get_results($query, ARRAY_A);
    echo '<select id="contact_groups">';
    echo '<option value="">Select a Group</option>';
    foreach ($org_groups as $group) {
        echo '<option value="' . esc_attr($group['id']) . '">' . esc_html(wp_unslash($group['group_name'])) . '</option>';
    }
    echo '</select>';

    echo '<a data-btn_link="' . esc_url( admin_url( 'admin.php?page=contact_page' ) ) . '" href="" data-selected_contact="" data-selected_contact_grp="" class="dashboard-search btn btn-primary btn-small white-svg"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg></a>';
    echo '<div></div>';
    echo '</div>';
    echo '</div>';

    // Other sections (if any)
    echo '<div class="dashboard-opt undeliverables">';
    echo '<div class="dashboard-opt-title">';
    echo '<i><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg></i>';
    echo '<h3>' . __( 'Submit Undeliverables', 'textdomain' ) . '</h3>';
    echo '</div>';
    echo '<div class="dashboard-opt-content">';
    echo '</div>';
    echo '</div>';

    echo '<div class="dashboard-opt view-contact-list">';
    echo '<div class="dashboard-opt-title">';
    echo '<i><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0z" fill="none"/><path d="M20 0H4v2h16V0zM4 24h16v-2H4v2zM20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-8 2.75c1.24 0 2.25 1.01 2.25 2.25s-1.01 2.25-2.25 2.25S9.75 10.24 9.75 9 10.76 6.75 12 6.75zM17 17H7v-1.5c0-1.67 3.33-2.5 5-2.5s5 .83 5 2.5V17z"/></svg></i>';
    echo '<h3>' . __( 'View My Update List', 'textdomain' ) . '</h3>';
    echo '</div>';
    echo '<div class="dashboard-opt-content">';
    echo '</div>';
    echo '</div>';

    echo '</div>';

    echo '</div>'; // End of dashboard-opts
}
?>
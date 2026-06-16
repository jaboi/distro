<?php
// Ensure that this file is being accessed through WordPress.
if (!defined('ABSPATH')) {
    exit;
}

function render_organizations_page() {
    global $wpdb;
    $table_name_org = $wpdb->prefix . 'organizations';
    $table_name_groups = $wpdb->prefix . 'groups';
    $table_name_group_relationships = $wpdb->prefix . 'group_relationships';
    $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';

    // Query to count total organizations
    $total_orgs = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name_org" );

    // Check if view_org_id or org_group_id is provided in the URL
    $view_org_id = isset($_GET['view_org_id']) ? intval($_GET['view_org_id']) : 0;
    $org_group_id = isset($_GET['org_group_id']) ? intval($_GET['org_group_id']) : 0;

    // SQL query to fetch organizations with group names
    /*$sql = "
        SELECT org.id, org.name, org.address, GROUP_CONCAT(grp.group_name SEPARATOR ', ') AS group_names
        FROM $table_name_org AS org
        LEFT JOIN $table_name_group_relationships AS rel ON org.id = rel.object_id
        LEFT JOIN $table_name_group_taxonomy AS tax ON rel.group_taxonomy_id = tax.id
        LEFT JOIN $table_name_groups AS grp ON tax.groups_id = grp.id
        WHERE 1=1
    ";*/
    $sql = "
        SELECT org.id, org.name, org.address, 
               GROUP_CONCAT(CONCAT(grp.group_name, ':', grp.id) SEPARATOR ', ') AS group_names
        FROM $table_name_org AS org
        LEFT JOIN $table_name_group_relationships AS rel ON org.id = rel.object_id
        LEFT JOIN $table_name_group_taxonomy AS tax ON rel.group_taxonomy_id = tax.id
        LEFT JOIN $table_name_groups AS grp ON tax.groups_id = grp.id
        WHERE 1=1
    ";

    // If an org_id is provided, filter by organization
    if ($view_org_id) {
        $sql .= $wpdb->prepare(" AND org.id = %d", $view_org_id);
    }

    // If an org_group_id is provided, filter by group
    if ($org_group_id) {
        $sql .= $wpdb->prepare(" AND grp.id = %d", $org_group_id);
    }

    $sql .= " GROUP BY org.id, org.name, org.address";

    // Fetch organizations from the database
    $organizations = $wpdb->get_results($sql, ARRAY_A);

    ?>
    <div class="wrap">
        <h2>Organiazations</h2>
        <?php
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['org_id'])) {
            $editing_mode = "editing_on";
        }
        ?>
        <div class="press_release_tabs <?php echo $editing_mode; ?>"> 
            <a href="#all_orgs" class="btn active_tab"> Organizations <span><?php echo esc_html($total_orgs); ?></span> </a>
            <a href="#add_new_orgs" class="btn add_btn"> Add New Organization &nbsp; <i class="fa fa-plus"></i> </a>
        </div>

        <div class="data-card_title" data-tab_name="all_orgs">
            <?php 
            if ($org_group_id) {
                $group = $wpdb->get_row($wpdb->prepare("SELECT group_name FROM $table_name_groups WHERE id = %d", $org_group_id), ARRAY_A);
                echo '<h4 class="card-title ">Directory of Media Organizations for ' . esc_html($group['group_name']) . '</h4>';
            } else {
                echo '<h4 class="card-title">Directory of Media Organizations</h4>';
            }
            ?>
        </div>

        <div class="data-card_title" data-tab_name="add_new_orgs" style="display:none;">
            <h4 class="card-title ">Add New Organization</h4>
        </div>
    <?php

    // edit start
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['org_id'])) {
        // Include the file containing the function
        require_once plugin_dir_path(__FILE__) . 'organization-edit-form.php';

        // Call the function to render the edit form
        if (function_exists('render_organization_edit_form')) {
            render_organization_edit_form(intval($_GET['org_id']));
        } else {
            echo '<div class="error"><p>' . __('Edit form function not found.', 'textdomain') . '</p></div>';
        }
        return;

    
            global $wpdb;
            $table_name_org = $wpdb->prefix . 'organizations';
            $table_name_groups = $wpdb->prefix . 'groups';
            $table_name_group_relationships = $wpdb->prefix . 'group_relationships';
            $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';

            // Fetch organizations with their group names
            $organizations = $wpdb->get_results("
                SELECT org.id, org.name, org.address, GROUP_CONCAT(grp.group_name SEPARATOR ', ') AS group_names
                FROM $table_name_org AS org
                LEFT JOIN $table_name_group_relationships AS rel ON org.id = rel.object_id
                LEFT JOIN $table_name_group_taxonomy AS tax ON rel.group_taxonomy_id = tax.id
                LEFT JOIN $table_name_groups AS grp ON tax.groups_id = grp.id
                GROUP BY org.id, org.name, org.address
            ", ARRAY_A);
    }
    // edit end

    ?>
    


        <div class="data_card-list " id="add_new_orgs">
            <!-- <div class="card-header card-header-primary">
                <div>
                    <h4 class="card-title ">Add New Organization</h4>
                    <p class="card-category">Some awesome text here</p>
                </div>
                <a class="btn btn-success" href="<?php echo esc_url(admin_url('admin.php?page=organization_page')); ?>"><?php __('View List', 'textdomain'); ?></a>
            </div> -->

            <?php
            // Query to get the groups
            $query = $wpdb->prepare(
                "SELECT g.id, g.group_name 
                FROM $table_name_groups g
                INNER JOIN $table_name_group_taxonomy t ON g.id = t.groups_id
                WHERE t.group_type = %s",
                'organization'
            );
            $org_groups = $wpdb->get_results($query, ARRAY_A);

            ?>
            <form id="save_org" class="simple_form" action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                <input type="hidden" name="action" value="save_organization">
                <?php wp_nonce_field('save_organization', 'organization_nonce'); ?>
                <label for="organization_name"><?php _e('Organization Name:', 'textdomain'); ?></label>
                <input type="text" id="organization_name" name="organization_name" class="regular-text" required>
                <br>
                <label for="organization_address"><?php _e('Organization Type:', 'textdomain'); ?></label>
                <!-- <input type="text" id="organization_address" name="organization_address" class="regular-text" required> -->
                <select name="organization_address" id="organization_address" required> 
                    <option value="Blog">Blog</option>
                    <option value="Newspaper">Newspaper</option>
                    <option value="Online Publisher">Online Publisher</option>
                    <option value="Podcast">Podcast</option>
                    <option value="Radio">Radio</option>
                    <option value="Social Media">Social Media</option>
                    <option value="Television">Television</option>
                    <option value="Trade Publisher">Trade Publisher</option>
                    <option value="Other">Other</option>
                </select>
                <br>
                <label for="org_group"><?php _e('Organization Group:', 'textdomain'); ?></label>
                <!-- <select id="org_group" name="org_group" class="regular-text">
                    <option value=""><?php _e('Select a Group', 'textdomain'); ?></option> -->
                <div class="org_list_box" id="org_group" name="org_group">
                    <?php
                    /*foreach ($org_groups as $group) {
                        echo '<option value="' . esc_attr($group['id']) . '">' . esc_html($group['group_name']) . '</option>';
                    }*/
                    // Display checkboxes for each group
                    foreach ($org_groups as $group) {
                        echo '<label>';
                        echo '<input type="checkbox" name="org_groups[]" value="' . esc_attr($group['id']) . '"> ';
                        echo esc_html(wp_unslash($group['group_name']));
                        echo '</label>';
                    }
                    ?>
                </div>
                    
                <!-- </select> -->
                <br>
                <input class="btn btn-primary" type="submit" value="Save Organization" name="Save Organization">
            </form>
        </div>
        
        <div class="data_card-list active_tab_content" id="all_orgs">
            <!-- <div class="card-header card-header-primary">
                <div>
                    <?php 
                    /*if ($org_group_id) {
                        $group = $wpdb->get_row($wpdb->prepare("SELECT group_name FROM $table_name_groups WHERE id = %d", $org_group_id), ARRAY_A);
                        echo '<h4 class="card-title ">Directory of Media Organizations for ' . esc_html($group['group_name']) . '</h4>';
                    } else {
                        echo '<h4 class="card-title">Directory of Media Organizations</h4>';
                    }*/
                    ?>
                    
                    <p class="card-category">Find and edit media organizations and contacts</p>
                </div>
                <a class="btn btn-success" href="<?php echo esc_url(admin_url('admin.php?page=add_new_organization')); ?>"><?php _e('Add New', 'textdomain'); ?></a>
            </div> -->

            <?php if (empty($organizations)): ?>
                <p><?php _e('No organizations found.', 'textdomain'); ?></p>
            <?php else: ?>
                <table id="organizations-table" class="wp-list-table fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Name', 'textdomain'); ?></th>
                            <th><?php _e('Organization Type ', 'textdomain'); ?></th>
                            <th><?php _e('Organization Groups', 'textdomain'); ?></th>
                            <th><?php _e('Actions', 'textdomain'); ?></th>
                        </tr>
                    </thead>
                    <tbody class="test-body">
                        <?php foreach ($organizations as $org): ?>
                            <tr data-org_id="<?php echo esc_html($org['id']); ?>">
                                <td><?php echo esc_html($org['name']); ?></td>
                                <td><?php echo esc_html($org['address']); ?></td>
                                <td class="group_list_col">
    <?php
    // Only process if group_names exists and isn't empty
    if (!empty($org['group_names'])) {
        $groups = explode(', ', wp_unslash($org['group_names']));
        $valid_groups = [];
        
        foreach ($groups as $group) {
            // Skip if group doesn't contain the expected delimiter
            if (strpos($group, ':') === false) {
                continue;
            }
            
            // Safely split into max 2 parts
            $group_parts = explode(':', $group, 2);
            if (count($group_parts) !== 2) {
                continue;
            }
            
            list($group_name, $group_id) = $group_parts;
            
            // Only process if we have both name and ID
            if (!empty($group_name) && !empty($group_id)) {
                $group_link = esc_url(add_query_arg([
                    'page' => 'organization_page',
                    'org_group_id' => $group_id
                ], admin_url('admin.php')));
                
                $valid_groups[] = '<a href="' . $group_link . '">' . esc_html($group_name) . '</a>';
            }
        }
        
        // Only output if we have valid groups
        if (!empty($valid_groups)) {
            echo implode(', ', $valid_groups);
        }
    }
    ?>
</td>
                                <td>
                                    <a href="<?php echo esc_url(add_query_arg(array('page' => 'organization_page', 'action' => 'edit', 'org_id' => $org['id']), admin_url('admin.php'))); ?>">
                                        <?php _e('Edit', 'textdomain'); ?>
                                    </a> | 
                                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=delete_organization&org_id=' . $org['id']), 'delete_organization'); ?>">
                                        <?php _e('Delete', 'textdomain'); ?>
                                    </a> |
                                    <a href="<?php echo esc_url(add_query_arg(array('page' => 'contact_page', 'org_id' => $org['id']), admin_url('admin.php'))); ?>">
                                        <?php _e('View All Contacts', 'textdomain'); ?>
                                    </a>
                                    <!-- <a href="<?php //echo esc_url(add_query_arg(array('page' => 'organization_page', 'org_id' => $org['id'], 'org_group_id' => $org_group_id), admin_url('admin.php'))); ?>">
                                        <?php //_e('View Group', 'textdomain'); ?>
                                    </a> -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
?>
<?php
// Ensure that this file is being accessed through WordPress.
if (!defined('ABSPATH')) {
    exit;
}

function render_group_page() {
    global $wpdb;

    // Start output buffering to prevent any headers from being sent prematurely
    ob_start();

    // Define table names
    $table_name_groups = $wpdb->prefix . 'groups';
    $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';
    $table_name_org = $wpdb->prefix . 'organizations';
    $table_name_contact = $wpdb->prefix . 'contacts';
    $table_name_group_relationships = $wpdb->prefix . 'group_relationships';

    // Query to count total organizations
    $total_groups = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name_groups" );

    // Fetch all Org Groups with their types
    $org_groups = $wpdb->get_results("
        SELECT g.id, g.group_name, g.description, t.group_type 
        FROM $table_name_groups g
        LEFT JOIN $table_name_group_taxonomy t ON g.id = t.groups_id
    ", ARRAY_A);

    // Check if we're in "edit" mode
    $is_edit_mode = isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id']);
    $group_id = $is_edit_mode ? intval($_GET['id']) : 0;
    $group_name = '';
    $description = '';
    $group_type = 'organization'; // Default to 'organization'

    if ($is_edit_mode) {
        // Fetch the group data to populate the edit form
        $group = $wpdb->get_row($wpdb->prepare("
            SELECT g.group_name, g.description, t.group_type 
            FROM $table_name_groups g
            LEFT JOIN $table_name_group_taxonomy t ON g.id = t.groups_id
            WHERE g.id = %d
        ", $group_id), ARRAY_A);

        if ($group) {
            $group_name = $group['group_name'];
            $description = $group['description'];
            $group_type = $group['group_type'];
        }
    }
    // Output the HTML form and table
    ?>
    <div class="wrap">
        <h2>Groups List</h2>
        <?php
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            $editing_mode = "editing_on";
        }
        ?>
        <div class="press_release_tabs <?php echo $editing_mode; ?>"> 
            <a href="#all_group" class="btn active_tab"> Groups <span><?php echo esc_html($total_groups); ?></span></a>
            <a href="#add_new_group" class="btn add_btn"> Add New Group &nbsp; <i class="fa fa-plus"></i> </a>
        </div>

        <div class="data-card_title" data-tab_name="all_group">
            <?php 
            if ($org_group_id) {
                $group = $wpdb->get_row($wpdb->prepare("SELECT group_name FROM $table_name_groups WHERE id = %d", $org_group_id), ARRAY_A);
                echo '<h4 class="card-title ">Directory of Media Organizations for ' . esc_html($group['group_name']) . '</h4>';
            } else {
                echo '<h4 class="card-title">Directory of Media Organizations</h4>';
            }
            ?>
        </div>

        <div class="data-card_title" data-tab_name="add_new_group" style="display:none;">
            <h4 class="card-title ">Add New Group</h4>
        </div>

        <div class="data_card-list" id="add_new_group">
            <!-- <div class="card-header card-header-primary">
                <div>
                    <h4 class="card-title">Add New Group</h4>
                    <p class="card-category">Create group for contacts and organizations</p>
                </div>
                
            </div> -->

        <?php /* if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e('Group added successfully!', 'textdomain'); ?></p>
            </div>
        <?php endif; */ ?>

        <form id="save_group" class="simple_form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="save_group">
            <!-- <table class="form-table">
                <tr>
                    <th scope="row"></th>
                    <td> -->
                        <label for="group_name"><?php _e('Group Name', 'textdomain'); ?></label>
                        <input type="text" name="group_name" id="group_name" value="<?php echo esc_attr($group_name); ?>" class="regular-text" required />
                        <br>
                    <!-- </td>
                </tr>
                <tr>
                    <th scope="row"></th>
                    <td> -->
                        <label for="description"><?php _e('Description', 'textdomain'); ?></label>
                        <textarea name="description" id="description" class="large-text" rows="4"><?php echo esc_textarea($description); ?></textarea>
                        <br>
                    <!-- </td>
                </tr>
                <tr>
                    <th scope="row"></th>
                    <td> -->
                        <label for="group_type"><?php _e('Group Type', 'textdomain'); ?></label>
                        <select name="group_type" id="group_type">
                            <option value="organization" <?php selected($group_type, 'organization'); ?>><?php _e('Organization', 'textdomain'); ?></option>
                            <option value="contact" <?php selected($group_type, 'contact'); ?>><?php _e('Contact', 'textdomain'); ?></option>
                        </select>
                        <br>
                    <!-- </td>
                </tr>
            </table> -->
            <!-- <p class="submit"> -->
                <input type="submit" name="submit" id="submit" class="btn btn-primary" value="<?php _e('Save Group', 'textdomain'); ?>">
            <!-- </p> -->
        </form>
        </div>

        <div class="data_card-list active_tab_content" id="all_group">
           <!--  <div class="card-header card-header-primary">
                <div>
                    <h4 class="card-title">
                        <?php if ($is_edit_mode) {
                            echo "Editing " . esc_attr($group_name);
                        } else {
                            echo "Groups List";
                        }?></h4>
                    <p class="card-category">Groups related to contacts and organizations</p>
                </div>
                <a class="btn btn-success" href="<?php //echo esc_url(admin_url('admin.php?page=add_group_page')); ?>">
                    <?php // _e('Add New', 'textdomain'); ?>
                </a>
            </div> -->
                <?php if ($is_edit_mode) : ?>
                <form id="update_group" class="simple_form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <h3>Edit Group <?php echo esc_attr(wp_unslash($group_name)); ?></h3>
                    <input type="hidden" name="action" value="update_group">
                    <input type="hidden" name="group_id" value="<?php echo esc_attr($group_id); ?>">
                    <!-- <table class="form-table">
                        <tr> -->
                            <!-- <th scope="row"></th>
                            <td> -->
                                <label for="group_name"><?php _e('Group Name', 'textdomain'); ?></label>
                                <input type="text" name="group_name" id="group_name" class="regular-text" value="<?php echo esc_attr(wp_unslash($group_name)); ?>" class="regular-text" required />
                                <br>
                           <!--  </td>
                        </tr> -->
                        <!-- <tr>
                            <th scope="row"></th>
                            <td> -->
                                <label for="description"><?php _e('Description', 'textdomain'); ?></label>
                                <textarea name="description" id="description" class="large-text" rows="4"><?php echo esc_textarea($description); ?></textarea>
                                <br>
                           <!--  </td>
                        </tr> -->
                        <!-- <tr>
                            <th scope="row"></th>
                            <td> -->
                                <label for="group_type"><?php _e('Group Type', 'textdomain'); ?></label>
                                <select name="group_type" id="group_type" class="regular-text">
                                    <option value="organization" <?php selected($group_type, 'organization'); ?>><?php _e('Organization', 'textdomain'); ?></option>
                                    <option value="contact" <?php selected($group_type, 'contact'); ?>><?php _e('Contact', 'textdomain'); ?></option>
                                </select>
                                <br>
                            <!-- </td> -->
                        <!-- </tr>
                    </table> -->
                    <div class="btn-container">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=group_page')); ?>" type="button" class="btn btn-danger text-center">Cancel</a>
                        <input type="submit" name="submit" id="submit" class="btn btn-primary" value="<?php _e('Update Group', 'textdomain'); ?>">
                    </div>
                </form>
            <?php endif; ?>
            <?php if (!$is_edit_mode) : ?>
                <?php if (!empty($org_groups)) : ?>
                <table id="group_list" class="fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Group Name', 'textdomain'); ?></th>
                            <th><?php _e('Description', 'textdomain'); ?></th>
                            <th><?php _e('Type', 'textdomain'); ?></th>
                            <th><?php _e('Organizations / Contacts', 'textdomain'); ?></th>
                            <th><?php _e('Actions', 'textdomain'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($org_groups as $group) : ?>
                            <tr>
                                <td data-group_id="<?php echo esc_html($group['id']); ?>"><?php 
                                            //echo esc_html($group['group_name']); 
                                        
                                    // Determine the correct URL based on the group type
                                    if ($group['group_type'] === 'organization') {
                                        $group_url = esc_url(add_query_arg(array(
                                            'page' => 'organization_page',
                                            'org_group_id' => $group['id']
                                        ), admin_url('admin.php')));
                                    } elseif ($group['group_type'] === 'contact') {
                                        $group_url = esc_url(add_query_arg(array(
                                            'page' => 'contact_page',
                                            'contact_group_id' => $group['id']
                                        ), admin_url('admin.php')));
                                    } else {
                                        $group_url = '#'; // Default if group_type is unknown
                                    }

                                    // Display group name as a link
                                    echo '<a href="' . $group_url . '">' . esc_html(wp_unslash($group['group_name'])) . '</a>';
                                    
                                ?></td>
                                <td><?php echo esc_html($group['description']); ?></td>
                                <td><?php echo esc_html($group['group_type']); ?></td>
                                <td>
                                    <?php
                                    if ($group['group_type'] === 'organization') {
                                        // Fetch organizations associated with this group
                                        $organizations = $wpdb->get_results($wpdb->prepare("
                                            SELECT o.name 
                                            FROM $table_name_org o
                                            INNER JOIN $table_name_group_relationships gr ON o.id = gr.object_id
                                            WHERE gr.group_taxonomy_id = (
                                                SELECT id FROM $table_name_group_taxonomy WHERE groups_id = %d
                                            )", $group['id']
                                        ), ARRAY_A);

                                        if (!empty($organizations)) {
                                            foreach ($organizations as $org) {
                                                echo esc_html($org['name']) . '<br>';
                                            }
                                        } else {
                                            _e('No organizations found.', 'textdomain');
                                        }
                                    } else if ($group['group_type'] === 'contact') {
                                        // Fetch contacts associated with this group
                                        $contacts = $wpdb->get_results($wpdb->prepare("
                                            SELECT c.name 
                                            FROM $table_name_contact c
                                            INNER JOIN $table_name_group_relationships gr ON c.id = gr.object_id
                                            WHERE gr.group_taxonomy_id = (
                                                SELECT id FROM $table_name_group_taxonomy WHERE groups_id = %d
                                            )", $group['id']
                                        ), ARRAY_A);

                                        if (!empty($contacts)) {
                                            foreach ($contacts as $contact) {
                                                echo esc_html($contact['name']) . '<br>';
                                            }
                                        } else {
                                            _e('No contacts found.', 'textdomain');
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(add_query_arg(array('page' => 'group_page', 'action' => 'edit', 'id' => $group['id']))); ?>">
                                        <?php _e('Edit', 'textdomain'); ?>
                                    </a> |
                                    <!-- <a href="<?php echo esc_url(add_query_arg(array('page' => 'group_page', 'action' => 'delete', 'id' => $group['id']))); ?>" onclick="return confirm('<?php _e('Are you sure you want to delete this group?', 'textdomain'); ?>');">
                                        <?php _e('Delete', 'textdomain'); ?>
                                    </a> -->
                                    <a href="<?php echo esc_url(add_query_arg(array('action' => 'delete_group', 'id' => $group['id']), admin_url('admin-post.php'))); ?>" onclick="return confirm('<?php _e('Are you sure you want to delete this group?', 'textdomain'); ?>');"><?php _e('Delete', 'textdomain'); ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6"></td>
                        </tr>
                    </tbody>
                </table>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (empty($org_groups)) : ?>
            <?php _e('No groups found.', 'textdomain'); ?>
            <?php endif; ?>
        </div>

        

    </div>
    <?php

    // Flush the output buffer and send output to the browser
    ob_end_flush();
}

?>
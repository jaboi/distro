<?php
// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function render_add_new_organization_page() {
    global $wpdb; // Make $wpdb accessible

    $table_name_groups = $wpdb->prefix . 'groups';
    $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';

    echo '<div class="wrap"><div class="data_card-list">';
    echo '<div class="card-header card-header-primary">';
    echo '<div><h4 class="card-title ">Add New Organization</h4>';
    echo '<p class="card-category">Some awesome text here</p></div>';
    echo '<a class="btn btn-success" href="' . esc_url(admin_url('admin.php?page=organization_page')) . '">' . __('View List', 'textdomain') . '</a>';
    echo '</div>';

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
        <label for="organization_address"><?php _e('Org Type:', 'textdomain'); ?></label>
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
            <option value="Other"></option>
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
    <?php

    echo '</div>';
    echo '</div>';
    echo '</div>';
}
?>

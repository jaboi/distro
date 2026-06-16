<?php
// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function render_organization_edit_form($org_id) {
    global $wpdb;

    // Fetch the organization details
    $table_name_org = $wpdb->prefix . 'organizations';
    $organization = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name_org WHERE id = %d", $org_id), ARRAY_A);

    if (empty($organization)) {
        echo '<div class="error"><p>' . __('Organization not found.', 'textdomain') . '</p></div>';
        return;
    }

    // Define table names
    $table_name_groups = $wpdb->prefix . 'groups';
    $table_name_group_relationships = $wpdb->prefix . 'group_relationships';
    $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';

    // Query to get all groups with type 'organization'
    $query = $wpdb->prepare(
        "SELECT g.id, g.group_name 
         FROM $table_name_groups g
         INNER JOIN $table_name_group_taxonomy t ON g.id = t.groups_id
         WHERE t.group_type = %s",
        'organization'
    );
    $org_groups = $wpdb->get_results($query, ARRAY_A);

    // Fetch the groups this organization is currently associated with
    $current_group_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT tax.groups_id 
         FROM $table_name_group_relationships rel
         INNER JOIN $table_name_group_taxonomy tax ON rel.group_taxonomy_id = tax.id
         WHERE rel.object_id = %d",
        $org_id
    ));

    ?>
    <!-- <div class="wrap"> -->
        <div class="data_card-list active_tab_content">
            <!-- <div class="card-header card-header-primary">
                <div><h4 class="card-title">Edit Organization</h4>
                <p class="card-category">Edit Organization</p></div>
            </div> -->

            <form class="simple_form" action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                <h3>Edit Organization <?php echo esc_attr($organization['name']); ?></h3>
                <input type="hidden" name="action" value="edit_organization">
                <?php wp_nonce_field('edit_organization', 'organization_nonce'); ?>
                <input type="hidden" name="org_id" value="<?php echo esc_attr($org_id); ?>">

                <!-- Organization Name -->
                <label for="organization_name"><?php _e('Organization Name:', 'textdomain'); ?></label>
                <input type="text" id="organization_name" name="organization_name" class="regular-text" value="<?php echo esc_attr($organization['name']); ?>" required>
                <br>

                <!-- Organization Address -->
                <label for="organization_address"><?php _e('Organization Type:', 'textdomain'); ?></label>
                <!-- <input type="text" id="organization_address" name="organization_address" class="regular-text" value="<?php echo esc_attr($organization['address']); ?>" required> -->
                <select name="organization_address" id="organization_address"> 
                    <option value="Blog" <?php echo ($organization['address'] == "Blog") ? "selected" : ""; ?>>Blog</option>
                    <option value="Newspaper" <?php echo ($organization['address'] == "Newspaper") ? "selected" : ""; ?>>Newspaper</option>
                    <option value="Online Publisher" <?php echo ($organization['address'] == "Online Publisher") ? "selected" : ""; ?>>Online Publisher</option>
                    <option value="Podcast" <?php echo ($organization['address'] == "Podcast") ? "selected" : ""; ?>>Podcast</option>
                    <option value="Radio" <?php echo ($organization['address'] == "Radio") ? "selected" : ""; ?>>Radio</option>
                    <option value="Social Media" <?php echo ($organization['address'] == "Social Media") ? "selected" : ""; ?>>Social Media</option>
                    <option value="Television" <?php echo ($organization['address'] == "Television") ? "selected" : ""; ?>>Television</option>
                    <option value="Trade Publisher" <?php echo ($organization['address'] == "Trade Publisher") ? "selected" : ""; ?>>Trade Publisher</option>
                    <option value="Other" <?php echo ($organization['address'] == "Other") ? "selected" : ""; ?>>Other</option>
                </select>
                <br>

                <!-- Organization Group Checkboxes -->
                <label for="org_group"><?php _e('Organization Groups:', 'textdomain'); ?></label><br>
                <div class="org_list_box" id="org_group" name="org_group">
                    <?php
                    foreach ($org_groups as $group) {
                        $checked = in_array($group['id'], $current_group_ids) ? 'checked' : '';
                        echo '<label><input type="checkbox" name="org_group[]" value="' . esc_attr($group['id']) . '" ' . $checked . '> ' . esc_html($group['group_name']) . '</label><br>';
                    }
                    ?>
                </div>
                <br>
                <div class="btn-container">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=organization_page')); ?>" type="button" class="btn btn-danger text-center">Cancel</a>
                    <!-- Submit Button -->
                    <input type="submit" name="submit" id="submit" class="btn btn-primary" value="Update Organization">
                    <?php //submit_button(__('Update Organization', 'textdomain')); ?>
                </div>
            </form>
        </div>
    <!-- </div> -->
    <?php
}
?>
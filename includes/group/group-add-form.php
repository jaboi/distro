<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function render_add_group_page() {
    global $wpdb;

    // Initialize variables
    $group_name = '';
    $description = '';
    $group_type = 'organization'; // Default to 'organization'

    // Output the HTML form
    ?>
    <div class="wrap">
        <div class="data_card-list">
            <div class="card-header card-header-primary">
                <div>
                    <h4 class="card-title">Add New Group</h4>
                    <p class="card-category">Create group for contacts and organizations</p>
                </div>
                <a class="btn btn-success" href="<?php echo esc_url(admin_url('admin.php?page=add_group_page')); ?>">
                    <?php _e('Add New', 'textdomain'); ?>
                </a>
            </div>

        <?php /* if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e('Group added successfully!', 'textdomain'); ?></p>
            </div>
        <?php endif; */ ?>

        <form id="save_group" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="save_group">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="group_name"><?php _e('Group Name', 'textdomain'); ?></label></th>
                    <td><input type="text" name="group_name" id="group_name" value="<?php echo esc_attr($group_name); ?>" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="description"><?php _e('Description', 'textdomain'); ?></label></th>
                    <td><textarea name="description" id="description" class="large-text" rows="4"><?php echo esc_textarea($description); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="group_type"><?php _e('Group Type', 'textdomain'); ?></label></th>
                    <td>
                        <select name="group_type" id="group_type">
                            <option value="organization" <?php selected($group_type, 'organization'); ?>><?php _e('Organization', 'textdomain'); ?></option>
                            <option value="contact" <?php selected($group_type, 'contact'); ?>><?php _e('Contact', 'textdomain'); ?></option>
                        </select>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Group', 'textdomain'); ?>">
            </p>
        </form>
        </div>
    </div>
    <?php
}
?>